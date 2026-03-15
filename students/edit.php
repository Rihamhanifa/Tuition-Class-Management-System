<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit();
}

$error = '';
$success = '';

// Fetch student
try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        die("Student not found.");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $joined_date = trim($_POST['joined_date'] ?? '');

    if (empty($first_name) || empty($last_name)) {
        $error = "First name and Last name are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE students SET first_name=?, last_name=?, phone=?, email=?, joined_date=? WHERE id=?");
            $stmt->execute([$first_name, $last_name, $phone, $email, $joined_date, $id]);
            
            $success = "Student updated successfully!";
            // Update local variable
            $student = array_merge($student, compact('first_name', 'last_name', 'phone', 'email', 'joined_date'));
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
        <h1 class="page-title" style="margin: 0;">Edit Student #<?= str_pad($student['id'], 4, '0', STR_PAD_LEFT) ?></h1>
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
                <input type="text" name="first_name" class="form-control" required value="<?= htmlspecialchars($student['first_name']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" required value="<?= htmlspecialchars($student['last_name']) ?>">
            </div>
        </div>
        
        <div class="grid grid-cols-2" style="gap: 15px;">
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($student['phone']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Joined Date</label>
            <input type="date" name="joined_date" class="form-control" value="<?= htmlspecialchars($student['joined_date']) ?>">
        </div>

        <div style="margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;">
            <a href="index.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Student</button>
        </div>
    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
