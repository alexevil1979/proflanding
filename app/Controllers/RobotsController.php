<?php

declare(strict_types=1);

namespace App\Controllers;

final class RobotsController
{
    public function index(): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
        $host = rtrim(app_url(), '/');
        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /admin/\n";
        echo "Disallow: /thank-you\n";
        echo "Disallow: /thank-you/\n";
        echo "Clean-param: utm_source&utm_medium&utm_campaign&utm_content&utm_term&yclid&gclid&fbclid\n";
        echo "\n";
        echo 'Host: ' . $host . "\n";
        echo 'Sitemap: ' . $host . "/sitemap.xml\n";
    }
}
