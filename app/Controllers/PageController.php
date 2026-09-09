<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\PageSeo;
use App\Models\Setting;

final class PageController
{
    public function privacy(): void
    {
        $seo = PageSeo::get('privacy') ?? [
            'title' => 'Политика конфиденциальности',
            'description' => '',
            'h1' => 'Политика конфиденциальности',
        ];
        View::render('pages/privacy', [
            'seo' => $seo,
            'settings' => Setting::all(),
        ], 'layouts/main');
    }

    public function offer(): void
    {
        $seo = PageSeo::get('offer') ?? [
            'title' => 'Публичная оферта',
            'description' => '',
            'h1' => 'Публичная оферта',
        ];
        View::render('pages/offer', [
            'seo' => $seo,
            'settings' => Setting::all(),
        ], 'layouts/main');
    }
}
