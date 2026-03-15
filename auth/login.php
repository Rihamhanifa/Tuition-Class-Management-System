<?php
session_start();
// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: ../dashboard/index.php");
    exit();
}
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Login success
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                header("Location: ../dashboard/index.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tuition Management</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--bg-color) 0%, #e2e8f0 100%);
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        .login-logo {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .login-title {
            margin-bottom: 30px;
            color: var(--text-main);
            font-weight: 600;
        }

        .error-msg {
            background-color: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .login-btn {
            width: 100%;
            justify-content: center;
            padding: 12px;
            font-size: 1rem;
            margin-top: 10px;
        }
    </style>
</head>

<body class="login-page">

    <div class="card login-card">
        <div class="login-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-graduation-cap">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z" />
                <path d="M6 12v5c3 3 9 3 12 0v-5" />
            </svg>
        </div>
        <h2 class="login-title">Admin Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Username" required
                    autocomplete="username">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required
                    autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary login-btn">Sign In </button>
        </form>
    </div>

</body>

</html>