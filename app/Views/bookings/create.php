<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จองห้อง - ระบบจองห้องเรียน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .form-group textarea { min-height: 80px; resize: vertical; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #2563eb; }
        .btn { padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        .btn-danger { background: #dc2626; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-success { background: #059669; }
        .btn-success:hover { background: #047857; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .room-info { background: #f9fafb; padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; color: #374151; }
        .participants-hint { font-size: 12px; color: #6b7280; margin-top: 4px; }
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
            <h2>จองห้อง</h2>

            <?php if (!empty($room)): ?>
                <div class="room-info">
                    <strong><?php echo e($room['name']); ?></strong><br>
                    <?php echo e($room['building']); ?> · ชั้น <?php echo e($room['floor']); ?> · ห้อง <?php echo e($room['room_number']); ?><br>
                    ความจุ: <?php echo e($room['capacity']); ?> คน · ประเภท: <?php echo e(ucfirst($room['room_type'])); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo base_path('/bookings'); ?>">
                <?php echo helpers\csrf_field(); ?>

                <?php if (!empty($room)): ?>
                    <input type="hidden" name="room_id" value="<?php echo e($room['id']); ?>">
                <?php else: ?>
                    <div class="form-group">
                        <label for="room_id">เลือกห้อง</label>
                        <select id="room_id" name="room_id" required>
                            <option value="">-- เลือกห้อง --</option>
                            <?php foreach ($rooms as $r): ?>
                                <option value="<?php echo e($r['id']); ?>">
                                    <?php echo e($r['name']); ?> (<?php echo e($r['building']); ?> ชั้น<?php echo e($r['floor']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">หัวข้อการจอง</label>
                    <input type="text" id="title" name="title" required value="<?php echo e(old('title')); ?>">
                </div>

                <div class="form-group">
                    <label for="booking_date">วันที่ต้องการใช้</label>
                    <input type="date" id="booking_date" name="booking_date" required value="<?php echo e($date); ?>">
                </div>

                <div class="form-group">
                    <label for="start_time">เวลาเริ่มต้น</label>
                    <input type="time" id="start_time" name="start_time" required value="<?php echo e($startTime); ?>">
                </div>

                <div class="form-group">
                    <label for="end_time">เวลาสิ้นสุด</label>
                    <input type="time" id="end_time" name="end_time" required value="<?php echo e($endTime); ?>">
                </div>

                <div class="form-group">
                    <label for="description">รายละเอียด</label>
                    <textarea id="description" name="description" placeholder="วัตถุประสงค์การใช้งานห้อง..."><?php echo e(old('description')); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="participants">รายชื่อผู้เข้าร่วม (optional)</label>
                    <textarea id="participants" name="participants" placeholder="ชื่อผู้เข้าร่วม (หนึ่งคนต่อบรรทัด)&#10;สามารถเพิ่มอีเมลและภาควิชาได้โดยใช้ | คั้น เช่น:&#10;สมชาย ใจดี | somchai@university.ac.th | วิทยาศาสตร์"></textarea>
                    <p class="participants-hint">ใส่ชื่อผู้เข้าร่วม คนละบรรทัด ส-descriptionะ añadir|อีเมล|ภาควิชา (optional)</p>
                </div>

                <button type="submit" class="btn">ส่งคำขอจอง</button>
            </form>
        </div>
    </div>
</body>
</html>