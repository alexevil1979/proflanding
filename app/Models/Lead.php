<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;

final class Lead
{
    public static function create(array $data): int
    {
        $st = DB::conn()->prepare(
            'INSERT INTO leads
            (name, phone, email, messenger, service_id, package_id, message, page_url,
             utm_source, utm_medium, utm_campaign, utm_content, utm_term, ip, user_agent, status)
             VALUES
            (:name,:phone,:email,:messenger,:service_id,:package_id,:message,:page_url,
             :utm_source,:utm_medium,:utm_campaign,:utm_content,:utm_term,:ip,:user_agent,:status)'
        );
        $st->execute($data);
        return (int)DB::conn()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $st = DB::conn()->prepare(
            'SELECT l.*, s.title AS service_title, p.title AS package_title
             FROM leads l
             LEFT JOIN services s ON s.id = l.service_id
             LEFT JOIN packages p ON p.id = l.package_id
             WHERE l.id = ? LIMIT 1'
        );
        $st->execute([$id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function list(?string $status = null, int $limit = 100): array
    {
        if ($status) {
            $st = DB::conn()->prepare(
                'SELECT l.*, s.title AS service_title, p.title AS package_title
                 FROM leads l
                 LEFT JOIN services s ON s.id = l.service_id
                 LEFT JOIN packages p ON p.id = l.package_id
                 WHERE l.status = ?
                 ORDER BY l.created_at DESC LIMIT ' . (int)$limit
            );
            $st->execute([$status]);
            return $st->fetchAll();
        }
        return DB::conn()->query(
            'SELECT l.*, s.title AS service_title, p.title AS package_title
             FROM leads l
             LEFT JOIN services s ON s.id = l.service_id
             LEFT JOIN packages p ON p.id = l.package_id
             ORDER BY l.created_at DESC LIMIT ' . (int)$limit
        )->fetchAll();
    }

    public static function updateStatus(int $id, string $status, ?string $note = null): void
    {
        $st = DB::conn()->prepare('UPDATE leads SET status = ?, admin_note = ? WHERE id = ?');
        $st->execute([$status, $note, $id]);
    }

    public static function countByStatus(string $status): int
    {
        $st = DB::conn()->prepare('SELECT COUNT(*) FROM leads WHERE status = ?');
        $st->execute([$status]);
        return (int)$st->fetchColumn();
    }

    public static function countRecentByIp(string $ip, int $minutes): int
    {
        $st = DB::conn()->prepare(
            'SELECT COUNT(*) FROM leads WHERE ip = ? AND created_at >= (NOW() - INTERVAL ? MINUTE)'
        );
        $st->execute([$ip, $minutes]);
        return (int)$st->fetchColumn();
    }
}
