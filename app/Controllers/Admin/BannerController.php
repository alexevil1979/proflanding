<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Lang;
use App\Core\Request;
use App\Core\View;
use App\Models\Setting;

final class BannerController
{
    public const STYLES = [
        1 => 'Teal shimmer — бирюзовый градиент',
        2 => 'Midnight gold — тёмный с золотом',
        3 => 'Coral pulse — коралловый пульс',
        4 => 'Forest glass — изумрудное стекло',
        5 => 'Electric stripe — полосатый акцент',
    ];

    public function edit(): void
    {
        Auth::requireLogin();
        $texts = promo_banner_texts_all();
        View::render('admin/banner/edit', [
            'title' => 'Промо-баннер',
            'settings' => Setting::all(),
            'texts' => $texts,
            'styles' => self::STYLES,
            'locales' => Lang::LOCALES,
            'flash_ok' => flash('ok'),
            'flash_error' => flash('error'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();

        $style = (int)Request::input('promo_banner_style', 1);
        if ($style < 1 || $style > 5) {
            $style = 1;
        }

        $enabled = Request::input('promo_banner_enabled') ? '1' : '0';
        $dismissible = Request::input('promo_banner_dismissible') ? '1' : '0';
        $ctaUrl = trim((string)Request::input('promo_banner_cta_url', '#lead'));
        if ($ctaUrl === '') {
            $ctaUrl = '#lead';
        }
        $until = trim((string)Request::input('promo_banner_until', ''));
        if ($until !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $until)) {
            flash('error', 'Дата окончания в формате YYYY-MM-DD');
            redirect('/admin/banner');
        }

        $rawTexts = Request::input('texts', []);
        $texts = [];
        if (is_array($rawTexts)) {
            foreach (Lang::codes() as $code) {
                $row = is_array($rawTexts[$code] ?? null) ? $rawTexts[$code] : [];
                $title = trim((string)($row['title'] ?? ''));
                $sub = trim((string)($row['sub'] ?? ''));
                $cta = trim((string)($row['cta'] ?? ''));
                if ($title === '' && $sub === '' && $cta === '') {
                    continue;
                }
                $texts[$code] = [
                    'title' => mb_substr($title, 0, 120),
                    'sub' => mb_substr($sub, 0, 200),
                    'cta' => mb_substr($cta !== '' ? $cta : 'OK', 0, 60),
                ];
            }
        }

        Setting::setMany([
            'promo_banner_enabled' => $enabled,
            'promo_banner_style' => (string)$style,
            'promo_banner_dismissible' => $dismissible,
            'promo_banner_cta_url' => $ctaUrl,
            'promo_banner_until' => $until,
            'promo_banner_texts' => json_encode($texts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);

        flash('ok', 'Баннер сохранён');
        redirect('/admin/banner');
    }
}
