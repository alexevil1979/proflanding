<?php
/** @var array $seo */
/** @var string $content */
use App\Core\Lang;

$settings = $settings ?? [];
$pageTitle = $seo['title'] ?? (brand_name() . ' — ' . setting('site_role'));
$pageDesc = $seo['description'] ?? setting('site_tagline');
$ogTitle = $seo['og_title'] ?? $pageTitle;
$ogDesc = $seo['og_description'] ?? $pageDesc;
$ogImage = media_url($seo['og_image'] ?? setting('og_image') ?: setting('avatar_path'));
$pathNoLang = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonical = $seo['canonical'] ?? Lang::absoluteUrl($pathNoLang === '/' ? '/' : $pathNoLang);
$robots = $seo['robots'] ?? 'index,follow';
$lang = Lang::code();
$homePath = ($pathNoLang === '/' || $pathNoLang === '') ? '/' : $pathNoLang;
?><!DOCTYPE html>
<html lang="<?= e(Lang::htmlLang()) ?>" dir="<?= e(Lang::dir()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc) ?>">
    <meta name="robots" content="<?= e($robots) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <?php foreach (Lang::codes() as $code):
        if ($homePath === '/') {
            $href = $code === Lang::DEFAULT ? app_url('/') : app_url($code . '/');
        } else {
            $href = $code === Lang::DEFAULT ? app_url(ltrim($homePath, '/')) : app_url($code . '/' . ltrim($homePath, '/'));
        }
        $hl = $code === 'zh' ? 'zh-Hans' : $code;
    ?>
    <link rel="alternate" hreflang="<?= e($hl) ?>" href="<?= e($href) ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= e(app_url('/')) ?>">
    <meta property="og:locale" content="<?= e(Lang::ogLocale()) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($ogTitle) ?>">
    <meta property="og:description" content="<?= e($ogDesc) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php if ($ogImage): ?>
    <meta property="og:image" content="<?= e($ogImage) ?>">
    <meta name="twitter:image" content="<?= e($ogImage) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($ogTitle) ?>">
    <meta name="twitter:description" content="<?= e($ogDesc) ?>">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="stylesheet" href="/assets/css/main.css">
    <?php if (!empty($settings['yandex_metrika'])): ?>
    <?= $settings['yandex_metrika'] ?>
    <?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main"><?= e(__('skip_to_content')) ?></a>
<header class="site-header" id="top">
    <div class="container header-inner">
        <a class="brand" href="<?= e(lang_url('/')) ?>">
            <span class="brand-mark" aria-hidden="true"></span>
            <span class="brand-text">
                <strong><bdi dir="ltr"><?= e(brand_name()) ?></bdi></strong>
                <small class="brand-role"><?= e(setting('site_role')) ?></small>
            </span>
        </a>

        <nav class="nav" id="siteNav" aria-label="<?= e(__('nav_aria')) ?>">
            <a href="<?= e(lang_url('/#services')) ?>"><?= e(__('nav_services')) ?></a>
            <a href="<?= e(lang_url('/#packages')) ?>"><?= e(__('nav_packages')) ?></a>
            <a href="<?= e(lang_url('/#process')) ?>"><?= e(__('nav_process')) ?></a>
            <a href="<?= e(lang_url('/#faq')) ?>"><?= e(__('nav_faq')) ?></a>
            <a href="<?= e(lang_url('/#lead')) ?>"><?= e(__('nav_contacts')) ?></a>
            <div class="nav-mobile-extra">
                <div class="lang-switch" aria-label="<?= e(__('lang_label')) ?>">
                    <?php foreach (Lang::LOCALES as $code => $meta):
                        if ($pathNoLang !== '/' && $pathNoLang !== '') {
                            $url = $code === Lang::DEFAULT ? $pathNoLang : '/' . $code . $pathNoLang;
                        } else {
                            $url = $code === Lang::DEFAULT ? '/' : '/' . $code . '/';
                        }
                    ?>
                    <a class="lang-link<?= $code === $lang ? ' is-active' : '' ?>" href="<?= e($url) ?>" hreflang="<?= e($code === 'zh' ? 'zh-Hans' : $code) ?>"><?= e(strtoupper($code)) ?></a>
                    <?php endforeach; ?>
                </div>
                <a class="btn btn-primary btn-sm nav-cta-mobile" href="<?= e(lang_url('/#lead')) ?>"><?= e(__('cta_lead')) ?></a>
            </div>
        </nav>

        <div class="header-actions">
            <div class="lang-switch lang-desktop" aria-label="<?= e(__('lang_label')) ?>">
                <?php foreach (Lang::LOCALES as $code => $meta):
                    if ($pathNoLang !== '/' && $pathNoLang !== '') {
                        $url = $code === Lang::DEFAULT ? $pathNoLang : '/' . $code . $pathNoLang;
                    } else {
                        $url = $code === Lang::DEFAULT ? '/' : '/' . $code . '/';
                    }
                ?>
                <a class="lang-link<?= $code === $lang ? ' is-active' : '' ?>" href="<?= e($url) ?>" hreflang="<?= e($code === 'zh' ? 'zh-Hans' : $code) ?>"><?= e(strtoupper($code)) ?></a>
                <?php endforeach; ?>
            </div>
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="<?= e(__('theme_toggle')) ?>">◐</button>
            <a class="btn btn-primary btn-sm header-cta-desktop" href="<?= e(lang_url('/#lead')) ?>"><?= e(__('cta_lead')) ?></a>
            <button type="button" class="nav-toggle" id="navToggle" aria-label="<?= e(__('menu')) ?>" aria-expanded="false" aria-controls="siteNav">☰</button>
        </div>
    </div>
</header>

<main id="main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong><bdi dir="ltr"><?= e(brand_name()) ?></bdi></strong>
            <p><?= e(setting('site_tagline')) ?></p>
        </div>
        <div>
            <p><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
            <p><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
            <p><?= e(setting('city')) ?></p>
        </div>
        <div class="footer-links">
            <a href="<?= e(lang_url('/privacy')) ?>"><?= e(__('footer_privacy')) ?></a>
            <a href="<?= e(lang_url('/offer')) ?>"><?= e(__('footer_offer')) ?></a>
        </div>
    </div>
    <div class="container footer-copy">© <?= date('Y') ?> <bdi dir="ltr"><?= e(brand_name()) ?></bdi></div>
</footer>

<?php if (setting('telegram') || setting('whatsapp')): ?>
<div class="float-messengers" aria-label="<?= e(__('messengers')) ?>">
    <?php if (setting('whatsapp')): ?><a class="float-btn wa" href="<?= e(setting('whatsapp')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">WA</a><?php endif; ?>
    <?php if (setting('telegram')): ?><a class="float-btn tg" href="<?= e(setting('telegram')) ?>" target="_blank" rel="noopener" aria-label="Telegram">TG</a><?php endif; ?>
</div>
<?php endif; ?>

<div class="toast" id="toast" hidden role="status" aria-live="polite"></div>
<script>
window.PL_I18N = {
  leadOk: <?= json_encode(__('lead_ok'), JSON_UNESCAPED_UNICODE) ?>,
  leadErr: <?= json_encode(__('lead_err'), JSON_UNESCAPED_UNICODE) ?>,
  leadNetwork: <?= json_encode(__('lead_network'), JSON_UNESCAPED_UNICODE) ?>,
  leadUrl: <?= json_encode(lang_url('/lead'), JSON_UNESCAPED_UNICODE) ?>,
  submitting: <?= json_encode(__('submitting', 'Отправка…'), JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script src="/assets/js/main.js" defer></script>
</body>
</html>
