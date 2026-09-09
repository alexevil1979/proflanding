<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class LoginAttempt
{
    public static function record(string $ip, string $login, bool $success): void
    {
        $st = DB::conn()->prepare(
            'INSERT INTO login_attempts (ip, login, success) VALUES (?, ?, ?)'
        );
        $st->execute([$ip, $login, $success ? 1 : 0]);
    }

    public static function isBlocked(string $ip, int $max, int $minutes): bool
    {
        $st = DB::conn()->prepare(
            'SELECT COUNT(*) FROM login_attempts
             WHERE ip = ? AND success = 0 AND created_at >= (NOW() - INTERVAL ? MINUTE)'
        );
        $st->execute([$ip, $minutes]);
        return (int)$st->fetchColumn() >= $max;
    }
}
