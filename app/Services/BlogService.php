<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use Config\Services;

/**
 * BlogService
 *
 * Reads and queries the blog dataset stored in public/data/blog.json.
 */
class BlogService
{
    private const CACHE_KEY = 'blog_dataset';
    private const CACHE_TTL = 300; // 5 minutes

    private string $dataFile;
    private CacheInterface $cache;

    public function __construct(?string $dataFile = null)
    {
        $this->dataFile = $dataFile ?? FCPATH . 'data' . DIRECTORY_SEPARATOR . 'blog.json';
        $this->cache    = Services::cache();
    }

    /**
     * @return array{posts: array<int, array<string, mixed>>, categories: array<int, string>}
     */
    public function dataset(): array
    {
        $cached = $this->cache->get(self::CACHE_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $data = [];
        if (is_file($this->dataFile)) {
            $decoded = json_decode((string) file_get_contents($this->dataFile), true);
            if (is_array($decoded)) {
                $data = $decoded;
            }
        }

        $data['posts']      ??= [];
        $data['categories'] ??= [];

        $this->cache->save(self::CACHE_KEY, $data, self::CACHE_TTL);

        return $data;
    }

    /**
     * All posts, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        $posts = $this->dataset()['posts'];
        usort($posts, static fn ($a, $b) => strcmp((string) ($b['date'] ?? ''), (string) ($a['date'] ?? '')));

        return $posts;
    }

    /**
     * @return array<int, string>
     */
    public function categories(): array
    {
        return $this->dataset()['categories'];
    }

    /**
     * The single featured/hero post (falls back to newest).
     *
     * @return array<string, mixed>|null
     */
    public function featured(): ?array
    {
        foreach ($this->all() as $post) {
            if (! empty($post['featured'])) {
                return $post;
            }
        }

        return $this->all()[0] ?? null;
    }

    /**
     * Recent posts, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function recent(int $limit = 3): array
    {
        return array_slice($this->all(), 0, $limit);
    }

    /**
     * Most-viewed posts for the sidebar.
     *
     * @return array<int, array<string, mixed>>
     */
    public function popular(int $limit = 4): array
    {
        $posts = $this->all();
        usort($posts, static fn ($a, $b) => (int) ($b['views'] ?? 0) <=> (int) ($a['views'] ?? 0));

        return array_slice($posts, 0, $limit);
    }

    /**
     * Posts in a category. Pass null/'' or 'All' for everything.
     *
     * @return array<int, array<string, mixed>>
     */
    public function byCategory(?string $category): array
    {
        if ($category === null || $category === '' || strtolower($category) === 'all') {
            return $this->all();
        }
        $cat = mb_strtolower($category);

        return array_values(array_filter(
            $this->all(),
            static fn ($p) => mb_strtolower((string) ($p['category'] ?? '')) === $cat
        ));
    }

    /**
     * Find a post by slug.
     *
     * @return array<string, mixed>|null
     */
    public function findBySlug(string $slug): ?array
    {
        foreach ($this->all() as $post) {
            if (($post['slug'] ?? null) === $slug) {
                return $post;
            }
        }

        return null;
    }

    /**
     * Related posts: same category first, then recent, excluding the given slug.
     *
     * @return array<int, array<string, mixed>>
     */
    public function related(string $slug, int $limit = 3): array
    {
        $current = $this->findBySlug($slug);
        if ($current === null) {
            return $this->recent($limit);
        }

        $pool = array_values(array_filter($this->all(), static fn ($p) => ($p['slug'] ?? null) !== $slug));
        usort($pool, static function ($a, $b) use ($current) {
            $sameA = (($a['category'] ?? null) === ($current['category'] ?? null)) ? 1 : 0;
            $sameB = (($b['category'] ?? null) === ($current['category'] ?? null)) ? 1 : 0;

            return $sameB <=> $sameA;
        });

        return array_slice($pool, 0, $limit);
    }

    /**
     * Tag cloud aggregated across all posts.
     *
     * @return array<int, string>
     */
    public function tags(): array
    {
        $tags = [];
        foreach ($this->all() as $post) {
            foreach ((array) ($post['tags'] ?? []) as $tag) {
                $tags[$tag] = true;
            }
        }

        return array_keys($tags);
    }
}
