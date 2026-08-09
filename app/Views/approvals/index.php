<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อนุมัติการจอง - ระบบจองห้องเรียน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; align-items: center; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .nav a.active { font-weight: 600; color: #111827; text-decoration: none; }
        .btn { padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1d4ed8; }
        .btn-success { background: #059669; }
        .btn-success:hover { background: #047857; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-cancelled { background: #f3f4f6; color: #374151; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .actions { display: flex; gap: 8px; }
        .empty { text-align: center; padding: 40px; color: #6b7280; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="<?php echo base_path('/'); ?>">หน้าแรก</a>
            <a href="<?php echo base_path('/rooms'); ?>">ค้นหาห้องว่าง</a>
            <a href="<?php echo base_path('/bookings'); ?>">การจองของฉัน</a>
            <a href="<?php echo base_path('/approvals'); ?>" class="<?php echo ($_GET['status'] ?? 'pending') === 'pending' ? 'active' : ''; ?>">รออนุมัติ</a>
            <a href="<?php echo base_path('/approvals?status=approved'); ?>" class="<?php echo ($_GET['status'] ?? '') === 'approved' ? 'active' : ''; ?>">อนุมัติแล้ว</a>
            <a href="<?php echo base_path('/approvals?status=rejected'); ?>" class="<?php echo ($_GET['status'] ?? '') === 'rejected' ? 'active' : ''; ?>">ปฏิเสธ</a>
            <a href="<?php echo base_path('/notifications'); ?>">แจ้งเตือน</a>
        </nav>

        <?php $flash = helpers\flash_messages(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>รายการจอง<?php echo e(($_GET['status'] ?? 'pending') === 'pending' ? 'รออนุมัติ' : (($_GET['status'] ?? '') === 'approved' ? 'ที่อนุมัติแล้ว' : 'ที่ปฏิเสธ')); ?></h2>

            <?php if (empty($bookings)): ?>
                <div class="empty">ไม่มีรายการ</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ผู้จอง</th>
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
                                <td>
                                    <?php echo e($b['user_name']); ?><br>
                                    <span style="font-size:12px;color:#6b7280;"><?php echo e($b['user_email']); ?></span>
                                </td>
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
                                <td class="actions">
                                    <a href="<?php echo base_path('/bookings/' . $b['id']); ?>" class="btn">ดู</a>
                                    <?php if ($b['status'] === 'pending'): ?>
                                        <form method="POST" action="<?php echo base_path('/approvals/' . $b['id'] . '/approve'); ?>" style="display:inline;">
                                            <?php echo helpers\csrf_field(); ?>
                                            <button type="submit" class="btn btn-success">อนุมัติ</button>
                                        </form>
                                        <button type="button" class="btn btn-danger" onclick="showRejectModal(<?php echo e($b['id']); ?>)">ปฏิเสธ</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function showRejectModal(bookingId) {
            const reason = prompt('เหตุผลการปฏิเสธ (optional):');
            if (reason === null) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/roombooking/approvals/' + bookingId + '/reject';
            form.innerHTML = '<input type="hidden" name="csrf_token" value="<?php echo e(helpers\csrf_token()); ?>">' +
                '<input type="hidden" name="reason" value="' + (reason || '') + '">';
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>