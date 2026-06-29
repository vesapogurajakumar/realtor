<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BlogCommentModel;
use App\Models\LeadModel;
use App\Models\PropertyModel;
use App\Models\SubscriberModel;
use Config\Services;

class Admin extends BaseController
{
    /** Default admin password if ADMIN_PASSWORD is not set in .env. */
    private const DEFAULT_PASSWORD = 'vesta-admin';

    // ---------------------------------------------------------------- Auth

    public function login()
    {
        if (session()->get('is_admin')) {
            return redirect()->to(base_url('public/admin'));
        }

        return view('admin/login', ['title' => 'Admin Login | Vesta']);
    }

    public function attemptLogin()
    {
        $password = (string) $this->request->getPost('password');
        $expected = env('ADMIN_PASSWORD', self::DEFAULT_PASSWORD);

        if (hash_equals((string) $expected, $password)) {
            session()->set('is_admin', true);

            return redirect()->to(base_url('public/admin'));
        }

        return redirect()->back()->with('error', 'Incorrect password.');
    }

    public function logout()
    {
        session()->remove('is_admin');

        return redirect()->to(base_url('public/admin/login'))->with('message', 'Signed out.');
    }

    // ------------------------------------------------------------ Dashboard

    public function dashboard(): string
    {
        return view('admin/dashboard', [
            'title'       => 'Dashboard | Vesta Admin',
            'active'      => 'dashboard',
            'stats'       => [
                'listings'    => $this->safeCount(PropertyModel::class),
                'leads'       => $this->safeCount(LeadModel::class),
                'subscribers' => $this->safeCount(SubscriberModel::class),
                'comments'    => $this->safeCount(BlogCommentModel::class),
            ],
            'leads'       => $this->safeRecent(LeadModel::class),
            'subscribers' => $this->safeRecent(SubscriberModel::class),
            'comments'    => $this->safeRecent(BlogCommentModel::class),
        ]);
    }

    // ------------------------------------------------------------- Listings

    public function listings(): string
    {
        $rows = [];
        try {
            $rows = (new PropertyModel())->orderBy('id', 'DESC')->findAll();
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Database not connected. Run migrations first.');
        }

        return view('admin/listings', [
            'title'  => 'Listings | Vesta Admin',
            'active' => 'listings',
            'rows'   => $rows,
        ]);
    }

    public function createListing(): string
    {
        return view('admin/listing_form', [
            'title'  => 'Add Listing | Vesta Admin',
            'active' => 'listings',
        ]);
    }

    public function storeListing()
    {
        $rules = [
            'title' => 'required|min_length[3]|max_length[180]',
            'price' => 'required|numeric',
            'city'  => 'required|max_length[90]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $code = (new PropertyModel())->createFromForm($this->request->getPost());
            if ($code === false) {
                return redirect()->back()->withInput()->with('error', 'Could not save the listing.');
            }
            Services::property()->clearCache();

            return redirect()->to(base_url('public/admin/listings'))
                ->with('message', 'Listing published! It is now live on the site.');
        } catch (\Throwable $e) {
            log_message('error', 'Listing save failed: {m}', ['m' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Database error. Make sure migrations have run.');
        }
    }

    public function deleteListing(int $id)
    {
        try {
            (new PropertyModel())->delete($id);
            Services::property()->clearCache();

            return redirect()->to(base_url('public/admin/listings'))->with('message', 'Listing deleted.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Could not delete listing.');
        }
    }

    // ------------------------------------------------------------- Comments

    public function comments(): string
    {
        $rows = [];
        try {
            $rows = (new BlogCommentModel())->orderBy('id', 'DESC')->findAll();
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Database not connected.');
        }

        return view('admin/comments', [
            'title'  => 'Comments | Vesta Admin',
            'active' => 'comments',
            'rows'   => $rows,
        ]);
    }

    public function approveComment(int $id)
    {
        return $this->setCommentApproval($id, 1, 'Comment approved — it is now visible on the post.');
    }

    public function unapproveComment(int $id)
    {
        return $this->setCommentApproval($id, 0, 'Comment hidden.');
    }

    public function deleteComment(int $id)
    {
        try {
            (new BlogCommentModel())->delete($id);

            return redirect()->back()->with('message', 'Comment deleted.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Could not delete comment.');
        }
    }

    private function setCommentApproval(int $id, int $approved, string $msg)
    {
        try {
            (new BlogCommentModel())->update($id, ['is_approved' => $approved]);

            return redirect()->back()->with('message', $msg);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Could not update comment.');
        }
    }

    // -------------------------------------------------------------- Helpers

    /** @param class-string $model */
    private function safeCount(string $model): int
    {
        try {
            return (new $model())->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    /**
     * @param class-string $model
     *
     * @return array<int, array<string, mixed>>
     */
    private function safeRecent(string $model, int $limit = 8): array
    {
        try {
            return (new $model())->orderBy('id', 'DESC')->findAll($limit);
        } catch (\Throwable $e) {
            return [];
        }
    }
}
