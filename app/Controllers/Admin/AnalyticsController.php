<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\Setting;

final class AnalyticsController
{
    private const KEYS = [
        'yandex_metrika_id',
        'yandex_metrika',
        'yandex_verification',
        'yandex_goal_lead',
        'google_analytics_id',
        'google_analytics',
        'google_tag_manager_id',
        'google_site_verification',
        'ga_event_lead',
        'head_custom',
        'body_custom',
    ];

    public function edit(): void
    {
        Auth::requireLogin();
        View::render('admin/analytics/edit', [
            'title' => 'Аналитика и верификация',
            'settings' => Setting::all(),
            'flash_ok' => flash('ok'),
            'flash_error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $pairs = [];
        foreach (self::KEYS as $key) {
            $val = trim((string)Request::input($key, ''));
            if (in_array($key, ['yandex_metrika_id', 'google_analytics_id', 'google_tag_manager_id', 'yandex_verification', 'google_site_verification'], true)) {
                $val = preg_replace('/[^A-Za-z0-9_\-.]/', '', $val) ?? '';
            }
            if (in_array($key, ['yandex_metrika', 'google_analytics', 'head_custom', 'body_custom'], true)) {
                $val = analytics_sanitize_snippet($val);
            }
            $pairs[$key] = $val;
        }
        if ($pairs['yandex_goal_lead'] === '') {
            $pairs['yandex_goal_lead'] = 'lead';
        }
        if ($pairs['ga_event_lead'] === '') {
            $pairs['ga_event_lead'] = 'generate_lead';
        }
        Setting::setMany($pairs);
        flash('ok', 'Настройки аналитики сохранены');
        redirect('/admin/analytics');
    }
}
