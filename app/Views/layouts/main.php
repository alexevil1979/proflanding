<?php
/** @var array $seo */
/** @var string $content */
$settings = $settings ?? [];
$pageTitle = $seo['title'] ?? (setting('site_name') . ' — ' . setting('site_role'));
$pageDesc = $seo['description'] ?? setting('site_tagline');
$ogTitle = $seo['og_title'] ?? $pageTitle;
$ogDesc = $seo['og_description'] ?? $pageDesc;
$ogImage = $seo['og_image'] ?? setting('og_image');
if ($ogImage && !str_starts_with($ogImage, 'http')) {
    $ogImage = app_url(ltrim($ogImage, '/'));
}
$canonical = $seo['canonical'] ?? app_url(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$robots = $seo['robots'] ?? 'index,follow';
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc) ?>">
    <meta name="robots" content="<?= e($robots) ?>">
    <link rel="canonical" href="<?= e($canonical) ?>">
    <meta property="og:locale" content="ru_RU">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($ogTitle) ?>">
    <meta property="og:description" content="<?= e($ogDesc) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <?php if ($ogImage): ?><meta property="og:image" content="<?= e($ogImage) ?>"><?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($ogTitle) ?>">
    <meta name="twitter:description" content="<?= e($ogDesc) ?>">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
      :root{--bg:#f4f7fb;--surface:#fff;--text:#1a2332;--muted:#5b6b7c;--accent:#0d9488;--accent-2:#0f766e;--line:#d8e0ea;--shadow:0 12px 40px rgba(26,35,50,.08)}
      html[data-theme="dark"]{--bg:#0f141c;--surface:#171e29;--text:#e8eef6;--muted:#9aabbd;--accent:#2dd4bf;--accent-2:#14b8a6;--line:#2a3545;--shadow:0 12px 40px rgba(0,0,0,.35)}
      body{margin:0;font-family:Manrope,system-ui,sans-serif;background:var(--bg);color:var(--text)}
    </style>
    <link rel="stylesheet" href="/assets/css/main.css">
    <?php if (!empty($settings['yandex_metrika'])): ?>
    <?= $settings['yandex_metrika'] /* admin-controlled snippet */ ?>
    <?php endif; ?>
</head>
<body>
<a class="skip-link" href="#main">К содержимому</a>
<header class="site-header" id="top">
    <div class="container header-inner">
        <a class="brand" href="/">
            <span class="brand-mark" aria-hidden="true"></span>
            <span class="brand-text">
                <strong><?= e(setting('site_name', 'IT Specialist')) ?></strong>
                <small><?= e(setting('site_role', 'IT-специалист')) ?></small>
            </span>
        </a>
        <nav class="nav" aria-label="Основная навигация">
            <a href="/#services">Услуги</a>
            <a href="/#packages">Пакеты</a>
            <a href="/#process">Как работаем</a>
            <a href="/#faq">FAQ</a>
            <a href="/#lead">Контакты</a>
        </nav>
        <div class="header-actions">
            <button type="button" class="theme-toggle" id="themeToggle" aria-label="Переключить тему">◐</button>
            <a class="btn btn-primary btn-sm" href="/#lead">Оставить заявку</a>
            <button type="button" class="nav-toggle" id="navToggle" aria-label="Меню" aria-expanded="false">☰</button>
        </div>
    </div>
</header>

<main id="main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <strong><?= e(setting('site_name')) ?></strong>
            <p><?= e(setting('site_tagline')) ?></p>
        </div>
        <div>
            <p><a href="tel:<?= e(preg_replace('/[^\d+]/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></p>
            <p><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></p>
            <p><?= e(setting('city')) ?></p>
        </div>
        <div class="footer-links">
            <a href="/privacy">Политика конфиденциальности</a>
            <a href="/offer">Публичная оферта</a>
        </div>
    </div>
    <div class="container footer-copy">© <?= date('Y') ?> <?= e(setting('site_name')) ?></div>
</footer>

<?php if (setting('telegram') || setting('whatsapp')): ?>
<div class="float-messengers" aria-label="Мессенджеры">
    <?php if (setting('whatsapp')): ?><a class="float-btn wa" href="<?= e(setting('whatsapp')) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">WA</a><?php endif; ?>
    <?php if (setting('telegram')): ?><a class="float-btn tg" href="<?= e(setting('telegram')) ?>" target="_blank" rel="noopener" aria-label="Telegram">TG</a><?php endif; ?>
</div>
<?php endif; ?>

<div class="toast" id="toast" hidden role="status" aria-live="polite"></div>
<script src="/assets/js/main.js" defer></script>
</body>
</html>
