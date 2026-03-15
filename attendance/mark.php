<?php
require_once '../config/db.php';
require_once '../includes/header.php';

$class_id = $_GET['class_id'] ?? null;
$date = $_GET['date'] ?? date('Y-m-d');

if (!$class_id) {
    echo "<script>window.location.href='index.php';</script>";
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_data = $_POST['attendance'] ?? []; // student_id => status
    
    if (!empty($attendance_data)) {
        try {
            $pdo->beginTransaction();
            
            // Delete existing records for this class and date to allow updating
            $delStmt = $pdo->prepare("DELETE FROM attendance WHERE class_id = ? AND attendance_date = ?");
            $delStmt->execute([$class_id, $date]);
            
            $insStmt = $pdo->prepare("INSERT INTO attendance (class_id, student_id, attendance_date, status) VALUES (?, ?, ?, ?)");
            foreach ($attendance_data as $student_id => $status) {
                $insStmt->execute([$class_id, $student_id, $date, $status]);
            }
            
            $pdo->commit();
            $success = "Attendance saved successfully!";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Database Error: " . $e->getMessage();
        }
    }
}

// Fetch class info
try {
    $cStmt = $pdo->prepare("SELECT class_name FROM classes WHERE id = ?");
    $cStmt->execute([$class_id]);
    $class_info = $cStmt->fetch();
    
    // Fetch all enrolled students for this class implicitly (for this demo, we assume all students are in the class or fetch them all. Let's assume all students are in this global class pool unless student_classes is properly populated. To make it work smoothly right now, we will just fetch all students and pre-check attendance.)
    // Note: In a real system, we'd join with `student_classes`
    $sStmt = $pdo->query("SELECT id, first_name, last_name FROM students ORDER BY first_name ASC");
    $students = $sStmt->fetchAll();
    
    // Fetch existing attendance for today
    $aStmt = $pdo->prepare("SELECT student_id, status FROM attendance WHERE class_id = ? AND attendance_date = ?");
    $aStmt->execute([$class_id, $date]);
    $existing = [];
    while ($row = $aStmt->fetch()) {
        $existing[$row['student_id']] = $row['status'];
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="page-header">
    <div style="display: flex; align-items: center; gap: 15px;">
        <a href="index.php" class="btn btn-secondary" style="padding: 8px;"><i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i></a>
        <div>
            <h1 class="page-title" style="margin: 0;">Mark Attendance</h1>
            <div style="font-size: 0.9rem; color: var(--text-muted); margin-top: 5px;">
                <?= htmlspecialchars($class_info['class_name'] ?? 'Unknown Class') ?> • <?= date('F d, Y', strtotime($date)) ?>
            </div>
        </div>
    </div>
</div>

<div class="card" style="max-width: 800px;">
    <?php if ($error): ?>
        <div style="background-color: #fee2e2; color: #ef4444; padding: 10px; border-radius: var(--radius-md); margin-bottom: 20px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div style="background-color: #dcfce7; color: #166534; padding: 10px; border-radius: var(--radius-md); margin-bottom: 20px;"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Student Name</th>
                        <th style="text-align: right; width: 300px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($students)): ?>
                        <tr><td colspan="3" style="text-align: center;">No students available.</td></tr>
                    <?php else: ?>
                        <?php foreach($students as $s): 
                            $status = $existing[$s['id']] ?? 'Present'; // Default to Present
                        ?>
                            <tr>
                                <td>#<?= $s['id'] ?></td>
                                <td style="font-weight: 500;"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                                <td style="text-align: right;">
                                    <div style="display: inline-flex; gap: 10px; background: var(--bg-color); padding: 5px; border-radius: var(--radius-md);">
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 4px; <?= $status == 'Present' ? 'background: #dcfce7; color: #166534; font-weight:600;' : '' ?>">
                                            <input type="radio" name="attendance[<?= $s['id'] ?>]" value="Present" <?= $status == 'Present' ? 'checked' : '' ?> onchange="this.form.submit()"> Present
                                        </label>
                                        <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; padding: 5px 10px; border-radius: 4px; <?= $status == 'Absent' ? 'background: #fee2e2; color: #991b1b; font-weight:600;' : '' ?>">
                                            <input type="radio" name="attendance[<?= $s['id'] ?>]" value="Absent" <?= $status == 'Absent' ? 'checked' : '' ?> onchange="this.form.submit()"> Absent
                                        </label>
                                    </div>
                                    <!-- Use JS onchange form submit for quick marking, or wait for master submit -->
                                    <noscript>
                                        <button type="submit" class="btn btn-secondary" style="padding: 2px 5px; font-size: 0.8rem;">Update</button>
                                    </noscript>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 24px;">Save All Attendance</button>
        </div>
    </form>
</div>

<script>
    // Remove auto-submit scripts if any to allow bulk saving via the master button
    document.querySelectorAll('input[type=radio]').forEach(radio => {
        radio.removeAttribute('onchange');
        radio.addEventListener('change', function() {
            // UI visual update only
            const container = this.closest('div');
            container.querySelectorAll('label').forEach(lbl => {
                lbl.style.background = 'transparent';
                lbl.style.fontWeight = 'normal';
                lbl.style.color = 'inherit';
            });
            if (this.value === 'Present') {
                this.parentElement.style.background = '#dcfce7';
                this.parentElement.style.color = '#166534';
                this.parentElement.style.fontWeight = '600';
            } else {
                this.parentElement.style.background = '#fee2e2';
                this.parentElement.style.color = '#991b1b';
                this.parentElement.style.fontWeight = '600';
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
