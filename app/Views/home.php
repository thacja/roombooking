<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Room Booking System') ?></title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; }
        .links { text-align: center; }
        .links a { margin: 0 10px; text-decoration: none; color: #0066cc; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ยินดีต้อนรับสู่ระบบจองห้องเรียน</h1>
        <p>Room Booking System</p>
    </div>
    <div class="links">
        <a href="<?php echo base_path('/rooms'); ?>">ดูรายการห้อง</a>
        <a href="<?php echo base_path('/login'); ?>">เข้าสู่ระบบ</a>
    </div>
</body>
</html>