<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $joined_date = trim($_POST['joined_date'] ?? date('Y-m-d'));

    if (empty($first_name) || empty($last_name)) {
        $error = "First name and Last name are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, phone, email, joined_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $phone, $email, $joined_date]);
            
            $success = "Student added successfully!";
            // Redirect after successful submission to avoid resubmission
            echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="index.php" class="btn btn-secondary" style="padding: 8px;"><i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i></a>
        <h1 class="page-title" style="margin: 0;">Add New Student</h1>
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
        <div class="grid grid-cols-2" style="gap: 15px;">
            <div class="form-group">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
        </div>
        
        <div class="grid grid-cols-2" style="gap: 15px;">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="07XXXXXXXX">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Joined Date</label>
            <input type="date" name="joined_date" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Student</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
