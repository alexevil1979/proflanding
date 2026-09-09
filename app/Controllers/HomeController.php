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
        // SEO title/description from content pack when not RU
        if (Lang::code() !== 'ru') {
            $seo['title'] = Lang::content('seo.home.title', $seo['title'] ?? '');
            $seo['description'] = Lang::content('seo.home.description', $seo['description'] ?? '');
            $seo['h1'] = setting('hero_offer');
            $seo['og_title'] = $seo['title'] ?: ($seo['og_title'] ?? null);
            $seo['og_description'] = $seo['description'] ?: ($seo['og_description'] ?? null);
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
