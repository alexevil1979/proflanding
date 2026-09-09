<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class Package
{
    public static function active(): array
    {
        return DB::conn()->query(
            'SELECT * FROM packages WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        )->fetchAll();
    }

    public static function all(): array
    {
        return DB::conn()->query('SELECT * FROM packages ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $st = DB::conn()->prepare('SELECT * FROM packages WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $st = DB::conn()->prepare(
            'INSERT INTO packages (title, description, price, price_note, features, is_featured, is_active, sort_order, cta_label)
             VALUES (:title,:description,:price,:price_note,:features,:is_featured,:is_active,:sort_order,:cta_label)'
        );
        $st->execute($data);
        return (int)DB::conn()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $st = DB::conn()->prepare(
            'UPDATE packages SET title=:title, description=:description, price=:price, price_note=:price_note,
             features=:features, is_featured=:is_featured, is_active=:is_active, sort_order=:sort_order, cta_label=:cta_label
             WHERE id=:id'
        );
        $st->execute($data);
    }

    public static function delete(int $id): void
    {
        $st = DB::conn()->prepare('DELETE FROM packages WHERE id = ?');
        $st->execute([$id]);
    }

    public static function featuresList(?string $json): array
    {
        if (!$json) {
            return [];
        }
        $decoded = json_decode($json, true);
        return is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $json)));
    }
}
