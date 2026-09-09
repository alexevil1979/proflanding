<?php

declare(strict_types=1);

namespace App\Core;

final class Csrf
{
    private const KEY = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION[self::KEY];
    }

    public static function field(): string
    {
        $t = htmlspecialchars(self::token(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return '<input type="hidden" name="_csrf" value="' . $t . '">';
    }

    public static function validate(?string $token): bool
    {
        if ($token === null || $token === '' || empty($_SESSION[self::KEY])) {
            return false;
        }
        return hash_equals((string)$_SESSION[self::KEY], $token);
    }

    public static function requireValid(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!self::validate(is_string($token) ? $token : null)) {
            http_response_code(419);
            if (self::wantsJson()) {
                View::json(['ok' => false, 'error' => 'CSRF token invalid'], 419);
                exit;
            }
            echo 'Ошибка CSRF. Обновите страницу.';
            exit;
        }
    }

    private static function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strcasecmp($xhr, 'XMLHttpRequest') === 0;
    }
}
