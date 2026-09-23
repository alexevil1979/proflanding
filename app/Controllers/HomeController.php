<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Lang;
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
        if (Lang::code() !== 'ru') {
            $t = Lang::content('seo.home.title', '');
            $d = Lang::content('seo.home.description', '');
            if ($t !== '') {
                $seo['title'] = $t;
            }
            if ($d !== '') {
                $seo['description'] = $d;
            }
            $seo['h1'] = setting('hero_offer') ?: ($seo['h1'] ?? '');
            $seo['og_title'] = $seo['title'] ?: ($seo['og_title'] ?? null);
            $seo['og_description'] = $seo['description'] ?: ($seo['og_description'] ?? null);
        }
        if (!empty($seo['title']) && mb_strlen((string)$seo['title']) > 60) {
            $seo['title'] = mb_substr((string)$seo['title'], 0, 60);
        }
        if (!empty($seo['description']) && mb_strlen((string)$seo['description']) > 160) {
            $seo['description'] = mb_substr((string)$seo['description'], 0, 160);
        }

        $faqRu = json_decode(Setting::get('faq_json', '[]'), true);
        if (!is_array($faqRu)) {
            $faqRu = [];
        }

        View::render('home/index', [
            'seo' => $seo,
            'services' => Service::active(),
            'packages' => Package::active(),
            'portfolio' => Portfolio::active(),
            'faq' => Lang::faq($faqRu),
            'settings' => Setting::all(),
            'usdRate' => \App\Core\Currency::usdRate(),
        ], 'layouts/main');
    }
}
