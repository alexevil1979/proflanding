<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\NotificationLog;
use App\Models\Setting;
use App\Services\Notify;
use App\Services\NotifyConfig;

final class NotificationsController
{
    public function index(): void
    {
        Auth::requireLogin();
        $settings = Setting::all();
        $resolved = [
            'smtp' => NotifyConfig::smtp(),
            'telegram' => NotifyConfig::telegram(),
        ];
        // не светим пароль/токен целиком в HTML value — только маска
        View::render('admin/notifications/index', [
            'title' => 'Уведомления',
            'settings' => $settings,
            'resolved' => $resolved,
            'has_smtp_pass' => NotifyConfig::hasSmtpPass(),
            'has_tg_token' => NotifyConfig::hasTelegramToken(),
            'logs' => NotificationLog::recent(30),
            'flash_ok' => flash('ok'),
            'flash_error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        $pairs = [
            'telegram_enabled' => Request::input('telegram_enabled') ? '1' : '0',
            'mail_enabled' => Request::input('mail_enabled') ? '1' : '0',
            'notify_tpl_email_subject' => trim((string)Request::input('notify_tpl_email_subject', '')),
            'telegram_chat_id' => trim((string)Request::input('telegram_chat_id', '')),
            'smtp_host' => trim((string)Request::input('smtp_host', 'smtp.gmail.com')),
            'smtp_port' => trim((string)Request::input('smtp_port', '587')),
            'smtp_secure' => trim((string)Request::input('smtp_secure', 'tls')),
            'smtp_user' => trim((string)Request::input('smtp_user', '')),
            'smtp_from' => trim((string)Request::input('smtp_from', '')),
            'smtp_from_name' => trim((string)Request::input('smtp_from_name', '')),
            'smtp_to' => trim((string)Request::input('smtp_to', '')),
        ];

        $tgToken = trim((string)Request::input('telegram_bot_token', ''));
        if ($tgToken !== '') {
            $pairs['telegram_bot_token'] = $tgToken;
        }

        $smtpPass = (string)Request::input('smtp_pass', '');
        if ($smtpPass !== '') {
            $pairs['smtp_pass'] = $smtpPass;
        }

        Setting::setMany($pairs);
        flash('ok', 'Настройки уведомлений сохранены');
        redirect('/admin/notifications');
    }

    public function testTelegram(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        try {
            $result = Notify::testTelegram();
            if (Request::wantsJson()) {
                View::json(['ok' => $result['ok'], 'message' => $result['ok'] ? 'Telegram OK' : 'Ошибка', 'response' => $result['response']]);
                return;
            }
            flash($result['ok'] ? 'ok' : 'error', $result['ok'] ? 'Тест Telegram успешен' : ('Ошибка: ' . $result['response']));
        } catch (\Throwable $e) {
            if (Request::wantsJson()) {
                View::json(['ok' => false, 'message' => $e->getMessage()], 500);
                return;
            }
            flash('error', $e->getMessage());
        }
        redirect('/admin/notifications');
    }

    public function testEmail(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        try {
            $result = Notify::testEmail();
            if (Request::wantsJson()) {
                View::json(['ok' => $result['ok'], 'message' => $result['ok'] ? 'SMTP OK' : 'Ошибка', 'response' => $result['response']]);
                return;
            }
            flash($result['ok'] ? 'ok' : 'error', $result['ok'] ? 'Тест SMTP успешен' : ('Ошибка: ' . $result['response']));
        } catch (\Throwable $e) {
            if (Request::wantsJson()) {
                View::json(['ok' => false, 'message' => $e->getMessage()], 500);
                return;
            }
            flash('error', $e->getMessage());
        }
        redirect('/admin/notifications');
    }
}
