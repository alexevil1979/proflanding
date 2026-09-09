<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class NotificationLog
{
    public static function add(?int $leadId, string $channel, string $status, string $response): void
    {
        $st = DB::conn()->prepare(
            'INSERT INTO notification_log (lead_id, channel, status, response) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$leadId, $channel, $status, mb_substr($response, 0, 2000)]);
    }

    public static function recent(int $limit = 20): array
    {
        return DB::conn()->query(
            'SELECT * FROM notification_log ORDER BY created_at DESC LIMIT ' . (int)$limit
        )->fetchAll();
    }
}
