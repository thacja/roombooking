<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Helpers\functions as helpers;

class ApprovalController
{
    public static function index(): void
    {
        helpers\AuthMiddleware::manager(function () {
            $db = Database::getInstance();
            $status = $_GET['status'] ?? 'pending';

            $allowedStatuses = ['pending', 'approved', 'rejected', 'cancelled', 'completed'];
            if (!in_array($status, $allowedStatuses, true)) {
                $status = 'pending';
            }

            $sql = 'SELECT b.*, r.name as room_name, r.building, r.floor, r.room_number, u.full_name as user_name, u.email as user_email, u.department as user_department
                    FROM bookings b
                    JOIN rooms r ON b.room_id = r.id
                    JOIN users u ON b.user_id = u.id
                    WHERE b.status = :status
                    ORDER BY b.created_at DESC';

            $bookings = $db->fetchAll($sql, ['status' => $status]);

            require __DIR__ . '/../Views/approvals/index.php';
        });
    }

    public static function approve(int $bookingId): void
    {
        helpers\AuthMiddleware::manager(function () use ($bookingId) {
            $db = Database::getInstance();
            $userId = helpers\id();

            $booking = $db->fetch(
                'SELECT * FROM bookings WHERE id = :id AND status = "pending" LIMIT 1',
                ['id' => $bookingId]
            );

            if (!$booking) {
                helpers\abort(404, 'ไม่พบรายการจองที่รออนุมัติ');
                return;
            }

            $db->execute(
                'UPDATE bookings SET status = "approved", approved_by = :approved_by, approved_at = NOW(), updated_at = NOW() WHERE id = :id',
                [
                    'id' => $bookingId,
                    'approved_by' => $userId,
                ]
            );

            $db->execute(
                'INSERT INTO booking_history (booking_id, old_status, new_status, changed_by, remark, created_at) VALUES (:booking_id, "pending", "approved", :changed_by, "อนุมัติการจอง", NOW())',
                [
                    'booking_id' => $bookingId,
                    'changed_by' => $userId,
                ]
            );

            $db->execute(
                'INSERT INTO notifications (user_id, booking_id, type, title, message, is_read, created_at) VALUES (:user_id, :booking_id, "in_app", :title, :message, 0, NOW())',
                [
                    'user_id' => $booking['user_id'],
                    'booking_id' => $bookingId,
                    'title' => 'การจองห้องได้รับการอนุมัติ',
                    'message' => 'การจอง ' . $booking['booking_code'] . ' ห้อง ' . $booking['room_name'] . ' วันที่ ' . $booking['booking_date'] . ' ได้รับการอนุมัติแล้ว',
                ]
            );

            helpers\flash('success', 'อนุมัติการจองสำเร็จ');
            helpers\redirect(base_path('/approvals'));
        });
    }

    public static function reject(int $bookingId): void
    {
        helpers\AuthMiddleware::manager(function () use ($bookingId) {
            $db = Database::getInstance();
            $userId = helpers\id();

            $booking = $db->fetch(
                'SELECT * FROM bookings WHERE id = :id AND status = "pending" LIMIT 1',
                ['id' => $bookingId]
            );

            if (!$booking) {
                helpers\abort(404, 'ไม่พบรายการจองที่รออนุมัติ');
                return;
            }

            $reason = trim((string) ($_POST['reason'] ?? ''));

            $db->execute(
                'UPDATE bookings SET status = "rejected", approved_by = :approved_by, approved_at = NOW(), cancel_reason = :cancel_reason, updated_at = NOW() WHERE id = :id',
                [
                    'id' => $bookingId,
                    'approved_by' => $userId,
                    'cancel_reason' => $reason ?: null,
                ]
            );

            $db->execute(
                'INSERT INTO booking_history (booking_id, old_status, new_status, changed_by, remark, created_at) VALUES (:booking_id, "pending", "rejected", :changed_by, :remark, NOW())',
                [
                    'booking_id' => $bookingId,
                    'changed_by' => $userId,
                    'remark' => $reason ?: 'ปฏิเสธการจอง',
                ]
            );

            $db->execute(
                'INSERT INTO notifications (user_id, booking_id, type, title, message, is_read, created_at) VALUES (:user_id, :booking_id, "in_app", :title, :message, 0, NOW())',
                [
                    'user_id' => $booking['user_id'],
                    'booking_id' => $bookingId,
                    'title' => 'การจองห้องไม่ได้รับการอนุมัติ',
                    'message' => 'การจอง ' . $booking['booking_code'] . ' ห้อง ' . $booking['room_name'] . ' ไม่ได้รับการอนุมัติ' . ($reason ? ' เหตุผล: ' . $reason : ''),
                ]
            );

            helpers\flash('success', 'ปฏิเสธการจองแล้ว');
            helpers\redirect(base_path('/approvals'));
        });
    }
}
