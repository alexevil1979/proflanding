<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class Portfolio
{
    public static function active(): array
    {
        return DB::conn()->query(
            'SELECT * FROM portfolio WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public static function all(): array
    {
        return DB::conn()->query(
            'SELECT * FROM portfolio ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $st = DB::conn()->prepare('SELECT * FROM portfolio WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $st = DB::conn()->prepare(
            'INSERT INTO portfolio (title, description, stack, result_text, url, image, is_active, sort_order)
             VALUES (:title,:description,:stack,:result_text,:url,:image,:is_active,:sort_order)'
        );
        $st->execute($data);
        return (int)DB::conn()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $st = DB::conn()->prepare(
            'UPDATE portfolio SET title=:title, description=:description, stack=:stack, result_text=:result_text,
             url=:url, image=:image, is_active=:is_active, sort_order=:sort_order WHERE id=:id'
        );
        $st->execute($data);
    }

    public static function delete(int $id): void
    {
        $st = DB::conn()->prepare('DELETE FROM portfolio WHERE id = ?');
        $st->execute([$id]);
    }
}
