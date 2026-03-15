<?php
require_once '../config/db.php';
require_once '../includes/header.php';
?>

<div class="page-header">
    <h1 class="page-title">Reports & Analytics</h1>
</div>

<div class="grid grid-cols-2">
    <!-- Student Reports -->
    <div class="card" style="display: flex; flex-direction: column; gap: 15px;">
        <h3 style="display: flex; align-items: center; gap: 10px; font-size: 1.1rem;">
            <i data-lucide="users" style="color: var(--primary-color);"></i> Student Data
        </h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Export the complete list of registered students including their contact information and joined dates.</p>
        <div style="margin-top: auto;">
            <a href="export.php?type=students" class="btn btn-primary"><i data-lucide="download"></i> Export as CSV</a>
        </div>
    </div>

    <!-- Financial Reports -->
    <div class="card" style="display: flex; flex-direction: column; gap: 15px;">
        <h3 style="display: flex; align-items: center; gap: 10px; font-size: 1.1rem;">
            <i data-lucide="credit-card" style="color: #166534;"></i> Payment Records
        </h3>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Download all historical payment transactions for accounting and auditing purposes.</p>
        <div style="margin-top: auto;">
            <a href="export.php?type=payments" class="btn btn-primary" style="background-color: #166534;"><i data-lucide="download"></i> Export as CSV</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
