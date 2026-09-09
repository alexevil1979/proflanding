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

final class NotificationsController
{
    public function index(): void
    {
        Auth::requireLogin();
        View::render('admin/notifications/index', [
            'title' => 'Уведомления',
            'settings' => Setting::all(),
            'logs' => NotificationLog::recent(30),
            'flash_ok' => flash('ok'),
            'flash_error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        Setting::setMany([
            'telegram_enabled' => Request::input('telegram_enabled') ? '1' : '0',
            'mail_enabled' => Request::input('mail_enabled') ? '1' : '0',
            'notify_tpl_email_subject' => trim((string)Request::input('notify_tpl_email_subject', '')),
        ]);
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
