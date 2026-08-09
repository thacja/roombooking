<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$authController = new \App\Controllers\AuthController();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $authController->login();
} else {
    $authController->showLogin();
}
