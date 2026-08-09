<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$router = require __DIR__ . '/../app/Routes/web.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

if ($uri === '' || $uri === false) {
    $uri = '/';
}

$router($method, $uri);
