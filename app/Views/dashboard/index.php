<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    <p>ยินดีต้อนรับ, <?php echo e(user('full_name')); ?></p>
    <p>Role: <?php echo e(user('role')); ?></p>
    <a href="<?php echo base_path('/logout'); ?>">Logout</a>
</body>
</html>
