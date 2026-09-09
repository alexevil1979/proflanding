<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use PDO;

final class Setting
{
    public static function all(): array
    {
        $rows = DB::conn()->query('SELECT k, v FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['k']] = $row['v'];
        }
        return $out;
    }

    public static function get(string $key, string $default = ''): string
    {
        $st = DB::conn()->prepare('SELECT v FROM settings WHERE k = ? LIMIT 1');
        $st->execute([$key]);
        $v = $st->fetchColumn();
        return $v === false || $v === null || $v === '' ? $default : (string)$v;
    }

    public static function set(string $key, ?string $value): void
    {
        $st = DB::conn()->prepare(
            'INSERT INTO settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)'
        );
        $st->execute([$key, $value]);
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            self::set((string)$k, $v === null ? null : (string)$v);
        }
    }
}
