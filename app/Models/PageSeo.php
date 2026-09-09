<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class PageSeo
{
    public static function get(string $key): ?array
    {
        $st = DB::conn()->prepare('SELECT * FROM pages_seo WHERE page_key = ? LIMIT 1');
        $st->execute([$key]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function all(): array
    {
        return DB::conn()->query('SELECT * FROM pages_seo ORDER BY page_key')->fetchAll();
    }

    public static function upsert(string $key, array $data): void
    {
        $st = DB::conn()->prepare(
            'INSERT INTO pages_seo (page_key, title, description, h1, og_title, og_description, og_image, canonical, robots)
             VALUES (:page_key,:title,:description,:h1,:og_title,:og_description,:og_image,:canonical,:robots)
             ON DUPLICATE KEY UPDATE
               title=VALUES(title), description=VALUES(description), h1=VALUES(h1),
               og_title=VALUES(og_title), og_description=VALUES(og_description),
               og_image=VALUES(og_image), canonical=VALUES(canonical), robots=VALUES(robots)'
        );
        $st->execute(array_merge(['page_key' => $key], $data));
    }
}
