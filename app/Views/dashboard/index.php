<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ระบบจองห้องเรียน</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f3f4f6; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .nav { display: flex; gap: 16px; margin-bottom: 24px; }
        .nav a { color: #2563eb; text-decoration: none; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        .welcome { font-size: 24px; font-weight: 600; color: #111827; margin-bottom: 8px; }
        .subtitle { color: #6b7280; margin-bottom: 24px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 14px; color: #6b7280; margin-bottom: 8px; font-weight: 500; }
        .stat-card .value { font-size: 32px; font-weight: 700; color: #111827; }
        .stat-card.pending .value { color: #f59e0b; }
        .stat-card.approved .value { color: #10b981; }
        .stat-card.rejected .value { color: #ef4444; }
        .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 24px; }
        .chart-card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .chart-card h3 { font-size: 16px; color: #111827; margin-bottom: 16px; }
        .recent-card { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .recent-card h3 { font-size: 16px; color: #111827; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; color: #374151; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 500; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .badge-cancelled { background: #f3f4f6; color: #374151; }
        .badge-completed { background: #dbeafe; color: #1e40af; }
        .empty { text-align: center; padding: 20px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="nav">
            <a href="<?php echo base_path('/'); ?>">หน้าแรก</a>
            <a href="<?php echo base_path('/rooms'); ?>">ค้นหาห้องว่าง</a>
            <a href="<?php echo base_path('/bookings'); ?>">การจองของฉัน</a>
            <?php if (in_array(helpers\role(), ['admin', 'manager'], true)): ?>
                <a href="<?php echo base_path('/approvals'); ?>">อนุมัติ</a>
            <?php endif; ?>
            <a href="<?php echo base_path('/notifications'); ?>">แจ้งเตือน</a>
            <a href="<?php echo base_path('/logout'); ?>">ออกจากระบบ</a>
        </nav>

        <div class="welcome">ยินดีต้อนรับ, <?php echo e(helpers\user('full_name')); ?></div>
        <div class="subtitle"><?php echo e(helpers\role()); ?></div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>การจองทั้งหมด</h3>
                <div class="value"><?php echo e($totalBookings); ?></div>
            </div>
            <div class="stat-card pending">
                <h3>รออนุมัติ</h3>
                <div class="value"><?php echo e($pendingBookings); ?></div>
            </div>
            <div class="stat-card approved">
                <h3>อนุมัติแล้ว</h3>
                <div class="value"><?php echo e($approvedBookings); ?></div>
            </div>
            <div class="stat-card rejected">
                <h3>ปฏิเสธ</h3>
                <div class="value"><?php echo e($rejectedBookings); ?></div>
            </div>
        </div>

        <div class="charts-grid">
            <div class="chart-card">
                <h3>สถานะการจอง</h3>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="chart-card">
                <h3>การจอง 7 วันล่าสุด</h3>
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="recent-card">
            <h3>รายการจองล่าสุด</h3>
            <table>
                <thead>
                    <tr>
                        <th>รหัส</th>
                        <th>ผู้จอง</th>
                        <th>ห้อง</th>
                        <th>วันที่</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentBookings)): ?>
                        <tr><td colspan="5" class="empty">ยังไม่มีรายการจอง</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $b): ?>
                            <tr>
                                <td><?php echo e($b['booking_code']); ?></td>
                                <td><?php echo e($b['user_name']); ?></td>
                                <td><?php echo e($b['room_name']); ?></td>
                                <td><?php echo e($b['booking_date']); ?></td>
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
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($statusLabels); ?>,
                datasets: [{
                    data: <?php echo json_encode($statusData); ?>,
                    backgroundColor: <?php echo json_encode(array_values($statusColors)); ?>,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($trendLabels); ?>,
                datasets: [{
                    label: 'การจอง',
                    data: <?php echo json_encode($trendData); ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    </script>
</body>
</html>