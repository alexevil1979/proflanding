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
        $this->renderLegal('privacy', __('footer_privacy'));
    }

    public function offer(): void
    {
        $this->renderLegal('offer', __('footer_offer'));
    }

    private function renderLegal(string $key, string $fallbackTitle): void
    {
        $seo = PageSeo::get($key) ?? [];
        if (trim((string)($seo['title'] ?? '')) === '') {
            $seo['title'] = $fallbackTitle . ' — ' . brand_name();
        }
        if (trim((string)($seo['h1'] ?? '')) === '') {
            $seo['h1'] = $fallbackTitle;
        }
        if (trim((string)($seo['description'] ?? '')) === '') {
            $seo['description'] = setting('site_tagline');
        }
        View::render('pages/' . $key, [
            'seo' => $seo,
            'settings' => Setting::all(),
        ], 'layouts/main');
    }
}
