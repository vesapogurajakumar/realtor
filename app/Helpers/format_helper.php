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

if (! function_exists('img_url')) {
    /**
     * Optimise an image URL. For Unsplash CDN URLs it sets width, quality and
     * auto-format (so the CDN serves AVIF/WebP). Non-Unsplash URLs (e.g. local
     * uploads, already resized/compressed) are returned unchanged.
     */
    function img_url(string $url, int $width = 1200, int $quality = 70): string
    {
        if (strpos($url, 'images.unsplash.com') === false) {
            return $url;
        }

        $parts = parse_url($url);
        parse_str($parts['query'] ?? '', $q);
        $q['w']    = $width;
        $q['q']    = $quality;
        $q['auto'] = 'format';
        $q['fit']  = $q['fit'] ?? 'crop';

        return ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? 'images.unsplash.com')
            . ($parts['path'] ?? '') . '?' . http_build_query($q);
    }
}

if (! function_exists('img_srcset')) {
    /**
     * Build a responsive srcset for an Unsplash URL (empty for other hosts).
     *
     * @param array<int, int> $widths
     */
    function img_srcset(string $url, array $widths = [400, 800, 1200, 1600], int $quality = 70): string
    {
        if (strpos($url, 'images.unsplash.com') === false) {
            return '';
        }
        $set = [];
        foreach ($widths as $w) {
            $set[] = img_url($url, $w, $quality) . ' ' . $w . 'w';
        }

        return implode(', ', $set);
    }
}

if (! function_exists('img_tag')) {
    /**
     * Render a performance-optimised <img>: responsive srcset/sizes for Unsplash,
     * lazy loading + async decoding, optional eager/high-priority for hero images.
     *
     * @param array<string, mixed> $o  width, widths, sizes, class, loading, fetchpriority, quality, style, attr
     */
    function img_tag(string $url, string $alt, array $o = []): string
    {
        $width   = (int) ($o['width'] ?? 1200);
        $widths  = $o['widths'] ?? [400, 800, 1200, 1600];
        $sizes   = $o['sizes'] ?? '100vw';
        $quality = (int) ($o['quality'] ?? 70);
        $loading = $o['loading'] ?? 'lazy';

        $src    = img_url($url, $width, $quality);
        $srcset = img_srcset($url, $widths, $quality);

        $html = '<img src="' . esc($src) . '" alt="' . esc($alt, 'attr') . '"';
        if ($srcset !== '') {
            $html .= ' srcset="' . esc($srcset) . '" sizes="' . esc($sizes, 'attr') . '"';
        }
        if (! empty($o['class'])) {
            $html .= ' class="' . esc($o['class'], 'attr') . '"';
        }
        if (! empty($o['style'])) {
            $html .= ' style="' . esc($o['style'], 'attr') . '"';
        }
        $html .= ' loading="' . esc($loading, 'attr') . '" decoding="async"';
        if (! empty($o['fetchpriority'])) {
            $html .= ' fetchpriority="' . esc($o['fetchpriority'], 'attr') . '"';
        }
        if (! empty($o['attr'])) {
            $html .= ' ' . $o['attr'];
        }

        return $html . '>';
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
