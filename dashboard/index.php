<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Fetch Statistics safely
try {
    $total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $total_classes = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    // Assuming the current month e.g., 'March'
    $current_month = date('F');
    $current_year = date('Y');
    
    $stmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE payment_month = ? AND payment_year = ?");
    $stmt->execute([$current_month, $current_year]);
    $monthly_income = $stmt->fetchColumn() ?: 0;

    // Last 5 recent students
    $recent_students = $pdo->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5")->fetchAll();

    // Chart Data (Mockup for Months)
    $months = ['January', 'February', 'March', 'April', 'May', 'June'];
    $income_data = [5000, 7000, $monthly_income, 0, 0, 0]; // Sample data

} catch (PDOException $e) {
    echo "<div class='badge badge-danger'>Error loading dashboard data: " . $e->getMessage() . "</div>";
    $total_students = $total_classes = $monthly_income = 0;
    $recent_students = [];
    $income_data = [0,0,0,0,0,0];
}
?>

<div class="page-header">
    <h1 class="page-title">Dashboard Overview</h1>
</div>

<div class="grid grid-cols-4" style="margin-bottom: 30px;">
    <!-- Stat Cards -->
    <div class="card">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px; font-weight: 500;">Total Students</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-main);"><?= number_format($total_students) ?></div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: #166534;"><i data-lucide="trending-up" style="width: 16px; height: 16px; vertical-align: middle;"></i> Active</div>
    </div>
    
    <div class="card">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px; font-weight: 500;">Total Classes</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-main);"><?= number_format($total_classes) ?></div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: var(--primary-color);">Scheduled</div>
    </div>
    
    <div class="card">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px; font-weight: 500;">Monthly Income (<?= $current_month ?>)</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-main);">Rs. <?= number_format($monthly_income, 2) ?></div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: #166534;">Revenue</div>
    </div>
    
    <div class="card">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px; font-weight: 500;">Attendance Rate</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--text-main);">85%</div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Avg across all classes</div>
    </div>
</div>

<div class="grid grid-cols-2">
    <!-- Chart Section -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Income Analytics</h3>
        <canvas id="incomeChart" height="200"></canvas>
    </div>
    
    <!-- Recent Students Table -->
    <div class="card">
        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Recently Joined Students</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Joined Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_students)): ?>
                        <tr><td colspan="3" style="text-align: center;">No students found</td></tr>
                    <?php else: ?>
                        <?php foreach($recent_students as $student): ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($student['joined_date'])) ?></td>
                                <td>
                                    <a href="/students/edit.php?id=<?= $student['id'] ?>" class="badge badge-success" style="text-decoration: none;">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('incomeChart').getContext('2d');
        const months = <?= json_encode(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) ?>;
        const data = <?= json_encode($income_data) ?>;
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Income (Rs)',
                    data: data,
                    backgroundColor: 'rgba(79, 141, 249, 0.8)',
                    borderColor: 'rgba(79, 141, 249, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e2e8f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>

<?php require_once '../includes/footer.php'; ?>
