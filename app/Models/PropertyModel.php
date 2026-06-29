<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table         = 'properties';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'code', 'title', 'type', 'status', 'featured', 'price', 'beds', 'baths', 'sqft',
        'lot_size', 'year_built', 'mls', 'address', 'city', 'state', 'zip', 'lat', 'lng',
        'neighborhood', 'description', 'hoa', 'garage', 'walk_score', 'transit_score',
        'virtual_tour', 'listed_date', 'images', 'features', 'amenities', 'agent', 'pois',
    ];

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[180]',
        'price' => 'required|numeric',
        'city'  => 'required|max_length[90]',
    ];

    /** JSON-encoded columns. */
    private const JSON_FIELDS = ['images', 'features', 'amenities', 'agent', 'pois'];

    /**
     * All DB listings, normalised into the same array shape as properties.json.
     *
     * @return array<int, array<string, mixed>>
     */
    public function allNormalized(): array
    {
        return array_map([$this, 'normalize'], $this->orderBy('id', 'DESC')->findAll());
    }

    /**
     * Convert a raw DB row into the canonical property array used across the site.
     *
     * @param array<string, mixed> $row
     *
     * @return array<string, mixed>
     */
    public function normalize(array $row): array
    {
        foreach (self::JSON_FIELDS as $f) {
            $decoded   = json_decode((string) ($row[$f] ?? ''), true);
            $row[$f]   = is_array($decoded) ? $decoded : ($f === 'agent' ? [] : []);
        }
        $row['id']       = $row['code'] ?? ('db-' . ($row['id'] ?? ''));
        $row['featured'] = (bool) ($row['featured'] ?? false);
        $row['price']    = (int) ($row['price'] ?? 0);

        return $row;
    }

    /**
     * Build a storable row from admin-form input and insert it.
     * Returns the generated listing code on success, or false on failure.
     *
     * @param array<string, mixed> $input
     *
     * @return string|false
     */
    public function createFromForm(array $input)
    {
        $toList = static function ($val): array {
            if (is_array($val)) {
                return array_values(array_filter(array_map('trim', $val)));
            }

            return array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string) $val) ?: [])));
        };

        $code = 'prop_' . substr(md5(($input['title'] ?? '') . microtime(true)), 0, 8);

        $row = [
            'code'          => $code,
            'title'         => trim((string) ($input['title'] ?? '')),
            'type'          => trim((string) ($input['type'] ?? 'Single Family')),
            'status'        => trim((string) ($input['status'] ?? 'For Sale')),
            'featured'      => ! empty($input['featured']) ? 1 : 0,
            'price'         => (int) ($input['price'] ?? 0),
            'beds'          => (int) ($input['beds'] ?? 0),
            'baths'         => (int) ($input['baths'] ?? 0),
            'sqft'          => (int) ($input['sqft'] ?? 0),
            'lot_size'      => trim((string) ($input['lot_size'] ?? '')),
            'year_built'    => ($input['year_built'] ?? '') !== '' ? (int) $input['year_built'] : null,
            'mls'           => trim((string) ($input['mls'] ?? '')),
            'address'       => trim((string) ($input['address'] ?? '')),
            'city'          => trim((string) ($input['city'] ?? '')),
            'state'         => trim((string) ($input['state'] ?? '')),
            'zip'           => trim((string) ($input['zip'] ?? '')),
            'lat'           => ($input['lat'] ?? '') !== '' ? (float) $input['lat'] : null,
            'lng'           => ($input['lng'] ?? '') !== '' ? (float) $input['lng'] : null,
            'neighborhood'  => trim((string) ($input['neighborhood'] ?? '')),
            'description'   => trim((string) ($input['description'] ?? '')),
            'hoa'           => (int) ($input['hoa'] ?? 0),
            'garage'        => (int) ($input['garage'] ?? 0),
            'walk_score'    => (int) ($input['walk_score'] ?? 0),
            'transit_score' => (int) ($input['transit_score'] ?? 0),
            'virtual_tour'  => trim((string) ($input['virtual_tour'] ?? '')),
            'listed_date'   => $input['listed_date'] ?? date('Y-m-d'),
            'images'        => json_encode($toList($input['images'] ?? [])),
            'features'      => json_encode($toList($input['features'] ?? [])),
            'amenities'     => json_encode($toList($input['amenities'] ?? [])),
            'agent'         => json_encode([
                'name'     => trim((string) ($input['agent_name'] ?? '')),
                'title'    => trim((string) ($input['agent_title'] ?? 'Listing Advisor')),
                'phone'    => trim((string) ($input['agent_phone'] ?? '')),
                'whatsapp' => preg_replace('/[^0-9]/', '', (string) ($input['agent_phone'] ?? '')),
                'email'    => trim((string) ($input['agent_email'] ?? '')),
                'photo'    => trim((string) ($input['agent_photo'] ?? '')),
            ]),
            'pois'          => json_encode([]),
        ];

        return $this->insert($row, false) ? $code : false;
    }
}
