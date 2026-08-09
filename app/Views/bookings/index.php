<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจองของฉัน - ระบบจองห้องเรียน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .btn { padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1d4ed8; }
        .btn-success { background: #059669; }
        .btn-success:hover { background: #047857; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-cancelled { background: #f3f4f6; color: #374151; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .empty { text-align: center; padding: 40px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="<?php echo base_path('/'); ?>">หน้าแรก</a>
            <a href="<?php echo base_path('/rooms'); ?>">ค้นหาห้องว่าง</a>
            <a href="<?php echo base_path('/bookings/create'); ?>">จองห้องใหม่</a>
        </nav>

        <?php $flash = helpers\flash_messages(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>การจองของฉัน</h2>

            <?php if (empty($bookings)): ?>
                <div class="empty">ยังไม่มีรายการจอง</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ห้อง</th>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><?php echo e($b['booking_code']); ?></td>
                                <td><?php echo e($b['room_name']); ?></td>
                                <td><?php echo e($b['booking_date']); ?></td>
                                <td><?php echo e($b['start_time']); ?> - <?php echo e($b['end_time']); ?></td>
                                <td>
                                    <?php
                                        $statusClass = 'badge-' . ($b['status'] ?? 'pending');
                                        $statusText = [
                                            'pending' => 'รออนุมัติ',
                                            'approved' => 'อนุมัติแล้ว',
                                            'rejected' => 'ปฏิเสธ',
                                            'cancelled' => 'ยกเลิก',
                                            'completed' => 'เสร็จสิ้น'
                                        ][$b['status']] ?? $b['status'];
                                    ?>
                                    <span class="badge <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                                </td>
                                <td>
                                    <a href="<?php echo base_path('/bookings/' . $b['id']); ?>" class="btn">ดูรายละเอียด</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>