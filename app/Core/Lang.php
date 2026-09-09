<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Языки как на hiddifysales.com: ru, en, fa, zh, tr, ar.
 * URL: / = ru, /en, /fa, /zh, /tr, /ar (+ /ru → редирект на /).
 */
final class Lang
{
    public const DEFAULT = 'ru';

    /** @var array<string, array{name:string,native:string,locale:string,dir:string,og:string}> */
    public const LOCALES = [
        'ru' => ['name' => 'Russian', 'native' => 'Русский', 'locale' => 'ru_RU', 'dir' => 'ltr', 'og' => 'ru_RU'],
        'en' => ['name' => 'English', 'native' => 'English', 'locale' => 'en_US', 'dir' => 'ltr', 'og' => 'en_US'],
        'fa' => ['name' => 'Persian', 'native' => 'فارسی', 'locale' => 'fa_IR', 'dir' => 'rtl', 'og' => 'fa_IR'],
        'zh' => ['name' => 'Chinese', 'native' => '中文', 'locale' => 'zh_CN', 'dir' => 'ltr', 'og' => 'zh_CN'],
        'tr' => ['name' => 'Turkish', 'native' => 'Türkçe', 'locale' => 'tr_TR', 'dir' => 'ltr', 'og' => 'tr_TR'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'locale' => 'ar_SA', 'dir' => 'rtl', 'og' => 'ar_SA'],
    ];

    private static string $code = self::DEFAULT;
    /** @var array<string, string> */
    private static array $messages = [];
    /** @var array<string, mixed> */
    private static array $content = [];

    public static function boot(string $root): void
    {
        $uri = Request::uri();
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);

        $detected = self::DEFAULT;
        if (preg_match('#^/(en|ru|fa|zh|tr|ar)(/.*)?$#u', $path, $m)) {
            $detected = $m[1];
            $rest = $m[2] ?? '/';
            if ($rest === '') {
                $rest = '/';
            }
            $query = parse_url($uri, PHP_URL_QUERY);
            $newUri = $rest . ($query ? ('?' . $query) : '');
            $_SERVER['REQUEST_URI'] = $newUri;
            $_SERVER['PROFLANDING_LANG'] = $detected;

            // /ru и /ru/... → каноникал без префикса
            if ($detected === self::DEFAULT) {
                $target = $rest === '/' ? '/' : $rest;
                if ($query) {
                    $target .= '?' . $query;
                }
                redirect($target);
            }
        }

        self::$code = $detected;
        self::loadFiles($root, $detected);
    }

    public static function code(): string
    {
        return self::$code;
    }

    public static function isRtl(): bool
    {
        return (self::LOCALES[self::$code]['dir'] ?? 'ltr') === 'rtl';
    }

    public static function htmlLang(): string
    {
        return match (self::$code) {
            'zh' => 'zh-Hans',
            default => self::$code,
        };
    }

    public static function ogLocale(): string
    {
        return self::LOCALES[self::$code]['og'] ?? 'ru_RU';
    }

    public static function dir(): string
    {
        return self::LOCALES[self::$code]['dir'] ?? 'ltr';
    }

    public static function get(string $key, ?string $default = null): string
    {
        if (array_key_exists($key, self::$messages)) {
            return (string)self::$messages[$key];
        }
        return $default ?? $key;
    }

    public static function content(string $key, ?string $fallback = null): string
    {
        $node = self::contentNode($key);
        if ($node === null) {
            return $fallback ?? '';
        }
        return is_scalar($node) ? (string)$node : ($fallback ?? '');
    }

    /** @return mixed */
    public static function contentNode(string $key): mixed
    {
        $parts = explode('.', $key);
        $node = self::$content;
        foreach ($parts as $p) {
            if (!is_array($node) || !array_key_exists($p, $node)) {
                return null;
            }
            $node = $node[$p];
        }
        return $node;
    }

    /** @return array<int|string, mixed>|null */
    public static function contentArray(string $key): ?array
    {
        $node = self::contentNode($key);
        return is_array($node) ? $node : null;
    }

    /** @return array<int, array{q:string,a:string}> */
    public static function faq(array $fallbackRu): array
    {
        if (!empty(self::$content['faq']) && is_array(self::$content['faq'])) {
            return self::$content['faq'];
        }
        return $fallbackRu;
    }

    public static function prefix(): string
    {
        return self::$code === self::DEFAULT ? '' : '/' . self::$code;
    }

    public static function url(string $path = '/'): string
    {
        $path = '/' . ltrim($path, '/');
        if ($path === '/') {
            return self::prefix() === '' ? '/' : self::prefix() . '/';
        }
        // якоря
        if (str_starts_with($path, '/#')) {
            return (self::prefix() ?: '') . $path;
        }
        return self::prefix() . $path;
    }

    public static function absoluteUrl(string $path = '/'): string
    {
        return app_url(ltrim(self::url($path), '/'));
    }

    /** @return list<string> */
    public static function codes(): array
    {
        return array_keys(self::LOCALES);
    }

    private static function loadFiles(string $root, string $code): void
    {
        $uiRu = $root . '/lang/ru.php';
        $ui = $root . '/lang/' . $code . '.php';
        $messages = is_file($uiRu) ? (require $uiRu) : [];
        if ($code !== 'ru' && is_file($ui)) {
            $messages = array_merge($messages, require $ui);
        }
        self::$messages = is_array($messages) ? $messages : [];

        $content = [];
        $contentFile = $root . '/lang/content/' . $code . '.php';
        if (is_file($contentFile)) {
            $loaded = require $contentFile;
            $content = is_array($loaded) ? $loaded : [];
        }
        self::$content = $content;
    }
}
