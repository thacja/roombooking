<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Helpers\functions as helpers;
use App\Middleware\AuthMiddleware;

class DashboardController
{
    public static function index(): void
    {
        AuthMiddleware::auth(function () {
            $db = Database::getInstance();
            $userRole = helpers\role();
            $userId = helpers\id();

            $totalBookings = $db->fetchColumn(
                'SELECT COUNT(*) FROM bookings WHERE 1=1' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND user_id = :user_id'),
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            $pendingBookings = $db->fetchColumn(
                'SELECT COUNT(*) FROM bookings WHERE status = "pending"' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND user_id = :user_id'),
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            $approvedBookings = $db->fetchColumn(
                'SELECT COUNT(*) FROM bookings WHERE status = "approved"' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND user_id = :user_id'),
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            $rejectedBookings = $db->fetchColumn(
                'SELECT COUNT(*) FROM bookings WHERE status = "rejected"' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND user_id = :user_id'),
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            $totalRooms = $db->fetchColumn('SELECT COUNT(*) FROM rooms WHERE is_active = 1');

            $statusChart = $db->fetchAll(
                'SELECT status, COUNT(*) as count FROM bookings WHERE 1=1' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND user_id = :user_id') . ' GROUP BY status',
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            $statusLabels = [];
            $statusData = [];
            $statusColors = [
                'pending' => '#f59e0b',
                'approved' => '#10b981',
                'rejected' => '#ef4444',
                'cancelled' => '#6b7280',
                'completed' => '#3b82f6'
            ];

            $statusMap = [];
            foreach ($statusChart as $row) {
                $statusMap[$row['status']] = (int) $row['count'];
            }

            $allStatuses = ['pending', 'approved', 'rejected', 'cancelled', 'completed'];
            foreach ($allStatuses as $s) {
                $statusLabels[] = [
                    'pending' => 'รออนุมัติ',
                    'approved' => 'อนุมัติแล้ว',
                    'rejected' => 'ปฏิเสธ',
                    'cancelled' => 'ยกเลิก',
                    'completed' => 'เสร็จสิ้น'
                ][$s];
                $statusData[] = $statusMap[$s] ?? 0;
            }

            $bookingTrend = $db->fetchAll(
                'SELECT booking_date, COUNT(*) as count 
                 FROM bookings 
                 WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                 ' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND user_id = :user_id') . '
                 GROUP BY booking_date 
                 ORDER BY booking_date ASC',
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            $trendLabels = [];
            $trendData = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-{$i} days"));
                $trendLabels[] = date('d/m', strtotime($date));
                $found = false;
                foreach ($bookingTrend as $row) {
                    if ($row['booking_date'] === $date) {
                        $trendData[] = (int) $row['count'];
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $trendData[] = 0;
                }
            }

            $recentBookings = $db->fetchAll(
                'SELECT b.*, r.name as room_name, u.full_name as user_name 
                 FROM bookings b 
                 JOIN rooms r ON b.room_id = r.id 
                 JOIN users u ON b.user_id = u.id 
                 WHERE 1=1' . (in_array($userRole, ['admin', 'manager'], true) ? '' : ' AND b.user_id = :user_id') . '
                 ORDER BY b.created_at DESC 
                 LIMIT 5',
                in_array($userRole, ['admin', 'manager'], true) ? [] : ['user_id' => $userId]
            );

            require __DIR__ . '/../Views/dashboard/index.php';
        });
    }
}
