<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($class_name)) {
        $error = "Class name is required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, description) VALUES (?, ?)");
            $stmt->execute([$class_name, $description]);
            
            $success = "Class created successfully!";
            echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1000);</script>";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="index.php" class="btn btn-secondary" style="padding: 8px;"><i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i></a>
        <h1 class="page-title" style="margin: 0;">Create New Class</h1>
    </div>
</div>

<div class="card" style="max-width: 600px;">
    <?php if ($error): ?>
        <div style="background-color: #fee2e2; color: #ef4444; padding: 10px; border-radius: var(--radius-md); margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background-color: #dcfce7; color: #166534; padding: 10px; border-radius: var(--radius-md); margin-bottom: 20px;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label class="form-label">Class Name *</label>
            <input type="text" name="class_name" class="form-control" required placeholder="e.g. O/L Mathematics 2026">
        </div>
        
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Brief description about the class schedule or contents"></textarea>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Class</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
