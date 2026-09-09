<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function money(?float $amount): string
{
    if ($amount === null) {
        return '';
    }
    return number_format($amount, 0, '.', ' ') . ' ₽';
}

function period_label(string $period): string
{
    return match ($period) {
        'monthly' => 'в месяц',
        'custom' => 'по согласованию',
        default => 'разово',
    };
}

function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = \App\Models\Setting::all();
    }
    return isset($cache[$key]) && $cache[$key] !== null && $cache[$key] !== ''
        ? (string)$cache[$key]
        : $default;
}

function app_url(string $path = ''): string
{
    $base = rtrim((string)\App\Core\Config::get('url', ''), '/');
    if ($base === '') {
        $scheme = \App\Core\Request::isHttps() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = $scheme . '://' . $host;
    }
    if ($path === '' || $path === '/') {
        return $base . '/';
    }
    return $base . '/' . ltrim($path, '/');
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
