<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class Service
{
    public static function active(): array
    {
        $st = DB::conn()->query(
            'SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC'
        );
        return $st->fetchAll();
    }

    public static function all(): array
    {
        return DB::conn()->query('SELECT * FROM services ORDER BY sort_order ASC, id ASC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $st = DB::conn()->prepare('SELECT * FROM services WHERE id = ? LIMIT 1');
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $st = DB::conn()->prepare(
            'INSERT INTO services (slug, title, short_text, full_text, price_from, price_note, period, icon, sort_order, is_active, is_featured, cta_label)
             VALUES (:slug,:title,:short_text,:full_text,:price_from,:price_note,:period,:icon,:sort_order,:is_active,:is_featured,:cta_label)'
        );
        $st->execute($data);
        return (int)DB::conn()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $st = DB::conn()->prepare(
            'UPDATE services SET slug=:slug, title=:title, short_text=:short_text, full_text=:full_text,
             price_from=:price_from, price_note=:price_note, period=:period, icon=:icon,
             sort_order=:sort_order, is_active=:is_active, is_featured=:is_featured, cta_label=:cta_label
             WHERE id=:id'
        );
        $st->execute($data);
    }

    public static function delete(int $id): void
    {
        $st = DB::conn()->prepare('DELETE FROM services WHERE id = ?');
        $st->execute([$id]);
    }

    public static function reorder(array $ids): void
    {
        $st = DB::conn()->prepare('UPDATE services SET sort_order = ? WHERE id = ?');
        $order = 10;
        foreach ($ids as $id) {
            $st->execute([$order, (int)$id]);
            $order += 10;
        }
    }

    public static function countActive(): int
    {
        return (int)DB::conn()->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn();
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        if ($exceptId) {
            $st = DB::conn()->prepare('SELECT id FROM services WHERE slug = ? AND id <> ? LIMIT 1');
            $st->execute([$slug, $exceptId]);
        } else {
            $st = DB::conn()->prepare('SELECT id FROM services WHERE slug = ? LIMIT 1');
            $st->execute([$slug]);
        }
        return (bool)$st->fetchColumn();
    }
}
