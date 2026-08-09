<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\RoomController;
use App\Controllers\BookingController;
use App\Middleware\AuthMiddleware;

return function (string $method, string $uri): void {
    route('GET', '/login', [AuthController::class, 'showLogin']);
    route('POST', '/login', [AuthController::class, 'login']);
    route('GET', '/logout', [AuthController::class, 'logout']);
    route('GET', '/rooms', [RoomController::class, 'index']);

    route('GET', '/dashboard', function () {
        AuthMiddleware::auth(function () {
            require __DIR__ . '/../../app/Views/dashboard/index.php';
        });
    });

    route('GET', '/admin', function () {
        AuthMiddleware::admin(function () {
            require __DIR__ . '/../../app/Views/admin/index.php';
        });
    });

    route('GET', '/', function () {
        require __DIR__ . '/../../app/Views/home.php';
    });

    dispatch($method, $uri);
};
