<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: /auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    
    if ($id) {
        try {
            $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
            $stmt->execute([$id]);
        } catch (PDOException $e) {
            // Handle error, maybe set a session flash message
        }
    }
}

header("Location: index.php");
exit();
?>
