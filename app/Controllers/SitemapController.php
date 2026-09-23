<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\DB;
use App\Core\Lang;

final class SitemapController
{
    public function index(): void
    {
        try {
            header('Content-Type: application/xml; charset=utf-8');
            header('Cache-Control: public, max-age=3600');
            $base = rtrim(app_url(), '/');
            if ($base === '' || preg_match('#1tlt\.ru#i', $base)) {
                $base = 'https://bizdevops.site';
            }
            $pages = [
                '/' => '1.0',
                '/privacy' => '0.3',
                '/offer' => '0.3',
            ];
            $langs = index_locales();
            if ($langs === []) {
                $langs = [Lang::DEFAULT];
            }
            $lastmod = self::lastmod();

            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
            foreach ($pages as $page => $priority) {
                foreach ($langs as $lang) {
                    $loc = self::loc($base, $lang, $page);
                    echo "  <url>\n";
                    echo '    <loc>' . htmlspecialchars($loc, ENT_XML1) . "</loc>\n";
                    echo '    <lastmod>' . $lastmod . "</lastmod>\n";
                    foreach ($langs as $alt) {
                        $href = self::loc($base, $alt, $page);
                        $hreflang = $alt === 'zh' ? 'zh-Hans' : $alt;
                        echo '    <xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . htmlspecialchars($href, ENT_XML1) . "\"/>\n";
                    }
                    echo '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars(self::loc($base, Lang::DEFAULT, $page), ENT_XML1) . "\"/>\n";
                    echo "    <changefreq>weekly</changefreq>\n";
                    echo '    <priority>' . $priority . "</priority>\n";
                    echo "  </url>\n";
                }
            }
            echo '</urlset>';
        } catch (\Throwable $e) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Sitemap unavailable';
        }
    }

    private static function loc(string $base, string $lang, string $page): string
    {
        if ($lang === Lang::DEFAULT) {
            return $base . ($page === '/' ? '/' : $page);
        }
        return $base . '/' . $lang . ($page === '/' ? '/' : $page);
    }

    private static function lastmod(): string
    {
        $ts = [time()];
        try {
            $pdo = DB::conn();
            foreach (['portfolio', 'leads'] as $table) {
                $v = $pdo->query('SELECT MAX(created_at) FROM `' . $table . '`')->fetchColumn();
                if ($v) {
                    $ts[] = strtotime((string)$v) ?: time();
                }
            }
        } catch (\Throwable $e) {
        }
        return date('Y-m-d', max($ts));
    }
}
