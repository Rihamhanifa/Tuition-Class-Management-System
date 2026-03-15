<?php
require_once '../config/db.php';
require_once '../includes/header.php';

try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY created_at DESC");
    $classes = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "<div class='badge badge-danger'>Error: " . $e->getMessage() . "</div>";
    $classes = [];
}
?>

<div class="page-header">
    <h1 class="page-title">Class Management</h1>
    <a href="add.php" class="btn btn-primary"><i data-lucide="plus"></i> Create Class</a>
</div>

<div class="grid grid-cols-3">
    <?php if(empty($classes)): ?>
        <div style="grid-column: span 3; text-align: center; padding: 40px; color: var(--text-muted);">
            No classes found. Start by creating one!
        </div>
    <?php else: ?>
        <?php foreach($classes as $c): ?>
            <?php
            // Get student count for this class
            try {
                $countStmt = $pdo->prepare("SELECT COUNT(*) FROM student_classes WHERE class_id = ?");
                $countStmt->execute([$c['id']]);
                $student_count = $countStmt->fetchColumn();
            } catch (Exception $e) { $student_count = 0; }
            ?>
            <div class="card" style="display: flex; flex-direction: column;">
                <h3 style="font-size: 1.2rem; margin-bottom: 5px; color: var(--text-main);"><?= htmlspecialchars($c['class_name']) ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem; flex-grow: 1; margin-bottom: 15px;">
                    <?= htmlspecialchars($c['description'] ?: 'No description provided.') ?>
                </p>
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 5px; color: var(--primary-color); font-weight: 500; font-size: 0.9rem;">
                        <i data-lucide="users" style="width: 16px; height: 16px;"></i> <?= $student_count ?> Enrolled
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
