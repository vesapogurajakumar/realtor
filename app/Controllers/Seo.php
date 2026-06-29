<?php

declare(strict_types=1);

namespace App\Controllers;

use Config\Services;

class Seo extends BaseController
{
    /**
     * Dynamic XML sitemap covering static pages, every property and every post.
     */
    public function sitemap()
    {
        $urls = [
            ['loc' => base_url('public/'),         'priority' => '1.0', 'freq' => 'daily'],
            ['loc' => base_url('public/listings'), 'priority' => '0.9', 'freq' => 'daily'],
            ['loc' => base_url('public/blog'),     'priority' => '0.8', 'freq' => 'weekly'],
            ['loc' => base_url('public/about'),    'priority' => '0.5', 'freq' => 'monthly'],
            ['loc' => base_url('public/contact'),  'priority' => '0.6', 'freq' => 'monthly'],
        ];

        foreach (Services::property()->all() as $p) {
            $urls[] = [
                'loc'      => property_url((string) ($p['id'] ?? '')),
                'priority' => '0.8',
                'freq'     => 'weekly',
                'lastmod'  => $p['listed_date'] ?? null,
            ];
        }

        foreach (Services::blog()->all() as $post) {
            $urls[] = [
                'loc'      => base_url('public/blog/' . ($post['slug'] ?? '')),
                'priority' => '0.7',
                'freq'     => 'monthly',
                'lastmod'  => $post['date'] ?? null,
            ];
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . esc($u['loc']) . "</loc>\n";
            if (! empty($u['lastmod'])) {
                $xml .= '    <lastmod>' . esc($u['lastmod']) . "</lastmod>\n";
            }
            $xml .= '    <changefreq>' . $u['freq'] . "</changefreq>\n";
            $xml .= '    <priority>' . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return $this->response->setContentType('application/xml')->setBody($xml);
    }

    /**
     * robots.txt with sitemap reference.
     */
    public function robots()
    {
        $body = "User-agent: *\n"
            . "Allow: /\n"
            . "Disallow: /writable/\n"
            . 'Sitemap: ' . base_url('public/sitemap.xml') . "\n";

        return $this->response->setContentType('text/plain')->setBody($body);
    }
}
