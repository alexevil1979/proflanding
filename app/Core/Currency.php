<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Setting;

/**
 * Курс USD по ЦБ РФ (кэш в settings). Цены в БД — рубли.
 */
final class Currency
{
    private const CACHE_KEY_RATE = 'usd_rate';
    private const CACHE_KEY_AT = 'usd_rate_updated_at';
    private const CACHE_TTL = 21600; // 6 часов

    public static function usdRate(): float
    {
        $rate = (float)Setting::get(self::CACHE_KEY_RATE, '0');
        $updated = Setting::get(self::CACHE_KEY_AT, '');
        $stale = $updated === '' || (time() - strtotime($updated)) > self::CACHE_TTL;

        if ($rate <= 0 || $stale) {
            try {
                self::refreshFromCbr();
                $rate = (float)Setting::get(self::CACHE_KEY_RATE, '0');
            } catch (\Throwable $e) {
                Logger::error('USD rate refresh failed', ['message' => $e->getMessage()]);
            }
        }

        if ($rate <= 0) {
            $rate = 90.0; // безопасный fallback
        }
        return $rate;
    }

    public static function toUsd(?float $rub): ?float
    {
        if ($rub === null) {
            return null;
        }
        $rate = self::usdRate();
        if ($rate <= 0) {
            return null;
        }
        return round($rub / $rate, 2);
    }

    public static function formatRubUsd(?float $rub): string
    {
        if ($rub === null) {
            return '';
        }
        $rubStr = number_format($rub, 0, '.', ' ') . ' ₽';
        $usd = self::toUsd($rub);
        if ($usd === null) {
            return $rubStr;
        }
        return $rubStr . ' · ~$' . number_format($usd, 2, '.', '');
    }

    public static function refreshFromCbr(): array
    {
        $url = 'https://www.cbr-xml-daily.ru/daily_json.js';
        $ctx = stream_context_create([
            'http' => ['timeout' => 8, 'ignore_errors' => true, 'header' => "Accept: application/json\r\n"],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);
        $raw = @file_get_contents($url, false, $ctx);
        if ($raw === false) {
            throw new \RuntimeException('Не удалось получить курс ЦБ');
        }
        $json = json_decode($raw, true);
        $value = $json['Valute']['USD']['Value'] ?? null;
        if (!is_numeric($value) || (float)$value <= 0) {
            throw new \RuntimeException('В ответе ЦБ нет USD');
        }
        $rate = round((float)$value, 4);
        Setting::setMany([
            self::CACHE_KEY_RATE => (string)$rate,
            self::CACHE_KEY_AT => date('Y-m-d H:i:s'),
        ]);
        return ['ok' => true, 'rate' => $rate];
    }

    public static function setManual(float $rate): void
    {
        if ($rate <= 0) {
            throw new \InvalidArgumentException('Курс должен быть > 0');
        }
        Setting::setMany([
            self::CACHE_KEY_RATE => (string)round($rate, 4),
            self::CACHE_KEY_AT => date('Y-m-d H:i:s'),
        ]);
    }
}
