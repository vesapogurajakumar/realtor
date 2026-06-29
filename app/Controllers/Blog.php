<?php

declare(strict_types=1);

namespace App\Controllers;

use Config\Services;

class Blog extends BaseController
{
    /**
     * Blog index — featured post, category-filtered grid, sidebar.
     */
    public function index(): string
    {
        $blog     = Services::blog();
        $category = (string) ($this->request->getGet('category') ?? '');
        $search   = trim((string) ($this->request->getGet('q') ?? ''));

        $posts = $blog->byCategory($category);
        if ($search !== '') {
            $needle = mb_strtolower($search);
            $posts  = array_values(array_filter($posts, static function ($p) use ($needle) {
                return str_contains(mb_strtolower($p['title'] . ' ' . $p['excerpt'] . ' ' . implode(' ', $p['tags'] ?? [])), $needle);
            }));
        }

        // Featured post only headlines the unfiltered view.
        $featured = ($category === '' && $search === '') ? $blog->featured() : null;
        $grid     = $featured ? array_values(array_filter($posts, static fn ($p) => ($p['slug'] ?? null) !== ($featured['slug'] ?? null))) : $posts;

        return view('blog/index', [
            'title'           => ($category !== '' ? $category . ' | ' : '') . 'Vesta Blog | Real Estate Insights',
            'metaDescription' => 'Market trends, buying tips, selling guides and neighborhood insights from the Vesta real estate team.',
            'activeNav'       => 'blog',
            'featured'        => $featured,
            'posts'           => $grid,
            'categories'      => $blog->categories(),
            'activeCategory'  => $category,
            'search'          => $search,
            'popular'         => $blog->popular(4),
            'tags'            => $blog->tags(),
        ]);
    }

    /**
     * Single blog post.
     */
    public function post(string $slug)
    {
        $blog = Services::blog();
        $post = $blog->findBySlug($slug);

        if ($post === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Post not found');
        }

        return view('blog/post', [
            'title'           => $post['title'] . ' | Vesta Blog',
            'metaDescription' => reading_excerpt((string) ($post['excerpt'] ?? ''), 160),
            'ogImage'         => $post['featured_image'] ?? null,
            'activeNav'       => 'blog',
            'post'            => $post,
            'related'         => $blog->related($slug, 3),
            'comments'        => $this->approvedComments($slug),
            'jsonld'          => $this->articleJsonLd($post),
        ]);
    }

    /**
     * Comment submission (POST, AJAX) — stored pending moderation.
     */
    public function comment()
    {
        if (trim((string) $this->request->getPost('company')) !== '') {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Thanks!']);
        }

        $rules = [
            'post_slug' => 'required|max_length[160]',
            'name'      => 'required|min_length[2]|max_length[120]',
            'email'     => 'required|valid_email|max_length[180]',
            'comment'   => 'required|min_length[3]|max_length[2000]',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Please complete all fields correctly.',
                'errors'  => $this->validator->getErrors(),
                'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
            ]);
        }

        $payload = [
            'post_slug'  => (string) $this->request->getPost('post_slug'),
            'name'       => (string) $this->request->getPost('name'),
            'email'      => (string) $this->request->getPost('email'),
            'comment'    => (string) $this->request->getPost('comment'),
            'is_approved' => 0,
            'ip'         => $this->request->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // DB attempt (no-op until model/migration & DB exist).
        try {
            if (class_exists(\App\Models\BlogCommentModel::class)) {
                (new \App\Models\BlogCommentModel())->insert($payload, false);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Comment DB persist failed: {m}', ['m' => $e->getMessage()]);
        }

        // Writable backup.
        try {
            $dir = WRITEPATH . 'comments';
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            file_put_contents($dir . DIRECTORY_SEPARATOR . 'comments.jsonl', json_encode($payload, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (\Throwable $e) {
            log_message('error', 'Comment backup failed: {m}', ['m' => $e->getMessage()]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Thank you! Your comment has been submitted and will appear once approved.',
            'csrf'    => ['name' => csrf_token(), 'hash' => csrf_hash()],
        ]);
    }

    /**
     * Approved comments for a post (DB if available, else writable backup).
     *
     * @return array<int, array<string, mixed>>
     */
    private function approvedComments(string $slug): array
    {
        try {
            if (class_exists(\App\Models\BlogCommentModel::class)) {
                return (new \App\Models\BlogCommentModel())
                    ->where('post_slug', $slug)->where('is_approved', 1)
                    ->orderBy('created_at', 'DESC')->findAll();
            }
        } catch (\Throwable $e) {
            // fall through to file backup
        }

        $out  = [];
        $file = WRITEPATH . 'comments' . DIRECTORY_SEPARATOR . 'comments.jsonl';
        if (is_file($file)) {
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $row = json_decode($line, true);
                if (is_array($row) && ($row['post_slug'] ?? '') === $slug && (int) ($row['is_approved'] ?? 0) === 1) {
                    $out[] = $row;
                }
            }
        }

        return $out;
    }

    private function articleJsonLd(array $post): string
    {
        return json_encode([
            '@context'      => 'https://schema.org',
            '@type'         => 'BlogPosting',
            'headline'      => $post['title'] ?? '',
            'description'   => $post['excerpt'] ?? '',
            'image'         => $post['featured_image'] ?? '',
            'datePublished' => $post['date'] ?? '',
            'author'        => ['@type' => 'Person', 'name' => $post['author']['name'] ?? 'Vesta'],
            'publisher'     => ['@type' => 'Organization', 'name' => 'Vesta Real Estate'],
            'articleSection' => $post['category'] ?? '',
            'url'           => base_url('public/blog/' . ($post['slug'] ?? '')),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
