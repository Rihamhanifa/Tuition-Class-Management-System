<?php
require_once '../config/db.php';
require_once '../includes/header.php';

// Fetch recent payments globally
try {
    $stmt = $pdo->query("
        SELECT p.*, s.first_name, s.last_name 
        FROM payments p 
        JOIN students s ON p.student_id = s.id 
        ORDER BY p.payment_date DESC, p.created_at DESC 
        LIMIT 20
    ");
    $payments = $stmt->fetchAll();
    
    // Total income this year
    $year = date('Y');
    $yStmt = $pdo->prepare("SELECT SUM(amount) FROM payments WHERE payment_year = ?");
    $yStmt->execute([$year]);
    $yearly_income = $yStmt->fetchColumn() ?: 0;
    
} catch (PDOException $e) {
    echo "<div class='badge badge-danger'>Error: " . $e->getMessage() . "</div>";
    $payments = [];
    $yearly_income = 0;
}
?>

<div class="page-header">
    <h1 class="page-title">Fee Management</h1>
    <a href="add.php" class="btn btn-primary"><i data-lucide="plus"></i> Record Payment</a>
</div>

<div class="grid grid-cols-4" style="margin-bottom: 20px;">
    <div class="card" style="grid-column: span 2;">
        <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px; font-weight: 500;">Yearly Revenue (<?= $year ?>)</h3>
        <div style="font-size: 2rem; font-weight: 700; color: var(--primary-color);">Rs. <?= number_format($yearly_income, 2) ?></div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Recent Payments</h3>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Ref ID</th>
                    <th>Student Name</th>
                    <th>Month/Year</th>
                    <th>Date Paid</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($payments)): ?>
                    <tr><td colspan="6" style="text-align: center;">No payments recorded yet.</td></tr>
                <?php else: ?>
                    <?php foreach($payments as $p): ?>
                        <tr>
                            <td style="color: var(--text-muted);">#PAY-<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td style="font-weight: 500;"><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></td>
                            <td><?= htmlspecialchars($p['payment_month']) ?> <?= $p['payment_year'] ?></td>
                            <td><?= date('M d, Y', strtotime($p['payment_date'])) ?></td>
                            <td style="font-weight: 600; color: var(--text-main);">Rs. <?= number_format($p['amount'], 2) ?></td>
                            <td><span class="badge badge-success"><?= htmlspecialchars($p['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
