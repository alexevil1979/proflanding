<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Lang;
use App\Core\Request;
use App\Core\View;
use App\Models\PageSeo;
use App\Models\Setting;

final class SeoController
{
    public function edit(): void
    {
        Auth::requireLogin();
        $rows = PageSeo::all();
        $byKey = [];
        foreach ($rows as $row) {
            $byKey[$row['page_key']] = $row;
        }
        View::render('admin/seo/edit', [
            'title' => 'SEO',
            'pages' => $byKey,
            'index_locales' => setting('index_locales', 'ru,en,fa,zh,tr,ar'),
            'locales' => Lang::LOCALES,
            'checklist' => $this->checklist(),
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $pages = Request::input('pages', []);
        if (is_array($pages)) {
            foreach ($pages as $key => $data) {
                if (!is_array($data)) {
                    continue;
                }
                $title = trim((string)($data['title'] ?? ''));
                $desc = trim((string)($data['description'] ?? ''));
                if (mb_strlen($title) > 60) {
                    $title = mb_substr($title, 0, 60);
                }
                if (mb_strlen($desc) > 160) {
                    $desc = mb_substr($desc, 0, 160);
                }
                PageSeo::upsert((string)$key, [
                    'title' => $title,
                    'description' => $desc,
                    'h1' => trim((string)($data['h1'] ?? '')),
                    'og_title' => trim((string)($data['og_title'] ?? '')) ?: null,
                    'og_description' => trim((string)($data['og_description'] ?? '')) ?: null,
                    'og_image' => trim((string)($data['og_image'] ?? '')) ?: null,
                    'canonical' => trim((string)($data['canonical'] ?? '')) ?: null,
                    'robots' => trim((string)($data['robots'] ?? 'index,follow')) ?: 'index,follow',
                ]);
            }
        }

        $selected = Request::input('index_locales', []);
        if (!is_array($selected)) {
            $selected = [];
        }
        $allowed = Lang::codes();
        $clean = [];
        foreach ($selected as $code) {
            $code = strtolower(trim((string)$code));
            if (in_array($code, $allowed, true)) {
                $clean[] = $code;
            }
        }
        if ($clean === []) {
            $clean = ['ru'];
        }
        if (!in_array('ru', $clean, true)) {
            array_unshift($clean, 'ru');
        }
        Setting::setMany(['index_locales' => implode(',', array_unique($clean))]);

        flash('ok', 'SEO сохранено');
        redirect('/admin/seo');
    }

    /** @return list<array{ok:bool,label:string}> */
    private function checklist(): array
    {
        $home = PageSeo::get('home') ?? [];
        $pub = setting('public_url', 'https://bizdevops.site');
        return [
            ['ok' => str_contains($pub, 'bizdevops.site'), 'label' => 'Каноникал public_url = bizdevops.site'],
            ['ok' => trim((string)($home['title'] ?? '')) !== '' && mb_strlen((string)$home['title']) <= 60, 'label' => 'Title главной ≤ 60 символов'],
            ['ok' => trim((string)($home['description'] ?? '')) !== '' && mb_strlen((string)$home['description']) <= 160, 'label' => 'Description главной ≤ 160'],
            ['ok' => setting('og_image') !== '' || setting('avatar_path') !== '', 'label' => 'OG image / фото специалиста загружены'],
            ['ok' => setting('email') !== '' && !str_contains(setting('email'), 'example.com'), 'label' => 'Контактный email без example.com'],
            ['ok' => setting('yandex_metrika_id') !== '' || setting('yandex_metrika') !== '', 'label' => 'Яндекс.Метрика подключена'],
            ['ok' => setting('google_analytics_id') !== '' || setting('google_analytics') !== '' || setting('google_tag_manager_id') !== '', 'label' => 'Google Analytics / GTM'],
            ['ok' => setting('yandex_verification') !== '', 'label' => 'Верификация Яндекс.Вебмастер'],
            ['ok' => setting('google_site_verification') !== '', 'label' => 'Верификация Google Search Console'],
        ];
    }
}
