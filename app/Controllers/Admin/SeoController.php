<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\View;
use App\Models\PageSeo;

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
            'flash_ok' => flash('ok'),
        ], 'admin/layouts/main');
    }

    public function update(): void
    {
        Auth::requireLogin();
        Csrf::requireValid();
        $pages = Request::input('pages', []);
        if (!is_array($pages)) {
            redirect('/admin/seo');
        }
        foreach ($pages as $key => $data) {
            if (!is_array($data)) {
                continue;
            }
            PageSeo::upsert((string)$key, [
                'title' => trim((string)($data['title'] ?? '')),
                'description' => trim((string)($data['description'] ?? '')),
                'h1' => trim((string)($data['h1'] ?? '')),
                'og_title' => trim((string)($data['og_title'] ?? '')) ?: null,
                'og_description' => trim((string)($data['og_description'] ?? '')) ?: null,
                'og_image' => trim((string)($data['og_image'] ?? '')) ?: null,
                'canonical' => trim((string)($data['canonical'] ?? '')) ?: null,
                'robots' => trim((string)($data['robots'] ?? 'index,follow')) ?: 'index,follow',
            ]);
        }
        flash('ok', 'SEO сохранено');
        redirect('/admin/seo');
    }
}
