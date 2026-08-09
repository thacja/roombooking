<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาห้องว่าง - ระบบจองห้องเรียน</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        .search-box { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
        .search-box h2 { font-size: 20px; margin-bottom: 16px; color: #111827; }
        
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group label { font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px; }
        .form-group input, .form-group select { padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #2563eb; }
        
        .btn { padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .btn:hover { background: #1d4ed8; }
        .btn-secondary { background: #6b7280; }
        .btn-secondary:hover { background: #4b5563; }
        
        .results-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .results-header h3 { font-size: 18px; color: #111827; }
        .results-count { color: #6b7280; font-size: 14px; }
        
        .room-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        
        .room-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .room-card-header { background: #2563eb; color: white; padding: 16px; }
        .room-card-header h4 { font-size: 16px; margin-bottom: 4px; }
        .room-card-header p { font-size: 13px; opacity: 0.9; }
        
        .room-card-body { padding: 16px; }
        .room-info { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 14px; color: #4b5563; }
        .room-info svg { width: 16px; height: 16px; flex-shrink: 0; }
        
        .room-facilities { margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb; }
        .room-facilities h5 { font-size: 13px; color: #6b7280; margin-bottom: 8px; }
        .facility-tags { display: flex; flex-wrap: wrap; gap: 6px; }
        .facility-tag { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
        
        .room-card-footer { padding: 12px 16px; background: #f9fafb; border-top: 1px solid #e5e7eb; }
        .btn-book { width: 100%; padding: 8px; background: #059669; color: white; border: none; border-radius: 6px; font-size: 14px; cursor: pointer; text-decoration: none; display: inline-block; text-align: center; }
        .btn-book:hover { background: #047857; }
        
        .empty-state { text-align: center; padding: 60px 20px; background: white; border-radius: 8px; }
        .empty-state svg { width: 64px; height: 64px; color: #d1d5db; margin-bottom: 16px; }
        .empty-state h3 { font-size: 18px; color: #374151; margin-bottom: 8px; }
        .empty-state p { color: #6b7280; font-size: 14px; }
        
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        
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
        </nav>

        <h1 style="font-size: 24px; color: #111827; margin-bottom: 20px;">ค้นหาห้องว่าง</h1>

        <div class="search-box">
            <h2>กำหนดเงื่อนไขการค้นหา</h2>
            <form method="GET" action="<?php echo base_path('/rooms'); ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="date">วันที่ต้องการใช้</label>
                        <input type="date" id="date" name="date" required value="<?php echo e($_GET['date'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="start_time">เวลาเริ่มต้น</label>
                        <input type="time" id="start_time" name="start_time" required value="<?php echo e($_GET['start_time'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="end_time">เวลาสิ้นสุด</label>
                        <input type="time" id="end_time" name="end_time" required value="<?php echo e($_GET['end_time'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="capacity">ความจุขั้นต่ำ (คน)</label>
                        <input type="number" id="capacity" name="capacity" min="1" placeholder="ไม่ระบุ" value="<?php echo e($_GET['capacity'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="building">อาคาร</label>
                        <select id="building" name="building">
                            <option value="">-- ทั้งหมด --</option>
                            <?php foreach ($buildings as $b): ?>
                                <option value="<?php echo e($b); ?>" <?php echo (($_GET['building'] ?? '') === $b) ? 'selected' : ''; ?>>
                                    <?php echo e($b); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="justify-content: flex-end;">
                        <button type="submit" class="btn">ค้นหา</button>
                    </div>
                </div>
            </form>
        </div>

        <?php if ($hasResults): ?>
            <div class="results-header">
                <h3>ผลการค้นหา</h3>
                <span class="results-count">พบ <?php echo count($rooms); ?> ห้อง</span>
            </div>

            <?php if (empty($rooms)): ?>
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <h3>ไม่พบห้องว่างในเงื่อนไขที่กำหนด</h3>
                    <p>ลองเปลี่ยนวันที่หรือเวลาหรือลดเงื่อนไขความจุ</p>
                </div>
            <?php else: ?>
                <div class="room-grid">
                    <?php foreach ($rooms as $room): ?>
                        <div class="room-card">
                            <div class="room-card-header">
                                <h4><?php echo e($room['name']); ?></h4>
                                <p><?php echo e($room['building']); ?> · ชั้น <?php echo e($room['floor']); ?> · ห้อง <?php echo e($room['room_number']); ?></p>
                            </div>
                            <div class="room-card-body">
                                <div class="room-info">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>รองรับ <?php echo e($room['capacity']); ?> คน</span>
                                </div>
                                <div class="room-info">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    <span><?php echo e(ucfirst($room['room_type'])); ?></span>
                                </div>
                                <?php if (!empty($room['description'])): ?>
                                    <div class="room-info">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span><?php echo e($room['description']); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($room['facilities'])): ?>
                                    <div class="room-facilities">
                                        <h5>สิ่งอำนวยความสะดวก</h5>
                                        <div class="facility-tags">
                                            <?php foreach (explode(',', $room['facilities']) as $facility): ?>
                                                <span class="facility-tag"><?php echo e(trim($facility)); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="room-card-footer">
                                <a href="<?php echo base_path('/bookings/create?room_id=' . e($room['id']) . '&date=' . e($_GET['date'] ?? '') . '&start_time=' . e($_GET['start_time'] ?? '') . '&end_time=' . e($_GET['end_time'] ?? '')); ?>" class="btn-book">
                                    จองห้องนี้
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
