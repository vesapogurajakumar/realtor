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

        return redirect()->to(base_url('public/admin/login'))->with('error', 'Incorrect password.');
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

    // ----------------------------------------------------------------- Leads

    public function leads(): string
    {
        $perPage = 15;
        $page    = max(1, (int) $this->request->getGet('page'));
        $rows    = [];
        $total   = 0;

        try {
            $model = new LeadModel();
            $total = $model->countAllResults();
            $rows  = $model->orderBy('id', 'DESC')->findAll($perPage, ($page - 1) * $perPage);
        } catch (\Throwable $e) {
            session()->setFlashdata('error', 'Database not connected.');
        }

        $pages = (int) max(1, ceil($total / $perPage));

        return view('admin/leads', [
            'title'    => 'Leads | Vesta Admin',
            'active'   => 'leads',
            'rows'     => $rows,
            'total'    => $total,
            'page'     => min($page, $pages),
            'pages'    => $pages,
            'pageUrl'  => static fn (int $p) => base_url('public/admin/leads?page=' . $p),
        ]);
    }

    /**
     * Stream all leads as a CSV download.
     */
    public function exportLeads()
    {
        $rows = [];
        try {
            $rows = (new LeadModel())->orderBy('id', 'DESC')->findAll();
        } catch (\Throwable $e) {
            return redirect()->to(base_url('public/admin/leads'))->with('error', 'Database not connected.');
        }

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Interest', 'Message', 'Preferred Time', 'Source', 'IP', 'Created At']);
        foreach ($rows as $l) {
            fputcsv($handle, [
                $l['id'] ?? '', $l['name'] ?? '', $l['email'] ?? '', $l['phone'] ?? '',
                $l['interest'] ?? '', $l['message'] ?? '', $l['preferred_time'] ?? '',
                $l['source'] ?? '', $l['ip'] ?? '', $l['created_at'] ?? '',
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="leads-' . date('Y-m-d') . '.csv"')
            ->setBody("\xEF\xBB\xBF" . $csv); // BOM for Excel UTF-8
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
            'title'         => 'required|min_length[3]|max_length[180]',
            'price'         => 'required|numeric|greater_than[0]',
            'city'          => 'required|max_length[90]',
            'beds'          => 'permit_empty|is_natural',
            'baths'         => 'permit_empty|is_natural',
            'sqft'          => 'permit_empty|is_natural',
            'garage'        => 'permit_empty|is_natural',
            'hoa'           => 'permit_empty|is_natural',
            'year_built'    => 'permit_empty|numeric|exact_length[4]',
            'walk_score'    => 'permit_empty|is_natural|less_than_equal_to[100]',
            'transit_score' => 'permit_empty|is_natural|less_than_equal_to[100]',
            'lat'           => 'permit_empty|decimal',
            'lng'           => 'permit_empty|decimal',
            'agent_email'   => 'permit_empty|valid_email|max_length[180]',
            'agent_phone'   => 'permit_empty|numeric|exact_length[10]',
        ];
        $messages = [
            'agent_phone' => ['exact_length' => 'Agent mobile number must be exactly 10 digits.', 'numeric' => 'Agent mobile must be digits only.'],
            'price'       => ['greater_than' => 'Price must be greater than 0.'],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->to(base_url('public/admin/listings/new'))->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $input = $this->request->getPost();

            // Merge uploaded files (saved to disk) with any pasted image URLs.
            $pasted = array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) ($input['images'] ?? '')) ?: [])));
            $input['images'] = array_merge($this->handleImageUploads('images_files'), $pasted);

            $code = (new PropertyModel())->createFromForm($input);
            if ($code === false) {
                return redirect()->to(base_url('public/admin/listings/new'))->withInput()->with('error', 'Could not save the listing.');
            }
            Services::property()->clearCache();

            return redirect()->to(base_url('public/admin/listings'))
                ->with('message', 'Listing published! It is now live on the site.');
        } catch (\Throwable $e) {
            log_message('error', 'Listing save failed: {m}', ['m' => $e->getMessage()]);

            return redirect()->to(base_url('public/admin/listings/new'))->withInput()->with('error', 'Database error. Make sure migrations have run.');
        }
    }

    public function deleteListing(int $id)
    {
        try {
            (new PropertyModel())->delete($id);
            Services::property()->clearCache();

            return redirect()->to(base_url('public/admin/listings'))->with('message', 'Listing deleted.');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('public/admin/listings'))->with('error', 'Could not delete listing.');
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

            return redirect()->to(base_url('public/admin/comments'))->with('message', 'Comment deleted.');
        } catch (\Throwable $e) {
            return redirect()->to(base_url('public/admin/comments'))->with('error', 'Could not delete comment.');
        }
    }

    private function setCommentApproval(int $id, int $approved, string $msg)
    {
        try {
            (new BlogCommentModel())->update($id, ['is_approved' => $approved]);

            return redirect()->to(base_url('public/admin/comments'))->with('message', $msg);
        } catch (\Throwable $e) {
            return redirect()->to(base_url('public/admin/comments'))->with('error', 'Could not update comment.');
        }
    }

    /**
     * Save uploaded images to public/assets/uploads, downscale + compress large
     * ones, and return their public URLs. Invalid/oversized files are skipped.
     *
     * @return array<int, string>
     */
    private function handleImageUploads(string $field): array
    {
        $paths   = [];
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $files   = $this->request->getFileMultiple($field);

        if (empty($files)) {
            return $paths;
        }

        $dir = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'uploads';
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        foreach ($files as $file) {
            if (! $file->isValid() || $file->hasMoved()) {
                continue;
            }
            if (! in_array($file->getMimeType(), $allowed, true)) {
                continue; // not an allowed image type
            }
            if ($file->getSizeByUnit('mb') > 8) {
                continue; // too large
            }

            $name = $file->getRandomName();
            $file->move($dir, $name);
            $full = $dir . DIRECTORY_SEPARATOR . $name;

            // Downscale (cap width at 1600px) + compress — skipped gracefully if GD is unavailable.
            try {
                $info = @getimagesize($full);
                if ($info && $info[0] > 1600) {
                    Services::image()
                        ->withFile($full)
                        ->resize(1600, 1600, true, 'width')
                        ->save($full, 82);
                }
            } catch (\Throwable $e) {
                log_message('warning', 'Image resize skipped: {m}', ['m' => $e->getMessage()]);
            }

            $paths[] = base_url('public/assets/uploads/' . $name);
        }

        return $paths;
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
