<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Logger;
use App\Core\Mailer;
use App\Core\Telegram;
use App\Models\Lead;
use App\Models\NotificationLog;
use App\Models\Setting;

final class Notify
{
    public static function leadCreated(int $leadId): void
    {
        $lead = Lead::find($leadId);
        if (!$lead) {
            return;
        }

        $tgEnabled = Setting::get('telegram_enabled', Config::get('telegram.enabled') ? '1' : '0') === '1'
            && Config::get('telegram.enabled');
        $mailEnabled = Setting::get('mail_enabled', Config::get('mail_enabled') ? '1' : '0') === '1'
            && Config::get('mail_enabled');

        if ($tgEnabled) {
            self::sendTelegram($lead);
        }
        if ($mailEnabled) {
            self::sendEmail($lead);
        }
    }

    public static function sendTelegram(array $lead): array
    {
        $text = self::telegramText($lead);
        $result = (new Telegram())->sendMessage($text);
        NotificationLog::add(
            (int)$lead['id'],
            'telegram',
            $result['ok'] ? 'ok' : 'fail',
            (string)$result['response']
        );
        if (!$result['ok']) {
            Logger::error('Telegram notify failed', ['lead_id' => $lead['id'], 'response' => $result['response']]);
        }
        return $result;
    }

    public static function sendEmail(array $lead): array
    {
        $to = (string)Config::get('smtp.to', '');
        $subjectTpl = Setting::get('notify_tpl_email_subject', 'Новая заявка с лендинга #{id}');
        $subject = str_replace('{id}', (string)$lead['id'], $subjectTpl);
        $html = self::emailHtml($lead);
        $text = self::emailText($lead);
        $result = (new Mailer())->send($to, $subject, $html, $text);
        NotificationLog::add(
            (int)$lead['id'],
            'email',
            $result['ok'] ? 'ok' : 'fail',
            (string)$result['response']
        );
        if (!$result['ok']) {
            Logger::error('Email notify failed', ['lead_id' => $lead['id'], 'response' => $result['response']]);
        }
        return $result;
    }

    public static function testTelegram(): array
    {
        $result = (new Telegram())->sendMessage(
            "✅ Тест Telegram-уведомлений\nСайт: " . e(app_url()) . "\nВремя: " . date('d.m.Y H:i')
        );
        NotificationLog::add(null, 'telegram', $result['ok'] ? 'ok' : 'fail', (string)$result['response']);
        return $result;
    }

    public static function testEmail(): array
    {
        $to = (string)Config::get('smtp.to', '');
        $html = '<p>Тестовое письмо с лендинга IT-специалиста.</p><p>Время: ' . date('d.m.Y H:i') . '</p>';
        $result = (new Mailer())->send($to, 'Тест SMTP с лендинга', $html, 'Тестовое письмо');
        NotificationLog::add(null, 'email', $result['ok'] ? 'ok' : 'fail', (string)$result['response']);
        return $result;
    }

    private static function telegramText(array $lead): string
    {
        $lines = [
            '<b>Новая заявка #' . (int)$lead['id'] . '</b>',
            'Имя: ' . self::esc($lead['name'] ?? ''),
            'Телефон: ' . self::esc($lead['phone'] ?? ''),
            'Email: ' . self::esc($lead['email'] ?? '—'),
            'Мессенджер: ' . self::esc($lead['messenger'] ?? '—'),
            'Услуга: ' . self::esc($lead['service_title'] ?? '—'),
            'Пакет: ' . self::esc($lead['package_title'] ?? '—'),
            'Сообщение: ' . self::esc($lead['message'] ?? '—'),
            'Страница: ' . self::esc($lead['page_url'] ?? '—'),
            'UTM: ' . self::esc(trim(implode(' / ', array_filter([
                $lead['utm_source'] ?? null,
                $lead['utm_medium'] ?? null,
                $lead['utm_campaign'] ?? null,
            ]))) ?: '—'),
            'Время: ' . self::esc($lead['created_at'] ?? date('Y-m-d H:i:s')),
        ];
        return implode("\n", $lines);
    }

    private static function emailHtml(array $lead): string
    {
        $rows = [
            'ID' => (string)$lead['id'],
            'Имя' => (string)($lead['name'] ?? ''),
            'Телефон' => (string)($lead['phone'] ?? ''),
            'Email' => (string)($lead['email'] ?? '—'),
            'Мессенджер' => (string)($lead['messenger'] ?? '—'),
            'Услуга' => (string)($lead['service_title'] ?? '—'),
            'Пакет' => (string)($lead['package_title'] ?? '—'),
            'Сообщение' => nl2br(e((string)($lead['message'] ?? '—'))),
            'Страница' => (string)($lead['page_url'] ?? '—'),
            'IP' => (string)($lead['ip'] ?? '—'),
            'Время' => (string)($lead['created_at'] ?? ''),
        ];
        $html = '<h2>Новая заявка #' . (int)$lead['id'] . '</h2><table cellpadding="6">';
        foreach ($rows as $k => $v) {
            $html .= '<tr><td><b>' . e($k) . '</b></td><td>' . (str_contains($k, 'Сообщение') ? $v : e($v)) . '</td></tr>';
        }
        $html .= '</table>';
        return $html;
    }

    private static function emailText(array $lead): string
    {
        return strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", self::emailHtml($lead)));
    }

    private static function esc(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
