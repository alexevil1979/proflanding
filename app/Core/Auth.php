<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;
use App\Models\LoginAttempt;

final class Auth
{
    private const SESSION_USER = 'admin_user_id';

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        $secure = Request::isHttps();
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_name('proflanding_sess');
        session_start();
    }

    public static function check(): bool
    {
        return !empty($_SESSION[self::SESSION_USER]);
    }

    public static function id(): ?int
    {
        return isset($_SESSION[self::SESSION_USER]) ? (int)$_SESSION[self::SESSION_USER] : null;
    }

    public static function user(): ?array
    {
        $id = self::id();
        return $id ? User::find($id) : null;
    }

    public static function attempt(string $login, string $password, string $ip): bool
    {
        $lock = Config::get('login_lock', ['max' => 5, 'minutes' => 15]);
        if (LoginAttempt::isBlocked($ip, (int)$lock['max'], (int)$lock['minutes'])) {
            return false;
        }

        $user = User::findByLogin($login);
        $ok = $user && password_verify($password, $user['password_hash']);
        LoginAttempt::record($ip, $login, $ok);

        if (!$ok) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER] = (int)$user['id'];
        User::touchLogin((int)$user['id']);
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', (bool)$p['secure'], (bool)$p['httponly']);
        }
        session_destroy();
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /admin/login');
            exit;
        }
    }
}
