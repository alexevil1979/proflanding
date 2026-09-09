<?php

declare(strict_types=1);

namespace App\Controllers;

final class SitemapController
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = rtrim(app_url(), '/');
        $urls = [
            ['loc' => $base . '/', 'priority' => '1.0'],
            ['loc' => $base . '/privacy', 'priority' => '0.3'],
            ['loc' => $base . '/offer', 'priority' => '0.3'],
        ];
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            echo '  <url><loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc><changefreq>weekly</changefreq><priority>' . $u['priority'] . '</priority></url>' . "\n";
        }
        echo '</urlset>';
    }
}
