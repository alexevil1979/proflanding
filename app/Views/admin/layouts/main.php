<?php /** @var string $content */ /** @var string $title */ ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e($title ?? 'Админка') ?> — Proflanding</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<aside class="admin-nav">
    <div class="admin-brand">Proflanding</div>
    <a href="/admin">Дашборд</a>
    <a href="/admin/leads">Заявки</a>
    <a href="/admin/services">Услуги</a>
    <a href="/admin/packages">Пакеты</a>
    <a href="/admin/settings">Контент</a>
    <a href="/admin/seo">SEO</a>
    <a href="/admin/notifications">Уведомления</a>
    <a href="/admin/password">Пароль</a>
    <a href="/" target="_blank" rel="noopener">Открыть сайт</a>
    <form method="post" action="/admin/logout" class="logout-form">
        <?= \App\Core\Csrf::field() ?>
        <button type="submit">Выйти</button>
    </form>
</aside>
<div class="admin-main">
    <header class="admin-top"><h1><?= e($title ?? '') ?></h1></header>
    <?php if (!empty($flash_ok)): ?><div class="alert ok"><?= e($flash_ok) ?></div><?php endif; ?>
    <?php if (!empty($flash_error)): ?><div class="alert err"><?= e($flash_error) ?></div><?php endif; ?>
    <?= $content ?>
</div>
</body>
</html>
