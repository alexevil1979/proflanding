<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

final class DB
{
    private static ?PDO $pdo = null;

    public static function conn(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = (string)Config::get('db.host', '127.0.0.1');
        $name = (string)Config::get('db.name', 'proflanding');
        $user = (string)Config::get('db.user', '');
        $pass = (string)Config::get('db.pass', '');
        $charset = (string)Config::get('db.charset', 'utf8mb4');

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
        try {
            self::$pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            self::$pdo->exec("SET time_zone = '+03:00'");
            self::$pdo->exec("SET NAMES {$charset}");
        } catch (PDOException $e) {
            throw new RuntimeException('Ошибка подключения к БД', 0, $e);
        }

        return self::$pdo;
    }
}
