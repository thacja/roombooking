<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การแจ้งเตือน - ระบบจองห้องเรียน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .btn { padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #1d4ed8; }
        .notification-item { padding: 16px; border-bottom: 1px solid #e5e7eb; }
        .notification-item:last-child { border-bottom: none; }
        .notification-item.unread { background: #eff6ff; }
        .notification-title { font-weight: 600; color: #111827; margin-bottom: 4px; }
        .notification-message { color: #4b5563; font-size: 14px; margin-bottom: 4px; }
        .notification-time { color: #9ca3af; font-size: 12px; }
        .empty { text-align: center; padding: 40px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="<?php echo base_path('/'); ?>">หน้าแรก</a>
            <a href="<?php echo base_path('/rooms'); ?>">ค้นหาห้องว่าง</a>
            <a href="<?php echo base_path('/bookings'); ?>">การจองของฉัน</a>
            <a href="<?php echo base_path('/notifications'); ?>">แจ้งเตือน</a>
        </nav>

        <div class="card">
            <h2>การแจ้งเตือน</h2>

            <?php if (empty($notifications)): ?>
                <div class="empty">ไม่มีการแจ้งเตือน</div>
            <?php else: ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="notification-item <?php echo $n['is_read'] ? '' : 'unread'; ?>">
                        <div class="notification-title"><?php echo e($n['title']); ?></div>
                        <div class="notification-message"><?php echo e($n['message']); ?></div>
                        <div class="notification-time"><?php echo e($n['created_at']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>