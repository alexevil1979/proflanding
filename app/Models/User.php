<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class User
{
    public static function find(int $id): ?array
    {
        $st = DB::conn()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findByLogin(string $login): ?array
    {
        $st = DB::conn()->prepare('SELECT * FROM users WHERE login = ? LIMIT 1');
        $st->execute([$login]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function touchLogin(int $id): void
    {
        $st = DB::conn()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
        $st->execute([$id]);
    }

    public static function updatePassword(int $id, string $hash): void
    {
        $st = DB::conn()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $st->execute([$hash, $id]);
    }
}
