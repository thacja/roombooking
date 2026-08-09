<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="<?php echo base_path('/'); ?>">หน้าแรก</a>
            <a href="<?php echo base_path('/rooms'); ?>">ค้นหาห้องว่าง</a>
            <a href="<?php echo base_path('/bookings'); ?>">การจองของฉัน</a>
            <a href="<?php echo base_path('/approvals'); ?>">อนุมัติ</a>
            <a href="<?php echo base_path('/notifications'); ?>">แจ้งเตือน</a>
        </nav>

        <h1>Admin Dashboard</h1>
        <p>ยินดีต้อนรับ, <?php echo e(user('full_name')); ?></p>
        <p>Role: <?php echo e(user('role')); ?></p>
        <p>คุณสามารถจัดการการจอง, อนุมัติ/ปฏิเสธการจอง และดูสถิติได้ที่นี่</p>
    </div>
</body>
</html>
