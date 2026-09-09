<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Models\Setting;

/**
 * SMTP / Telegram: сначала settings (админка), иначе .env.
 */
final class NotifyConfig
{
    public static function smtp(): array
    {
        $env = Config::get('smtp', []);
        return [
            'host' => self::pref('smtp_host', (string)($env['host'] ?? 'smtp.gmail.com')),
            'port' => (int)self::pref('smtp_port', (string)($env['port'] ?? '587')),
            'secure' => self::pref('smtp_secure', (string)($env['secure'] ?? 'tls')),
            'user' => self::pref('smtp_user', (string)($env['user'] ?? '')),
            'pass' => self::pref('smtp_pass', (string)($env['pass'] ?? '')),
            'from' => self::pref('smtp_from', (string)($env['from'] ?? '')),
            'from_name' => self::pref('smtp_from_name', (string)($env['from_name'] ?? 'IT Specialist')),
            'to' => self::pref('smtp_to', (string)($env['to'] ?? '')),
        ];
    }

    public static function telegram(): array
    {
        return [
            'token' => self::pref('telegram_bot_token', (string)Config::get('telegram.token', '')),
            'chat_id' => self::pref('telegram_chat_id', (string)Config::get('telegram.chat_id', '')),
        ];
    }

    public static function telegramEnabled(): bool
    {
        $default = Config::get('telegram.enabled') ? '1' : '0';
        return Setting::get('telegram_enabled', $default) === '1';
    }

    public static function mailEnabled(): bool
    {
        $default = Config::get('mail_enabled') ? '1' : '0';
        return Setting::get('mail_enabled', $default) === '1';
    }

    public static function hasSmtpPass(): bool
    {
        return self::smtp()['pass'] !== '';
    }

    public static function hasTelegramToken(): bool
    {
        return self::telegram()['token'] !== '';
    }

    private static function pref(string $settingKey, string $fallback): string
    {
        $v = Setting::get($settingKey, '');
        return $v !== '' ? $v : $fallback;
    }
}
