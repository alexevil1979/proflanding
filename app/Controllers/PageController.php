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
        $seo = PageSeo::get('privacy') ?? [];
        $seo['title'] = __('footer_privacy');
        $seo['h1'] = __('footer_privacy');
        View::render('pages/privacy', [
            'seo' => $seo,
            'settings' => Setting::all(),
        ], 'layouts/main');
    }

    public function offer(): void
    {
        $seo = PageSeo::get('offer') ?? [];
        $seo['title'] = __('footer_offer');
        $seo['h1'] = __('footer_offer');
        View::render('pages/offer', [
            'seo' => $seo,
            'settings' => Setting::all(),
        ], 'layouts/main');
    }
}
