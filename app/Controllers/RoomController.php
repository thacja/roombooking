<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;

class RoomController
{
    public static function index(): void
    {
        $db = Database::getInstance();

        $search = [
            'date' => trim((string) ($_GET['date'] ?? '')),
            'start_time' => trim((string) ($_GET['start_time'] ?? '')),
            'end_time' => trim((string) ($_GET['end_time'] ?? '')),
            'capacity' => trim((string) ($_GET['capacity'] ?? '')),
            'building' => trim((string) ($_GET['building'] ?? '')),
        ];

        $rooms = [];
        $hasResults = false;

        if (!empty($search['date']) && !empty($search['start_time']) && !empty($search['end_time'])) {
            $rooms = self::searchAvailableRooms($db, $search);
            $hasResults = true;
        }

        $buildings = self::getBuildings($db);

        require __DIR__ . '/../Views/rooms/index.php';
    }

    private static function searchAvailableRooms(Database $db, array $search): array
    {
        $sql = '
            SELECT 
                r.id,
                r.name,
                r.building,
                r.floor,
                r.room_number,
                r.capacity,
                r.room_type,
                r.description,
                r.image_path,
                COUNT(rf.id) as facility_count,
                GROUP_CONCAT(rf.name) as facilities
            FROM rooms r
            LEFT JOIN room_facilities rf ON r.id = rf.room_id
            WHERE 
                r.is_active = 1
                AND r.capacity >= :capacity
                AND (:building = "" OR r.building = :building)
                AND NOT EXISTS (
                    SELECT 1 
                    FROM bookings b 
                    WHERE 
                        b.room_id = r.id
                        AND b.booking_date = :date
                        AND b.status IN ("pending", "approved", "completed")
                        AND b.start_time < :end_time
                        AND b.end_time > :start_time
                )
            GROUP BY r.id
            ORDER BY r.building, r.floor, r.room_number
        ';

        $capacity = !empty($search['capacity']) ? (int) $search['capacity'] : 0;
        $building = $search['building'];

        return $db->fetchAll($sql, [
            'date' => $search['date'],
            'start_time' => $search['start_time'],
            'end_time' => $search['end_time'],
            'capacity' => $capacity,
            'building' => $building,
        ]);
    }

    private static function getBuildings(Database $db): array
    {
        $sql = '
            SELECT DISTINCT building 
            FROM rooms 
            WHERE is_active = 1 
            ORDER BY building ASC
        ';

        $results = $db->fetchAll($sql);
        return array_column($results, 'building');
    }
}
