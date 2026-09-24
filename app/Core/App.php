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

    /**
     * 301 на APP_URL, если текущий Host перечислен в CANONICAL_REDIRECT_FROM
     * (через запятую). Пример на bizdevops: proflanding.1tlt.ru
     * На инстансе 1tlt.ru список пустой — редиректа нет.
     */
    private static function canonicalHostRedirect(): void
    {
        $fromRaw = trim((string)($_ENV['CANONICAL_REDIRECT_FROM'] ?? getenv('CANONICAL_REDIRECT_FROM') ?: ''));
        if ($fromRaw === '') {
            return;
        }
        $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
        $host = preg_replace('/:\d+$/', '', $host) ?? $host;
        if ($host === '') {
            return;
        }
        $targets = array_filter(array_map(
            static fn(string $h): string => strtolower(trim($h)),
            explode(',', $fromRaw)
        ));
        if ($targets === [] || !in_array($host, $targets, true)) {
            return;
        }
        $base = rtrim((string)Config::get('url', ''), '/');
        if ($base === '') {
            return;
        }
        $uri = (string)($_SERVER['REQUEST_URI'] ?? '/');
        header('Location: ' . $base . $uri, true, 301);
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
