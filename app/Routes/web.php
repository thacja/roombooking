<?php

declare(strict_types=1);

use App\Controllers\ApprovalController;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\DashboardController;
use App\Controllers\NotificationController;
use App\Controllers\RoomController;
use App\Middleware\AuthMiddleware;

return function (string $method, string $uri): void {
    route('GET', '/login', [AuthController::class, 'showLogin']);
    route('POST', '/login', [AuthController::class, 'login']);
    route('GET', '/logout', [AuthController::class, 'logout']);
    route('GET', '/rooms', [RoomController::class, 'index']);

    route('GET', '/dashboard', [DashboardController::class, 'index']);

    route('GET', '/admin', function () {
        AuthMiddleware::admin(function () {
            require __DIR__ . '/../../app/Views/admin/index.php';
        });
    });

    route('GET', '/bookings', [BookingController::class, 'index']);
    route('GET', '/bookings/create', [BookingController::class, 'create']);
    route('POST', '/bookings', [BookingController::class, 'store']);
    route('GET', '/bookings/{id}', [BookingController::class, 'show']);
    route('POST', '/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    route('GET', '/approvals', [ApprovalController::class, 'index']);
    route('POST', '/approvals/{id}/approve', [ApprovalController::class, 'approve']);
    route('POST', '/approvals/{id}/reject', [ApprovalController::class, 'reject']);

    route('GET', '/notifications', [NotificationController::class, 'index']);
    route('POST', '/notifications/{id}/read', [NotificationController::class, 'markRead']);

    route('GET', '/', function () {
        require __DIR__ . '/../../app/Views/home.php';
    });

    dispatch($method, $uri);
};
