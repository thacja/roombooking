<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Middleware\AuthMiddleware;
use App\Helpers\functions as helpers;

class BookingController
{
    public static function create(): void
    {
        AuthMiddleware::auth(function () {
            $db = Database::getInstance();
            $roomId = (int) ($_GET['room_id'] ?? 0);
            $date = $_GET['date'] ?? '';
            $startTime = $_GET['start_time'] ?? '';
            $endTime = $_GET['end_time'] ?? '';

            $room = null;
            if ($roomId > 0) {
                $room = $db->fetch(
                    'SELECT id, name, building, floor, room_number, capacity, room_type FROM rooms WHERE id = :id AND is_active = 1 LIMIT 1',
                    ['id' => $roomId]
                );
            }

            $buildings = [];
            $rooms = [];
            if (empty($room)) {
                $buildings = self::getBuildings($db);
                $rooms = $db->fetchAll(
                    'SELECT id, name, building, floor, room_number, capacity FROM rooms WHERE is_active = 1 ORDER BY building, floor, room_number'
                );
            }

            require __DIR__ . '/../Views/bookings/create.php';
        });
    }

    public static function store(): void
    {
        AuthMiddleware::auth(function () {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                helpers\redirect(base_path('/rooms'));
                return;
            }

            $db = Database::getInstance();
            $roomId = (int) ($_POST['room_id'] ?? 0);
            $title = trim((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $bookingDate = $_POST['booking_date'] ?? '';
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';
            $participants = array_filter(array_map('trim', explode("\n", $_POST['participants'] ?? '')), fn($p) => $p !== '');

            if (empty($roomId) || empty($title) || empty($bookingDate) || empty($startTime) || empty($endTime)) {
                helpers\flash('error', 'กรุณากรอกข้อมูลให้ครบถ้วน');
                helpers\redirect(base_path('/bookings/create?room_id=' . $roomId . '&date=' . $bookingDate . '&start_time=' . $startTime . '&end_time=' . $endTime));
                return;
            }

            $room = $db->fetch(
                'SELECT id, name FROM rooms WHERE id = :id AND is_active = 1 LIMIT 1',
                ['id' => $roomId]
            );

            if (!$room) {
                helpers\flash('error', 'ไม่พบห้องที่เลือก');
                helpers\redirect(base_path('/rooms'));
                return;
            }

            $conflict = $db->fetch(
                'SELECT id FROM bookings 
                 WHERE room_id = :room_id 
                 AND booking_date = :date 
                 AND status IN ("pending", "approved", "completed") 
                 AND start_time < :end_time 
                 AND end_time > :start_time 
                 LIMIT 1',
                [
                    'room_id' => $roomId,
                    'date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]
            );

            if ($conflict) {
                helpers\flash('error', 'ห้องนี้ถูกจองแล้วในช่วงเวลาดังกล่าว');
                helpers\redirect(base_path('/bookings/create?room_id=' . $roomId . '&date=' . $bookingDate . '&start_time=' . $startTime . '&end_time=' . $endTime));
                return;
            }

            $bookingCode = 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $userId = (int) helpers\id();

            $db->execute(
                'INSERT INTO bookings (booking_code, room_id, user_id, title, description, booking_date, start_time, end_time, status, created_at, updated_at) 
                 VALUES (:booking_code, :room_id, :user_id, :title, :description, :booking_date, :start_time, :end_time, "pending", NOW(), NOW())',
                [
                    'booking_code' => $bookingCode,
                    'room_id' => $roomId,
                    'user_id' => $userId,
                    'title' => $title,
                    'description' => $description,
                    'booking_date' => $bookingDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ]
            );

            $bookingId = (int) $db->lastInsertId();

            if (!empty($participants)) {
                $stmt = $db->getConnection()->prepare(
                    'INSERT INTO booking_participants (booking_id, name, email, department, created_at) VALUES (:booking_id, :name, :email, :department, NOW())'
                );

                foreach ($participants as $participant) {
                    $parts = explode('|', $participant);
                    $name = trim($parts[0] ?? $participant);
                    $email = trim($parts[1] ?? '');
                    $department = trim($parts[2] ?? '');

                    $stmt->execute([
                        'booking_id' => $bookingId,
                        'name' => $name,
                        'email' => $email ?: null,
                        'department' => $department ?: null,
                    ]);
                }
            }

            $db->execute(
                'INSERT INTO booking_history (booking_id, old_status, new_status, changed_by, remark, created_at) VALUES (:booking_id, NULL, "pending", :changed_by, "สร้างรายการจอง", NOW())',
                [
                    'booking_id' => $bookingId,
                    'changed_by' => $userId,
                ]
            );

            $managers = $db->fetchAll(
                'SELECT u.id FROM users u WHERE u.role IN ("admin", "manager") AND u.is_active = 1'
            );

            if (!empty($managers)) {
                $stmt = $db->getConnection()->prepare(
                    'INSERT INTO notifications (user_id, booking_id, type, title, message, is_read, created_at) VALUES (:user_id, :booking_id, "in_app", :title, :message, 0, NOW())'
                );

                foreach ($managers as $manager) {
                    $stmt->execute([
                        'user_id' => $manager['id'],
                        'booking_id' => $bookingId,
                        'title' => 'มีรายการจองรอการอนุมัติ',
                        'message' => 'การจอง ' . $bookingCode . ' ห้อง ' . $room['name'] . ' วันที่ ' . $bookingDate . ' รอการอนุมัติ',
                    ]);
                }
            }

            helpers\flash('success', 'จองห้องสำเร็จ รอการอนุมัติ');
            helpers\redirect(base_path('/bookings/' . $bookingId));
        });
    }

    public static function show(int $bookingId): void
    {
        AuthMiddleware::auth(function () use ($bookingId) {
            $db = Database::getInstance();
            $userId = helpers\id();
            $userRole = helpers\role();

            $booking = $db->fetch(
                'SELECT b.*, r.name as room_name, r.building, r.floor, r.room_number, u.full_name as user_name, u.email as user_email, u.department as user_department,
                         approver.full_name as approved_by_name
                 FROM bookings b
                 JOIN rooms r ON b.room_id = r.id
                 JOIN users u ON b.user_id = u.id
                 LEFT JOIN users approver ON b.approved_by = approver.id
                 WHERE b.id = :id LIMIT 1',
                ['id' => $bookingId]
            );

            if (!$booking) {
                helpers\abort(404, 'ไม่พบรายการจอง');
                return;
            }

            if ($booking['user_id'] !== $userId && !in_array($userRole, ['admin', 'manager'], true)) {
                helpers\abort(403, 'คุณไม่มีสิทธิ์เข้าถึงรายการจองนี้');
                return;
            }

            $participants = $db->fetchAll(
                'SELECT * FROM booking_participants WHERE booking_id = :booking_id',
                ['booking_id' => $bookingId]
            );

            $history = $db->fetchAll(
                'SELECT bh.*, u.full_name as changed_by_name 
                 FROM booking_history bh 
                 LEFT JOIN users u ON bh.changed_by = u.id 
                 WHERE bh.booking_id = :booking_id 
                 ORDER BY bh.created_at ASC',
                ['booking_id' => $bookingId]
            );

            require __DIR__ . '/../Views/bookings/show.php';
        });
    }

    public static function cancel(int $bookingId): void
    {
        AuthMiddleware::auth(function () use ($bookingId) {
            $db = Database::getInstance();
            $userId = helpers\id();
            $userRole = helpers\role();

            $booking = $db->fetch(
                'SELECT * FROM bookings WHERE id = :id LIMIT 1',
                ['id' => $bookingId]
            );

            if (!$booking) {
                helpers\abort(404, 'ไม่พบรายการจอง');
                return;
            }

            if ($booking['user_id'] !== $userId && !in_array($userRole, ['admin', 'manager'], true)) {
                helpers\abort(403, 'คุณไม่มีสิทธิ์ยกเลิกรายการจองนี้');
                return;
            }

            if (in_array($booking['status'], ['cancelled', 'completed'], true)) {
                helpers\flash('error', 'ไม่สามารถยกเลิกรายการจองนี้ได้');
                helpers\redirect(base_path('/bookings/' . $bookingId));
                return;
            }

            $cancelReason = trim((string) ($_POST['cancel_reason'] ?? ''));

            $db->execute(
                'UPDATE bookings SET status = "cancelled", cancel_reason = :cancel_reason, updated_at = NOW() WHERE id = :id',
                [
                    'id' => $bookingId,
                    'cancel_reason' => $cancelReason ?: null,
                ]
            );

            $db->execute(
                'INSERT INTO booking_history (booking_id, old_status, new_status, changed_by, remark, created_at) VALUES (:booking_id, :old_status, "cancelled", :changed_by, :remark, NOW())',
                [
                    'booking_id' => $bookingId,
                    'old_status' => $booking['status'],
                    'changed_by' => $userId,
                    'remark' => $cancelReason ?: 'ยกเลิกการจอง',
                ]
            );

            if ($booking['status'] === 'approved') {
                $booker = $db->fetch('SELECT id FROM users WHERE id = :id LIMIT 1', ['id' => $booking['user_id']]);
                if ($booker) {
                    $db->execute(
                        'INSERT INTO notifications (user_id, booking_id, type, title, message, is_read, created_at) VALUES (:user_id, :booking_id, "in_app", :title, :message, 0, NOW())',
                        [
                            'user_id' => $booking['user_id'],
                            'booking_id' => $bookingId,
                            'title' => 'การจองห้องถูกยกเลิก',
                            'message' => 'การจอง ' . $booking['booking_code'] . ' ห้อง ' . $booking['room_name'] . ' ถูกยกเลิก',
                        ]
                    );
                }
            }

            helpers\flash('success', 'ยกเลิกรายการจองสำเร็จ');
            helpers\redirect(base_path('/bookings'));
        });
    }

    public static function index(): void
    {
        AuthMiddleware::auth(function () {
            $db = Database::getInstance();
            $userId = helpers\id();
            $userRole = helpers\role();

            $sql = 'SELECT b.*, r.name as room_name, r.building, r.floor, r.room_number 
                    FROM bookings b 
                    JOIN rooms r ON b.room_id = r.id 
                    WHERE 1=1';

            $params = [];

            if (!in_array($userRole, ['admin', 'manager'], true)) {
                $sql .= ' AND b.user_id = :user_id';
                $params['user_id'] = $userId;
            }

            $sql .= ' ORDER BY b.booking_date DESC, b.created_at DESC';

            $bookings = $db->fetchAll($sql, $params);

            require __DIR__ . '/../Views/bookings/index.php';
        });
    }

    private static function getBuildings(Database $db): array
    {
        $results = $db->fetchAll('SELECT DISTINCT building FROM rooms WHERE is_active = 1 ORDER BY building ASC');
        return array_column($results, 'building');
    }
}
