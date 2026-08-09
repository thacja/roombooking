<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-box { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .login-box h1 { font-size: 24px; margin-bottom: 8px; color: #111827; }
        .login-box p { color: #6b7280; margin-bottom: 24px; font-size: 14px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px; }
        .form-group input { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .form-group input:focus { outline: none; border-color: #2563eb; ring: 2px solid #dbeafe; }
        .btn { width: 100%; padding: 10px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>เข้าสู่ระบบ</h1>
        <p>ระบบจองห้องเรียน</p>

        <?php $flash = flash_messages(); ?>
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo base_path('/login'); ?>">
            <?php echo $csrf; ?>
            <div class="form-group">
                <label for="email">อีเมล</label>
                <input type="email" id="email" name="email" required autofocus value="<?php echo e(old('email')); ?>">
            </div>
            <div class="form-group">
                <label for="password">รหัสผ่าน</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">เข้าสู่ระบบ</button>
        </form>
    </div>
</body>
</html>
