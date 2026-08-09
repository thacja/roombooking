<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>ยินดีต้อนรับ, <?php echo e(user('full_name')); ?></p>
    <p>Role: <?php echo e(user('role')); ?></p>
</body>
</html>
