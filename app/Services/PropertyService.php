<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use Config\Services;

/**
 * PropertyService
 *
 * Reads, filters, sorts and caches the property dataset stored in
 * public/data/properties.json. All public methods operate on plain arrays
 * so views and controllers stay framework-agnostic.
 */
class PropertyService
{
    private const CACHE_KEY = 'properties_dataset';
    private const CACHE_TTL = 300; // 5 minutes

    private string $dataFile;
    private CacheInterface $cache;

    public function __construct(?string $dataFile = null)
    {
        $this->dataFile = $dataFile ?? FCPATH . 'data' . DIRECTORY_SEPARATOR . 'properties.json';
        $this->cache    = Services::cache();
    }

    /**
     * Returns the full decoded dataset (properties + neighborhoods), cached.
     *
     * @return array{properties: array<int, array<string, mixed>>, neighborhoods: array<int, array<string, mixed>>}
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

        $data['properties']    ??= [];
        $data['neighborhoods'] ??= [];

        $this->cache->save(self::CACHE_KEY, $data, self::CACHE_TTL);

        return $data;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return $this->dataset()['properties'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function neighborhoods(): array
    {
        return $this->dataset()['neighborhoods'];
    }

    /**
     * Featured listings for the home page carousel.
     *
     * @return array<int, array<string, mixed>>
     */
    public function featured(int $limit = 6): array
    {
        $featured = array_values(array_filter($this->all(), static fn ($p) => ! empty($p['featured'])));
        if (count($featured) < $limit) {
            // Top up with the newest non-featured listings.
            $rest = array_values(array_filter($this->all(), static fn ($p) => empty($p['featured'])));
            usort($rest, static fn ($a, $b) => strcmp((string) ($b['listed_date'] ?? ''), (string) ($a['listed_date'] ?? '')));
            $featured = array_merge($featured, $rest);
        }

        return array_slice($featured, 0, $limit);
    }

    /**
     * Find a single property by its id.
     *
     * @return array<string, mixed>|null
     */
    public function find(string $id): ?array
    {
        foreach ($this->all() as $property) {
            if (($property['id'] ?? null) === $id) {
                return $property;
            }
        }

        return null;
    }

    /**
     * Similar properties: same neighbourhood first, then same city, excluding self.
     *
     * @return array<int, array<string, mixed>>
     */
    public function similar(string $id, int $limit = 3): array
    {
        $current = $this->find($id);
        if ($current === null) {
            return [];
        }

        $pool = array_values(array_filter(
            $this->all(),
            static fn ($p) => ($p['id'] ?? null) !== $id
        ));

        usort($pool, static function ($a, $b) use ($current) {
            $score = static function ($p) use ($current): int {
                $s = 0;
                if (($p['neighborhood'] ?? null) === ($current['neighborhood'] ?? null)) {
                    $s += 2;
                }
                if (($p['city'] ?? null) === ($current['city'] ?? null)) {
                    $s += 1;
                }

                return $s;
            };

            return $score($b) <=> $score($a);
        });

        return array_slice($pool, 0, $limit);
    }

    /**
     * Filter + sort + paginate the dataset.
     *
     * Supported $params keys: q, city, type (array|csv|string), status,
     * min_price, max_price, beds, baths, min_sqft, max_sqft,
     * amenities (array|csv), sort, page, per_page.
     *
     * @param array<string, mixed> $params
     *
     * @return array{
     *     results: array<int, array<string, mixed>>,
     *     total: int, page: int, per_page: int, pages: int
     * }
     */
    public function filter(array $params = []): array
    {
        $items = $this->all();

        $q = trim((string) ($params['q'] ?? ''));
        if ($q !== '') {
            $needle = mb_strtolower($q);
            $items  = array_filter($items, static function ($p) use ($needle): bool {
                $haystack = mb_strtolower(implode(' ', [
                    $p['title'] ?? '', $p['city'] ?? '', $p['state'] ?? '',
                    $p['zip'] ?? '', $p['neighborhood'] ?? '', $p['address'] ?? '',
                ]));

                return str_contains($haystack, $needle);
            });
        }

        if (! empty($params['city'])) {
            $city  = mb_strtolower((string) $params['city']);
            $items = array_filter($items, static fn ($p) => mb_strtolower((string) ($p['city'] ?? '')) === $city);
        }

        $types = $this->toList($params['type'] ?? null);
        if ($types !== []) {
            $types = array_map('mb_strtolower', $types);
            $items = array_filter($items, static fn ($p) => in_array(mb_strtolower((string) ($p['type'] ?? '')), $types, true));
        }

        if (! empty($params['status'])) {
            $status = mb_strtolower((string) $params['status']);
            $items  = array_filter($items, static fn ($p) => mb_strtolower((string) ($p['status'] ?? '')) === $status);
        }

        if (isset($params['min_price']) && $params['min_price'] !== '') {
            $min   = (float) $params['min_price'];
            $items = array_filter($items, static fn ($p) => (float) ($p['price'] ?? 0) >= $min);
        }
        if (isset($params['max_price']) && $params['max_price'] !== '') {
            $max   = (float) $params['max_price'];
            $items = array_filter($items, static fn ($p) => (float) ($p['price'] ?? 0) <= $max);
        }

        if (! empty($params['beds'])) {
            $beds  = (int) $params['beds'];
            $items = array_filter($items, static fn ($p) => (int) ($p['beds'] ?? 0) >= $beds);
        }
        if (! empty($params['baths'])) {
            $baths = (int) $params['baths'];
            $items = array_filter($items, static fn ($p) => (int) ($p['baths'] ?? 0) >= $baths);
        }

        if (isset($params['min_sqft']) && $params['min_sqft'] !== '') {
            $minSqft = (int) $params['min_sqft'];
            $items   = array_filter($items, static fn ($p) => (int) ($p['sqft'] ?? 0) >= $minSqft);
        }
        if (isset($params['max_sqft']) && $params['max_sqft'] !== '') {
            $maxSqft = (int) $params['max_sqft'];
            $items   = array_filter($items, static fn ($p) => (int) ($p['sqft'] ?? 0) <= $maxSqft);
        }

        $amenities = $this->toList($params['amenities'] ?? null);
        if ($amenities !== []) {
            $items = array_filter($items, static function ($p) use ($amenities): bool {
                $have = array_map('mb_strtolower', (array) ($p['amenities'] ?? []));
                foreach ($amenities as $a) {
                    if (! in_array(mb_strtolower($a), $have, true)) {
                        return false;
                    }
                }

                return true;
            });
        }

        $items = array_values($items);
        $items = $this->sort($items, (string) ($params['sort'] ?? 'newest'));

        $total   = count($items);
        $perPage = max(1, (int) ($params['per_page'] ?? 9));
        $pages   = (int) max(1, ceil($total / $perPage));
        $page    = min($pages, max(1, (int) ($params['page'] ?? 1)));
        $offset  = ($page - 1) * $perPage;

        return [
            'results'  => array_slice($items, $offset, $perPage),
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => $pages,
        ];
    }

    /**
     * Distinct cities for autocomplete / dropdowns.
     *
     * @return array<int, string>
     */
    public function cities(): array
    {
        $cities = array_map(static fn ($p) => (string) ($p['city'] ?? ''), $this->all());
        $cities = array_values(array_unique(array_filter($cities)));
        sort($cities);

        return $cities;
    }

    /**
     * Distinct property types.
     *
     * @return array<int, string>
     */
    public function types(): array
    {
        $types = array_map(static fn ($p) => (string) ($p['type'] ?? ''), $this->all());
        $types = array_values(array_unique(array_filter($types)));
        sort($types);

        return $types;
    }

    /**
     * Distinct amenities across all listings.
     *
     * @return array<int, string>
     */
    public function amenities(): array
    {
        $all = [];
        foreach ($this->all() as $p) {
            foreach ((array) ($p['amenities'] ?? []) as $a) {
                $all[$a] = true;
            }
        }
        $amenities = array_keys($all);
        sort($amenities);

        return $amenities;
    }

    /**
     * Min/max price across listings for slider bounds.
     *
     * @return array{min: int, max: int}
     */
    public function priceRange(): array
    {
        $prices = array_map(static fn ($p) => (int) ($p['price'] ?? 0), $this->all());
        $prices = array_filter($prices);

        return [
            'min' => $prices === [] ? 0 : (int) min($prices),
            'max' => $prices === [] ? 0 : (int) max($prices),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $items
     *
     * @return array<int, array<string, mixed>>
     */
    private function sort(array $items, string $sort): array
    {
        switch ($sort) {
            case 'price_asc':
                usort($items, static fn ($a, $b) => (float) ($a['price'] ?? 0) <=> (float) ($b['price'] ?? 0));
                break;
            case 'price_desc':
                usort($items, static fn ($a, $b) => (float) ($b['price'] ?? 0) <=> (float) ($a['price'] ?? 0));
                break;
            case 'sqft_desc':
                usort($items, static fn ($a, $b) => (int) ($b['sqft'] ?? 0) <=> (int) ($a['sqft'] ?? 0));
                break;
            case 'newest':
            default:
                usort($items, static fn ($a, $b) => strcmp((string) ($b['listed_date'] ?? ''), (string) ($a['listed_date'] ?? '')));
                break;
        }

        return $items;
    }

    /**
     * Normalise a CSV string or array into a clean list of strings.
     *
     * @param mixed $value
     *
     * @return array<int, string>
     */
    private function toList($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        if (! is_array($value)) {
            $value = explode(',', (string) $value);
        }
        $value = array_map(static fn ($v) => trim((string) $v), $value);

        return array_values(array_filter($value, static fn ($v) => $v !== ''));
    }
}
