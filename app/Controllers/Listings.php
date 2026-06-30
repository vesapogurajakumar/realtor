<?php

declare(strict_types=1);

namespace App\Controllers;

use Config\Services;

class Listings extends BaseController
{
    /**
     * Listings page with filter sidebar, grid/list results and a cluster map.
     */
    public function index(): string
    {
        $svc = Services::property();

        // Seed initial params from the query string (e.g. coming from the hero search).
        $params = $this->collectParams();
        $result = $svc->filter($params);

        return view('listings', [
            'title'           => 'Property Listings | Vesta Real Estate',
            'metaDescription' => 'Browse luxury homes for sale and rent. Filter by price, beds, baths, type and amenities, and explore listings on an interactive map.',
            'activeNav'       => 'listings',
            'result'          => $result,
            'params'          => $params,
            'priceRange'      => $svc->priceRange(),
            'types'           => $svc->types(),
            'amenities'       => $svc->amenities(),
            'cities'          => $svc->cities(),
            'markers'         => $this->markers($svc->all()),
        ]);
    }

    /**
     * AJAX filter endpoint — returns rendered cards, marker data and pagination meta.
     */
    public function filter()
    {
        $svc    = Services::property();
        $params = $this->collectParams();
        $result = $svc->filter($params);

        $cards = '';
        foreach ($result['results'] as $property) {
            $cards .= view('partials/property_card', ['property' => $property, 'aos' => false]);
        }

        return $this->response->setJSON([
            'status'     => 'success',
            'html'       => $cards,
            'markers'    => $this->markers($result['results']),
            'total'      => $result['total'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'per_page'   => $result['per_page'],
            'summary'    => $result['total'] . ' ' . ($result['total'] === 1 ? 'home' : 'homes') . ' found',
            'pagination' => $this->paginationHtml($result['page'], $result['pages']),
        ]);
    }

    /**
     * Single property detail page.
     */
    public function detail(string $id)
    {
        $svc      = Services::property();
        $property = $svc->find($id);

        if ($property === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Property not found');
        }

        return view('property_detail', [
            'title'           => $property['title'] . ' | Vesta Real Estate',
            'metaDescription' => reading_excerpt((string) ($property['description'] ?? ''), 160),
            'ogImage'         => $property['images'][0] ?? null,
            'activeNav'       => 'listings',
            'bodyClass'       => 'header-solid',
            'property'        => $property,
            'similar'         => $svc->similar($id, 3),
            'jsonld'          => $this->jsonLd($property),
        ]);
    }

    /**
     * Normalise request parameters used by the property filter.
     *
     * @return array<string, mixed>
     */
    private function collectParams(): array
    {
        $req = $this->request;

        return array_filter([
            'q'         => $req->getGet('q'),
            'city'      => $req->getGet('city'),
            'type'      => $req->getGet('type'),
            'status'    => $req->getGet('status'),
            'min_price' => $req->getGet('min_price'),
            'max_price' => $req->getGet('max_price'),
            'beds'      => $req->getGet('beds'),
            'baths'     => $req->getGet('baths'),
            'min_sqft'  => $req->getGet('min_sqft'),
            'max_sqft'  => $req->getGet('max_sqft'),
            'amenities' => $req->getGet('amenities'),
            'sort'      => $req->getGet('sort'),
            'page'      => $req->getGet('page'),
            'per_page'  => $req->getGet('per_page'),
        ], static fn ($v) => $v !== null && $v !== '');
    }

    /**
     * Build a lightweight marker payload for the Leaflet map.
     *
     * @param array<int, array<string, mixed>> $properties
     *
     * @return array<int, array<string, mixed>>
     */
    private function markers(array $properties): array
    {
        $out = [];
        foreach ($properties as $p) {
            if (! isset($p['lat'], $p['lng'])) {
                continue;
            }
            $out[] = [
                'id'     => $p['id'] ?? '',
                'lat'    => (float) $p['lat'],
                'lng'    => (float) $p['lng'],
                'price'  => money($p['price'] ?? 0) . (strtolower((string) ($p['status'] ?? '')) === 'for rent' ? '/mo' : ''),
                'title'  => $p['title'] ?? '',
                'beds'   => (int) ($p['beds'] ?? 0),
                'baths'  => (int) ($p['baths'] ?? 0),
                'sqft'   => (int) ($p['sqft'] ?? 0),
                'type'   => $p['type'] ?? '',
                'status' => $p['status'] ?? '',
                'img'    => $p['images'][0] ?? '',
                'url'    => property_url((string) ($p['id'] ?? '')),
            ];
        }

        return $out;
    }

    /**
     * Render numbered pagination markup for the AJAX response.
     */
    private function paginationHtml(int $page, int $pages): string
    {
        if ($pages <= 1) {
            return '';
        }
        $html = '<nav class="pager" aria-label="Listings pagination">';
        $html .= '<button class="pager-btn" data-page="' . max(1, $page - 1) . '"' . ($page <= 1 ? ' disabled' : '') . ' aria-label="Previous">‹</button>';
        for ($i = 1; $i <= $pages; $i++) {
            $active = $i === $page ? ' is-active' : '';
            $html .= '<button class="pager-btn' . $active . '" data-page="' . $i . '">' . $i . '</button>';
        }
        $html .= '<button class="pager-btn" data-page="' . min($pages, $page + 1) . '"' . ($page >= $pages ? ' disabled' : '') . ' aria-label="Next">›</button>';
        $html .= '</nav>';

        return $html;
    }

    /**
     * RealEstateListing JSON-LD for the detail page.
     */
    private function jsonLd(array $p): string
    {
        $data = [
            '@context'    => 'https://schema.org',
            '@type'       => 'RealEstateListing',
            'name'        => $p['title'] ?? '',
            'description' => reading_excerpt((string) ($p['description'] ?? ''), 300),
            'url'         => property_url((string) ($p['id'] ?? '')),
            'image'       => $p['images'] ?? [],
            'datePosted'  => $p['listed_date'] ?? null,
            'offers'      => [
                '@type'         => 'Offer',
                'price'         => $p['price'] ?? 0,
                'priceCurrency' => 'USD',
            ],
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $p['address'] ?? '',
                'addressLocality' => $p['city'] ?? '',
                'addressRegion'   => $p['state'] ?? '',
                'postalCode'      => $p['zip'] ?? '',
            ],
            'geo' => [
                '@type'     => 'GeoCoordinates',
                'latitude'  => $p['lat'] ?? null,
                'longitude' => $p['lng'] ?? null,
            ],
            'numberOfRooms'       => $p['beds'] ?? null,
            'numberOfBathroomsTotal' => $p['baths'] ?? null,
            'floorSize'           => [
                '@type'    => 'QuantitativeValue',
                'value'    => $p['sqft'] ?? null,
                'unitCode' => 'FTK',
            ],
        ];

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
