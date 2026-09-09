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
        self::securityHeaders();

        Lang::boot($root);

        $router = new Router();
        require $root . '/config/routes.php';
        $router->dispatch(Request::method(), Request::uri());
    }

    private static function securityHeaders(): void
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        header('X-XSS-Protection: 0');
    }
}
