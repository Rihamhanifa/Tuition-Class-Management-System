<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard/index.php");
} else {
    // Otherwise, redirect to the login page
    header("Location: auth/login.php");
}
exit();
?>
