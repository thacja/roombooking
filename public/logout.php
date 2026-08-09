<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

$authController = new \App\Controllers\AuthController();
$authController->logout();
