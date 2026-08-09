<?php

declare(strict_types=1);

if (!function_exists('redirect')) {
    function redirect(string $path, int $statusCode = 302): void
    {
        header('Location: ' . $path, true, $statusCode);
        exit;
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return '/roombooking' . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('back')) {
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}

if (!function_exists('flash_messages')) {
    function flash_messages(): ?array
    {
        if (empty($_SESSION['flash'])) {
            return null;
        }

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);

        return $flash;
    }
}

if (!function_exists('abort')) {
    function abort(int $statusCode, string $message = ''): void
    {
        http_response_code($statusCode);
        echo $message ?: ($statusCode === 404 ? 'Page Not Found' : 'Forbidden');
        exit;
    }
}

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('route')) {
    function route(string $method, string $path, $handler): void
    {
        static $routes = [];

        $routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler,
        ];

        $GLOBALS['_routes'] = $routes;
    }
}

if (!function_exists('dispatch')) {
    function dispatch(string $method, string $uri): void
    {
        $routes = $GLOBALS['_routes'] ?? [];

        foreach ($routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                ($route['handler'])();
                return;
            }
        }

        abort(404);
    }
}
