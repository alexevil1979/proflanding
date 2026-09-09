<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\Setting;

final class SettingsController
{
    public function edit(): void
    {
        Auth::requireLogin();
        View::render('admin/settings/edit', [
            'title' => 'Контент сайта',
            'settings' => Setting::all(),
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $keys = [
            'site_name', 'site_role', 'site_tagline', 'hero_offer', 'hero_sub',
            'phone', 'email', 'telegram', 'whatsapp', 'city',
            'experience_years', 'projects_count', 'response_hours',
            'yandex_metrika', 'google_analytics', 'faq_json',
        ];
        $pairs = [];
        foreach ($keys as $key) {
            $pairs[$key] = trim((string)Request::input($key, ''));
        }
        Setting::setMany($pairs);
        flash('ok', 'Настройки сохранены');
        redirect('/admin/settings');
    }
}
