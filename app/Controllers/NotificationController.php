<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Helpers\functions as helpers;

class NotificationController
{
    public static function index(): void
    {
        helpers\AuthMiddleware::auth(function () {
            $db = Database::getInstance();
            $userId = helpers\id();

            $db->execute('UPDATE notifications SET is_read = 1 WHERE user_id = :user_id AND is_read = 0', ['user_id' => $userId]);

            $notifications = $db->fetchAll(
                'SELECT n.*, b.booking_code, r.name as room_name
                 FROM notifications n
                 LEFT JOIN bookings b ON n.booking_id = b.id
                 LEFT JOIN rooms r ON b.room_id = r.id
                 WHERE n.user_id = :user_id
                 ORDER BY n.created_at DESC',
                ['user_id' => $userId]
            );

            require __DIR__ . '/../Views/notifications/index.php';
        });
    }

    public static function markRead(int $notificationId): void
    {
        helpers\AuthMiddleware::auth(function () use ($notificationId) {
            $db = Database::getInstance();
            $userId = helpers\id();

            $db->execute(
                'UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id',
                ['id' => $notificationId, 'user_id' => $userId]
            );

            helpers\redirect(base_path('/notifications'));
        });
    }
}
