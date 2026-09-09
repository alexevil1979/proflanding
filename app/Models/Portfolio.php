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
}
