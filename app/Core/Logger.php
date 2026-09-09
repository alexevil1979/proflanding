<?php

declare(strict_types=1);

namespace App\Core;

final class Logger
{
    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void
    {
        $root = dirname(__DIR__, 2);
        $dir = $root . '/storage/logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $safeContext = self::redact($context);
        $line = sprintf(
            "[%s] %s %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $safeContext ? json_encode($safeContext, JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents($dir . '/app.log', $line, FILE_APPEND | LOCK_EX);
    }

    private static function redact(array $context): array
    {
        $deny = ['password', 'pass', 'token', 'smtp_pass', 'SMTP_PASS', 'TELEGRAM_BOT_TOKEN', 'APP_KEY'];
        foreach ($context as $k => $v) {
            foreach ($deny as $d) {
                if (stripos((string)$k, $d) !== false) {
                    $context[$k] = '***';
                }
            }
        }
        return $context;
    }
}
