<?php
/*
 * File: login_process.php
 * Description: Authenticates users (admin and membership), logs successful login events to login_history table.
 * Author: Root Flower Team
 * Created: 2025-11-02
 */
session_start();

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

// Verify CSRF token
if (!isset($_POST['csrf']) || !isset($_SESSION['csrf_token']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
    $_SESSION['login_error'] = 'Invalid CSRF token.';
    header('Location: login.php');
    exit();
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

$servername = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'DB';
$conn = mysqli_connect($servername, $dbuser, $dbpass, $dbname);

if (!$conn) {
    $_SESSION['login_error'] = 'Database connection failed.';
    header('Location: login.php');
    exit();
}

// Check Admin
$stmt = $conn->prepare("SELECT id, username, password_hash FROM admin WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $admin = $res->fetch_assoc();
    // Case-insensitive password check: verify hash against lowercase input
    if (password_verify(strtolower($password), $admin['password_hash'])) {
        $_SESSION['is_admin'] = true;
        $_SESSION['username'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];

        // Log history
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $stmt_log = $conn->prepare("INSERT INTO login_history (username, ip, user_agent) VALUES (?, ?, ?)");
        $stmt_log->bind_param('sss', $admin['username'], $ip, $ua);
        $stmt_log->execute();
        $stmt_log->close();
        
        $conn->close();
        header('Location: admin_panel.php');
        exit();
    }
}
$stmt->close();

// Check Membership
$stmt = $conn->prepare('SELECT member_id, username, email, password_hash FROM memberships WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $user = $res->fetch_assoc();
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['member_id'] = $user['member_id'];
        
        // Log history
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $stmt_log = $conn->prepare("INSERT INTO login_history (username, ip, user_agent) VALUES (?, ?, ?)");
        $stmt_log->bind_param('sss', $user['username'], $ip, $ua);
        $stmt_log->execute();
        $stmt_log->close();

        $conn->close();
        header('Location: login_sucess.php');
        exit();
    }
}
$stmt->close();
$conn->close();

$_SESSION['login_error'] = 'Invalid credentials. Please try again.';
header('Location: login.php');
exit();
?>
