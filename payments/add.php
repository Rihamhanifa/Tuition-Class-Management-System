<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$error = '';
$success = '';

$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$current_month = date('F');
$current_year = date('Y');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $payment_month = $_POST['payment_month'] ?? '';
    $payment_year = $_POST['payment_year'] ?? '';
    $payment_date = $_POST['payment_date'] ?? date('Y-m-d');

    if (empty($student_id) || empty($amount) || !is_numeric($amount)) {
        $error = "Student and valid amount are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO payments (student_id, amount, payment_month, payment_year, payment_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$student_id, $amount, $payment_month, $payment_year, $payment_date]);
            
            $success = "Payment recorded successfully!";
            echo "<script>setTimeout(() => { window.location.href = 'index.php'; }, 1500);</script>";
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// Fetch students for dropdown
try {
    $sStmt = $pdo->query("SELECT id, first_name, last_name FROM students ORDER BY first_name ASC");
    $students = $sStmt->fetchAll();
} catch (PDOException $e) {
    $students = [];
}
?>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="index.php" class="btn btn-secondary" style="padding: 8px;"><i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i></a>
        <h1 class="page-title" style="margin: 0;">Record Payment</h1>
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
            <label class="form-label">Select Student *</label>
            <select name="student_id" class="form-control" required>
                <option value="">-- Choose Student --</option>
                <?php foreach($students as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?> (ID: <?= $s['id'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="grid grid-cols-2" style="gap: 15px;">
            <div class="form-group">
                <label class="form-label">Amount (Rs) *</label>
                <input type="number" step="0.01" name="amount" class="form-control" required placeholder="1500.00">
            </div>
            <div class="form-group">
                <label class="form-label">Payment Date *</label>
                <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <div class="grid grid-cols-2" style="gap: 15px;">
            <div class="form-group">
                <label class="form-label">For Month *</label>
                <select name="payment_month" class="form-control" required>
                    <?php foreach($months as $m): ?>
                        <option value="<?= $m ?>" <?= $m == $current_month ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">For Year *</label>
                <input type="number" name="payment_year" class="form-control" value="<?= $current_year ?>" required>
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Save Payment</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
