<?php
session_start();

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Simple hardcoded admin check: Name: Admin, Password: Admin
if ($username === 'Admin' && $password === 'Admin') {
    // Set admin session (use lowercase 'admin' to match existing checks)
    $_SESSION['is_admin'] = true;
    $_SESSION['username'] = 'admin';

    // Redirect to admin panel
    header('Location: admin_panel.php');
    exit();
} else {
    // Set an error message and redirect back to login
    $_SESSION['login_error'] = 'Invalid credentials. Please try again.';
    header('Location: login.php');
    exit();
}
?>
