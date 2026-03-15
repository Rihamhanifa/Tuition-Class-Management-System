<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: /auth/login.php");
    exit();
}
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Tuition Management</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-brand">
                    <i data-lucide="graduation-cap"></i>
                    <span>TuitionSaaS</span>
                </div>
            </div>
            
            <nav class="sidebar-menu">
                <a href="/dashboard/index.php" class="menu-item <?= ($current_dir == 'dashboard') ? 'active' : '' ?>">
                    <i data-lucide="layout-dashboard"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/students/index.php" class="menu-item <?= ($current_dir == 'students') ? 'active' : '' ?>">
                    <i data-lucide="users"></i>
                    <span>Students</span>
                </a>
                <a href="/classes/index.php" class="menu-item <?= ($current_dir == 'classes') ? 'active' : '' ?>">
                    <i data-lucide="book-open"></i>
                    <span>Classes</span>
                </a>
                <a href="/attendance/index.php" class="menu-item <?= ($current_dir == 'attendance') ? 'active' : '' ?>">
                    <i data-lucide="calendar-check"></i>
                    <span>Attendance</span>
                </a>
                <a href="/payments/index.php" class="menu-item <?= ($current_dir == 'payments') ? 'active' : '' ?>">
                    <i data-lucide="credit-card"></i>
                    <span>Payments</span>
                </a>
                <a href="/reports/index.php" class="menu-item <?= ($current_dir == 'reports') ? 'active' : '' ?>">
                    <i data-lucide="bar-chart-3"></i>
                    <span>Reports</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <button class="toggle-sidebar-btn" id="toggleSidebar">
                    <i data-lucide="menu"></i>
                </button>
                
                <div class="nav-right">
                    <a href="/auth/logout.php" class="btn btn-secondary">
                        <i data-lucide="log-out" style="width: 18px; height: 18px;"></i>
                        Logout
                    </a>
                </div>
            </header>
            
            <!-- Dynamic Content Area -->
            <div class="content-area">
