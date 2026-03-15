<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: /auth/login.php");
    exit();
}

require_once '../config/db.php';

$type = $_GET['type'] ?? '';

if ($type === 'students') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="students_report_' . date('Ymd') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'First Name', 'Last Name', 'Phone', 'Email', 'Joined Date']);
    
    $stmt = $pdo->query("SELECT id, first_name, last_name, phone, email, joined_date FROM students ORDER BY id ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
} 
elseif ($type === 'payments') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payments_report_' . date('Ymd') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Transaction ID', 'Student ID', 'Student Name', 'Amount (Rs)', 'Month', 'Year', 'Payment Date', 'Status']);
    
    $stmt = $pdo->query("
        SELECT p.id, p.student_id, CONCAT(s.first_name, ' ', s.last_name) as student_name, 
               p.amount, p.payment_month, p.payment_year, p.payment_date, p.status 
        FROM payments p 
        LEFT JOIN students s ON p.student_id = s.id 
        ORDER BY p.payment_date DESC
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit();
} else {
    die("Invalid export type.");
}
