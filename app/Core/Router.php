<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<int, array{method:string,pattern:string,handler:callable|array}> */
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, callable|array $handler): void
    {
        $pattern = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $pattern . '$#u';
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = $this->normalizePath($uri);

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }
            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }
            $params = [];
            foreach ($matches as $k => $v) {
                if (!is_int($k)) {
                    $params[$k] = $v;
                }
            }
            $this->invoke($route['handler'], $params);
            return;
        }

        http_response_code(404);
        View::render('errors/404', [
            'title' => 'Страница не найдена',
        ], 'layouts/main');
    }

    private function invoke(callable|array $handler, array $params): void
    {
        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            $controller->$action(...array_values($params));
            return;
        }
        $handler(...array_values($params));
    }

    /**
     * Apache DirectoryIndex / php-fpm часто отдают URI как /index.php.
     */
    private function normalizePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if (!is_string($path) || $path === '') {
            $path = '/';
        }
        $path = rawurldecode($path);

        if ($path === '/index.php' || str_starts_with($path, '/index.php/')) {
            $path = substr($path, strlen('/index.php')) ?: '/';
        }

        // PATH_INFO (если proxy_fcgi прокинул)
        if ($path === '/' && !empty($_SERVER['PATH_INFO'])) {
            $path = (string)$_SERVER['PATH_INFO'];
        }

        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/') ?: '/';
        }

        return $path === '' ? '/' : $path;
    }
}
