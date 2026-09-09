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
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rawurldecode($path);
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/') ?: '/';
        }

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
}
