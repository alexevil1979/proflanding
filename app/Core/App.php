<?php

declare(strict_types=1);

namespace App\Core;

final class App
{
    public static function boot(string $root): void
    {
        Config::load($root);
        date_default_timezone_set((string)Config::get('timezone', 'Europe/Moscow'));
        mb_internal_encoding('UTF-8');

        Auth::startSession();
        self::canonicalHostRedirect();
        self::securityHeaders();

        Lang::boot($root);

        $router = new Router();
        require $root . '/config/routes.php';
        $router->dispatch(Request::method(), Request::uri());
    }

    private static function canonicalHostRedirect(): void
    {
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        if ($host === '' || !str_contains($host, '1tlt.ru')) {
            return;
        }
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: https://bizdevops.site' . $uri, true, 301);
        exit;
    }

    private static function securityHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('X-XSS-Protection: 0');
        if (Request::isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
