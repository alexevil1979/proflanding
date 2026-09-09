<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $template, array $data = [], ?string $layout = null): void
    {
        $content = self::fetch($template, $data);
        if ($layout) {
            echo self::fetch($layout, array_merge($data, ['content' => $content]));
            return;
        }
        echo $content;
    }

    public static function fetch(string $template, array $data = []): string
    {
        $path = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $template) . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('Шаблон не найден: ' . $template);
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $path;
        return (string)ob_get_clean();
    }

    public static function json(array $payload, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
