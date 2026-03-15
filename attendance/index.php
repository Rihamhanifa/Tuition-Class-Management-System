<?php
require_once '../config/db.php';
require_once '../includes/header.php';

try {
    // Get all classes for the selection
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC");
    $classes = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='badge badge-danger'>Error: " . $e->getMessage() . "</div>";
    $classes = [];
}
?>

<div class="page-header">
    <h1 class="page-title">Attendance Management</h1>
</div>

<div class="grid grid-cols-2">
    <!-- Mark Attendance Panel -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="check-square" style="color: var(--primary-color);"></i> Mark Daily Attendance
        </h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 20px;">
            Select a class and date to mark student attendance.
        </p>
        
        <form action="mark.php" method="GET">
            <div class="form-group">
                <label class="form-label">Select Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">-- Choose Class --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required max="<?= date('Y-m-d') ?>">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">Proceed</button>
        </form>
    </div>

    <!-- Attendance History Summary -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="history" style="color: var(--secondary-color);"></i> Recent Records
        </h3>
        
        <?php
        try {
            // Get recent days where attendance was marked
            $histStmt = $pdo->query("SELECT a.attendance_date, c.class_name, COUNT(a.id) as total_marked, 
                                     SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) as present_count
                                     FROM attendance a 
                                     JOIN classes c ON a.class_id = c.id 
                                     GROUP BY a.attendance_date, a.class_id, c.class_name 
                                     ORDER BY a.attendance_date DESC LIMIT 5");
            $history = $histStmt->fetchAll();
        } catch (PDOException $e) { $history = []; }
        ?>
        
        <?php if(empty($history)): ?>
            <p style="text-align: center; color: var(--text-muted); padding: 20px;">No recent attendance records.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach($history as $h): ?>
                    <div style="padding: 15px; border: 1px solid var(--border-color); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div style="font-weight: 600; color: var(--text-main); margin-bottom: 4px;"><?= htmlspecialchars($h['class_name']) ?></div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);"><i data-lucide="calendar" style="width: 12px; height: 12px; vertical-align: -2px;"></i> <?= date('D, M d, Y', strtotime($h['attendance_date'])) ?></div>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-success" style="font-size: 0.9rem;"><?= $h['present_count'] ?> / <?= $h['total_marked'] ?> Present</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
