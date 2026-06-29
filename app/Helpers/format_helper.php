<?php

declare(strict_types=1);

/**
 * Formatting helpers shared across the Vesta real-estate views.
 * Loaded globally via BaseController so every view can use them.
 */

if (! function_exists('money')) {
    /**
     * Format a numeric price as USD with no decimals (e.g. 2450000 -> $2,450,000).
     */
    function money($amount): string
    {
        return '$' . number_format((float) $amount, 0, '.', ',');
    }
}

if (! function_exists('price_label')) {
    /**
     * Price label that appends "/mo" for rentals.
     *
     * @param array<string, mixed> $property
     */
    function price_label(array $property): string
    {
        $label = money($property['price'] ?? 0);
        if (strtolower((string) ($property['status'] ?? '')) === 'for rent') {
            $label .= '<span class="price-period">/mo</span>';
        }

        return $label;
    }
}

if (! function_exists('status_class')) {
    /**
     * Maps a listing status to a CSS modifier class.
     */
    function status_class(?string $status): string
    {
        return match (strtolower((string) $status)) {
            'for sale' => 'badge--sale',
            'for rent' => 'badge--rent',
            'sold'     => 'badge--sold',
            default    => 'badge--sale',
        };
    }
}

if (! function_exists('property_url')) {
    /**
     * Canonical URL for a property detail page (respects the public/ prefix).
     */
    function property_url(string $id): string
    {
        return base_url('public/property/' . $id);
    }
}

if (! function_exists('sqft')) {
    /**
     * Format a square-footage value with thousands separators.
     */
    function sqft($value): string
    {
        return number_format((float) $value, 0, '.', ',');
    }
}

if (! function_exists('reading_excerpt')) {
    /**
     * Trim a string to a clean excerpt without breaking mid-word.
     */
    function reading_excerpt(string $text, int $limit = 140): string
    {
        $text = trim(strip_tags($text));
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit);
        $cut = mb_substr($cut, 0, (int) mb_strrpos($cut, ' '));

        return $cut . '…';
    }
}

if (! function_exists('nice_date')) {
    /**
     * Format an ISO date (Y-m-d) as e.g. "Jun 2, 2025".
     */
    function nice_date(?string $iso): string
    {
        if (! $iso) {
            return '';
        }
        $ts = strtotime($iso);

        return $ts ? date('M j, Y', $ts) : $iso;
    }
}
