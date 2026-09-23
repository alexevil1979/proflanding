<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function __(string $key, ?string $default = null): string
{
    return \App\Core\Lang::get($key, $default);
}

function lang_url(string $path = '/'): string
{
    return \App\Core\Lang::url($path);
}

function current_lang(): string
{
    return \App\Core\Lang::code();
}

function money(?float $amount): string
{
    if ($amount === null) {
        return '';
    }
    return number_format($amount, 0, '.', ' ') . ' ₽';
}

/** @return array{rub:string,usd:string} */
function money_offer(?float $amount): array
{
    if ($amount === null) {
        return ['rub' => '', 'usd' => ''];
    }
    $rub = number_format($amount, 0, '.', ' ') . ' ₽';
    $usdVal = \App\Core\Currency::toUsd($amount);
    $usd = $usdVal !== null ? '~$' . number_format($usdVal, 2, '.', '') : '';
    return ['rub' => $rub, 'usd' => $usd];
}

function money_dual(?float $amount): string
{
    $o = money_offer($amount);
    if ($o['rub'] === '') {
        return '';
    }
    return $o['usd'] !== '' ? $o['rub'] . ' · ' . $o['usd'] : $o['rub'];
}

function period_label(string $period): string
{
    return match ($period) {
        'monthly' => __('period_monthly'),
        'custom' => __('period_custom'),
        default => __('period_one_time'),
    };
}

function brand_name(): string
{
    if (\App\Core\Lang::code() === 'ru') {
        return setting('site_name', 'Александр М.');
    }
    $fromLang = \App\Core\Lang::content('site_name');
    if ($fromLang !== '') {
        return $fromLang;
    }
    static $cache = null;
    if ($cache === null) {
        $cache = \App\Models\Setting::all();
    }
    $latin = trim((string)($cache['site_name_latin'] ?? ''));
    return $latin !== '' ? $latin : 'Alexander M.';
}

function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = \App\Models\Setting::all();
    }

    $fromAdminOnly = [
        'public_url', 'city', 'phone', 'email', 'telegram', 'whatsapp',
        'experience_years', 'projects_count', 'response_hours',
        'site_name', 'site_name_latin', 'yandex_metrika', 'google_analytics',
        'yandex_metrika_id', 'google_analytics_id', 'google_tag_manager_id',
        'yandex_verification', 'google_site_verification',
        'yandex_goal_lead', 'ga_event_lead', 'head_custom', 'body_custom',
        'index_locales',
        'usd_rate', 'usd_rate_updated_at', 'telegram_enabled', 'mail_enabled',
        'notify_tpl_email_subject', 'avatar_path', 'og_image',
        'work_format', 'response_sla', 'not_doing',
        'telegram_bot_token', 'telegram_chat_id',
        'telegram_proxy_enabled', 'telegram_proxy_type', 'telegram_proxy_host',
        'telegram_proxy_port', 'telegram_proxy_user', 'telegram_proxy_pass',
        'smtp_host', 'smtp_port', 'smtp_secure', 'smtp_user', 'smtp_pass',
        'smtp_from', 'smtp_from_name', 'smtp_to',
    ];

    if (!in_array($key, $fromAdminOnly, true)) {
        $translated = \App\Core\Lang::content($key);
        if ($translated !== '') {
            return $translated;
        }
    }

    return isset($cache[$key]) && $cache[$key] !== null && $cache[$key] !== ''
        ? (string)$cache[$key]
        : $default;
}

function service_field(array $service, string $field): string
{
    $slug = (string)($service['slug'] ?? '');
    $fallback = (string)($service[$field] ?? '');
    if ($slug === '') {
        return $fallback;
    }
    $t = \App\Core\Lang::content('services.' . $slug . '.' . $field, '');
    return $t !== '' ? $t : $fallback;
}

function package_field(array $package, string $field): string
{
    $titleRu = (string)($package['title'] ?? '');
    $fallback = (string)($package[$field] ?? '');
    $t = \App\Core\Lang::content('packages.' . $titleRu . '.' . $field, '');
    return $t !== '' ? $t : $fallback;
}

/** @return list<string> */
function package_features(array $package): array
{
    $titleRu = (string)($package['title'] ?? '');
    $raw = \App\Core\Lang::contentArray('packages.' . $titleRu . '.features');
    if (is_array($raw) && $raw !== []) {
        return array_map('strval', $raw);
    }
    return \App\Models\Package::featuresList($package['features'] ?? null);
}

function app_url(string $path = ''): string
{
    $canonical = 'https://bizdevops.site';
    $base = '';
    try {
        $pub = trim((string)\App\Models\Setting::get('public_url', ''));
        if ($pub !== '') {
            $base = rtrim($pub, '/');
        }
    } catch (\Throwable $e) {
    }
    if ($base === '') {
        $base = rtrim((string)\App\Core\Config::get('url', ''), '/');
    }
    if ($base === '' || preg_match('#1tlt\.ru#i', $base) || preg_match('#example\.com#i', $base)) {
        $base = $canonical;
    }
    $base = preg_replace('#^https?://(www\.)?bizdevops\.site#i', $canonical, $base) ?? $base;
    $base = rtrim($base, '/');
    if ($path === '' || $path === '/') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
}

function media_url(?string $path): string
{
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        if (preg_match('#1tlt\.ru#i', $path)) {
            return preg_replace('#https?://[^/]*1tlt\.ru#i', 'https://bizdevops.site', $path) ?? app_url('/');
        }
        return $path;
    }
    return app_url(ltrim($path, '/'));
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $v = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return is_string($v) ? $v : null;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function slugify(string $text): string
{
    $map = [
        'а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'zh','з'=>'z','и'=>'i','й'=>'y',
        'к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f',
        'х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'sch','ъ'=>'','ы'=>'y','ь'=>'','э'=>'e','ю'=>'yu','я'=>'ya',
    ];
    $text = mb_strtolower(trim($text));
    $text = strtr($text, $map);
    $text = preg_replace('~[^a-z0-9]+~', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

/** Убрать PHP из вставок аналитики (только HTML/JS из кабинетов). */
function analytics_sanitize_snippet(string $html): string
{
    $html = preg_replace('/<\?(?:php|=)?[\s\S]*?\?>/i', '', $html) ?? '';
    return trim($html);
}

/** @return list<string> */
function index_locales(): array
{
    $raw = setting('index_locales', 'ru,en,fa,zh,tr,ar');
    $parts = preg_split('/[\s,]+/', strtolower($raw)) ?: [];
    $allowed = \App\Core\Lang::codes();
    $out = [];
    foreach ($parts as $p) {
        if ($p !== '' && in_array($p, $allowed, true)) {
            $out[] = $p;
        }
    }
    return $out !== [] ? array_values(array_unique($out)) : ['ru'];
}

function locale_is_indexable(?string $code = null): bool
{
    $code = $code ?? \App\Core\Lang::code();
    return in_array($code, index_locales(), true);
}

function analytics_head_html(): string
{
    $chunks = [];
    $yv = setting('yandex_verification');
    if ($yv !== '') {
        $chunks[] = '<meta name="yandex-verification" content="' . e($yv) . '">';
    }
    $gv = setting('google_site_verification');
    if ($gv !== '') {
        $chunks[] = '<meta name="google-site-verification" content="' . e($gv) . '">';
    }

    $gtm = setting('google_tag_manager_id');
    if ($gtm !== '' && preg_match('/^GTM-[A-Z0-9]+$/i', $gtm)) {
        $chunks[] = "<!-- Google Tag Manager -->\n<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':"
            . "new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],"
            . "j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src="
            . "'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);"
            . "})(window,document,'script','dataLayer','" . e($gtm) . "');</script>\n<!-- End Google Tag Manager -->";
    }

    $gaFull = analytics_sanitize_snippet(setting('google_analytics'));
    $gaId = setting('google_analytics_id');
    if ($gaFull !== '') {
        $chunks[] = $gaFull;
    } elseif ($gaId !== '' && preg_match('/^G-[A-Z0-9]+$/i', $gaId) && $gtm === '') {
        $id = e($gaId);
        $chunks[] = '<script async src="https://www.googletagmanager.com/gtag/js?id=' . $id . '"></script>'
            . "\n<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}"
            . "gtag('js',new Date());gtag('config','" . $id . "');</script>";
    }

    $ymFull = analytics_sanitize_snippet(setting('yandex_metrika'));
    $ymId = setting('yandex_metrika_id');
    if ($ymFull !== '') {
        $chunks[] = $ymFull;
    } elseif ($ymId !== '' && preg_match('/^\d+$/', $ymId)) {
        $id = e($ymId);
        $chunks[] = '<script type="text/javascript">'
            . '(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};'
            . 'm[i].l=1*new Date();'
            . 'for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}'
            . 'k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})'
            . '(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");'
            . 'ym(' . $id . ', "init", {clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true});'
            . '</script>'
            . '<noscript><div><img src="https://mc.yandex.ru/watch/' . $id . '" style="position:absolute; left:-9999px;" alt="" /></div></noscript>';
    }

    $headCustom = analytics_sanitize_snippet(setting('head_custom'));
    if ($headCustom !== '') {
        $chunks[] = $headCustom;
    }

    return implode("\n", $chunks);
}

function analytics_body_open_html(): string
{
    $gtm = setting('google_tag_manager_id');
    if ($gtm === '' || !preg_match('/^GTM-[A-Z0-9]+$/i', $gtm)) {
        return '';
    }
    return '<!-- Google Tag Manager (noscript) -->'
        . '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . e($gtm) . '"'
        . ' height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>'
        . '<!-- End Google Tag Manager (noscript) -->';
}

function analytics_body_html(): string
{
    return analytics_sanitize_snippet(setting('body_custom'));
}
