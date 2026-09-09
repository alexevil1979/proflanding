<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Models\Package;
use App\Models\PageSeo;
use App\Models\Portfolio;
use App\Models\Service;
use App\Models\Setting;

final class HomeController
{
    public function index(): void
    {
        $seo = PageSeo::get('home') ?? [];
        $faq = json_decode(Setting::get('faq_json', '[]'), true);
        if (!is_array($faq)) {
            $faq = [];
        }

        View::render('home/index', [
            'seo' => $seo,
            'services' => Service::active(),
            'packages' => Package::active(),
            'portfolio' => Portfolio::active(),
            'faq' => $faq,
            'settings' => Setting::all(),
        ], 'layouts/main');
    }
}
