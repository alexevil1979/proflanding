<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Lang;

final class SitemapController
{
    public function index(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $base = rtrim(app_url(), '/');
        $pages = ['/', '/privacy', '/offer'];
        $langs = Lang::codes();

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        foreach ($pages as $page) {
            foreach ($langs as $lang) {
                $loc = $lang === Lang::DEFAULT
                    ? $base . ($page === '/' ? '/' : $page)
                    : $base . '/' . $lang . ($page === '/' ? '/' : $page);
                echo "  <url>\n";
                echo '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
                foreach ($langs as $alt) {
                    $href = $alt === Lang::DEFAULT
                        ? $base . ($page === '/' ? '/' : $page)
                        : $base . '/' . $alt . ($page === '/' ? '/' : $page);
                    $hreflang = $alt === 'zh' ? 'zh-Hans' : $alt;
                    echo '    <xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . htmlspecialchars($href, ENT_XML1) . "\"/>\n";
                }
                echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($base . ($page === '/' ? '/' : $page), ENT_XML1) . "\"/>\n";
                echo "    <changefreq>weekly</changefreq>\n";
                echo '    <priority>' . ($page === '/' ? '1.0' : '0.3') . "</priority>\n";
                echo "  </url>\n";
            }
        }
        echo '</urlset>';
    }
}
