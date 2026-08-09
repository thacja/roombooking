<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการจอง - ระบบจองห้องเรียน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        .info-row { display: flex; padding: 12px 0; border-bottom: 1px solid #e5e7eb; }
        .info-row:last-child { border-bottom: none; }
        .info-label { width: 200px; font-weight: 500; color: #6b7280; font-size: 14px; }
        .info-value { flex: 1; color: #111827; font-size: 14px; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-cancelled { background: #f3f4f6; color: #374151; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .btn { padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1d4ed8; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .section-title { font-size: 16px; font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        .empty { text-align: center; padding: 20px; color: #6b7280; }
        .actions { display: flex; gap: 8px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="<?php echo base_path('/'); ?>">หน้าแรก</a>
            <a href="<?php echo base_path('/rooms'); ?>">ค้นหาห้องว่าง</a>
            <a href="<?php echo base_path('/bookings'); ?>">การจองของฉัน</a>
        </nav>

        <?php $flash = helpers\flash_messages(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>รายละเอียดการจอง #<?php echo e($booking['id']); ?></h2>

            <div class="info-row">
                <div class="info-label">รหัสการจอง</div>
                <div class="info-value"><?php echo e($booking['booking_code']); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">สถานะ</div>
                <div class="info-value">
                    <?php
                        $statusClass = 'badge-' . ($booking['status'] ?? 'pending');
                        $statusText = [
                            'pending' => 'รออนุมัติ',
                            'approved' => 'อนุมัติแล้ว',
                            'rejected' => 'ปฏิเสธ',
                            'cancelled' => 'ยกเลิก',
                            'completed' => 'เสร็จสิ้น'
                        ][$booking['status']] ?? $booking['status'];
                    ?>
                    <span class="badge <?php echo e($statusClass); ?>"><?php echo e($statusText); ?></span>
                </div>
            </div>

            <div class="info-row">
                <div class="info-label">ผู้จอง</div>
                <div class="info-value"><?php echo e($booking['user_name']); ?> (<?php echo e($booking['user_email']); ?>)</div>
            </div>

            <div class="info-row">
                <div class="info-label">ห้อง</div>
                <div class="info-value"><?php echo e($booking['room_name']); ?> (<?php echo e($booking['building']); ?> ชั้น<?php echo e($booking['floor']); ?> ห้อง<?php echo e($booking['room_number']); ?>)</div>
            </div>

            <div class="info-row">
                <div class="info-label">หัวข้อ</div>
                <div class="info-value"><?php echo e($booking['title']); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">รายละเอียด</div>
                <div class="info-value"><?php echo e($booking['description'] ?? '-'); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">วันที่</div>
                <div class="info-value"><?php echo e($booking['booking_date']); ?></div>
            </div>

            <div class="info-row">
                <div class="info-label">เวลา</div>
                <div class="info-value"><?php echo e($booking['start_time']); ?> - <?php echo e($booking['end_time']); ?></div>
            </div>

            <?php if (!empty($booking['approved_by_name'])): ?>
                <div class="info-row">
                    <div class="info-label">อนุมัติโดย</div>
                    <div class="info-value"><?php echo e($booking['approved_by_name']); ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($booking['cancel_reason'])): ?>
                <div class="info-row">
                    <div class="info-label">เหตุผลการยกเลิก</div>
                    <div class="info-value"><?php echo e($booking['cancel_reason']); ?></div>
                </div>
            <?php endif; ?>

            <div class="actions">
                <?php if (in_array($booking['status'], ['pending', 'approved'], true) && ($booking['user_id'] === (int)helpers\id() || in_array(helpers\role(), ['admin', 'manager'], true))): ?>
                    <form method="POST" action="<?php echo base_path('/bookings/' . $booking['id'] . '/cancel'); ?>" onsubmit="return confirm('ยืนยันการยกเลิก?');">
                        <?php echo helpers\csrf_field(); ?>
                        <button type="submit" class="btn btn-danger">ยกเลิกการจอง</button>
                    </form>
                <?php endif; ?>
                <a href="<?php echo base_path('/bookings'); ?>" class="btn btn-secondary">กลับ</a>
            </div>
        </div>

        <?php if (!empty($history)): ?>
            <div class="card">
                <h2 class="section-title">ประวัติการเปลี่ยนแปลง</h2>
                <table>
                    <thead>
                        <tr>
                            <th>เวลา</th>
                            <th>สถานะ</th>
                            <th>ผู้เปลี่ยนแปลง</th>
                            <th>หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                            <tr>
                                <td><?php echo e($h['created_at']); ?></td>
                                <td><?php echo e($h['new_status'] ?? '-'); ?></td>
                                <td><?php echo e($h['changed_by_name'] ?? '-'); ?></td>
                                <td><?php echo e($h['remark'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($participants)): ?>
            <div class="card">
                <h2 class="section-title">ผู้เข้าร่วม</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ชื่อ</th>
                            <th>อีเมล</th>
                            <th>ภาควิชา</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td><?php echo e($p['name']); ?></td>
                                <td><?php echo e($p['email'] ?? '-'); ?></td>
                                <td><?php echo e($p['department'] ?? '-'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>