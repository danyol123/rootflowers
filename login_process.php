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

// Check Admin (case-insensitive username)
$stmt = $conn->prepare("SELECT id, username, password_hash FROM admin WHERE LOWER(username) = LOWER(?) LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $admin = $res->fetch_assoc();
    if (password_verify($password, $admin['password_hash'])) {
        $_SESSION['is_admin'] = true;
        $_SESSION['username'] = $admin['username'];
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_role'] = 'admin';

        // Ensure admin has a linked membership record for wallet actions
        $member_id = null;
        $stmt_member = $conn->prepare('SELECT member_id FROM memberships WHERE LOWER(username) = LOWER(?) LIMIT 1');
        $stmt_member->bind_param('s', $admin['username']);
        $stmt_member->execute();
        $res_member = $stmt_member->get_result();

        if ($res_member && $res_member->num_rows > 0) {
            $member = $res_member->fetch_assoc();
            $member_id = $member['member_id'];
        } else {
            $default_firstname = 'Admin';
            $default_lastname = 'User';
            $default_email = $admin['username'] . '@rootflower.local';
            $stmt_create = $conn->prepare('INSERT INTO memberships (firstname, lastname, username, email, password_hash, balance) VALUES (?, ?, ?, ?, ?, 0.00)');
            $stmt_create->bind_param('sssss', $default_firstname, $default_lastname, $admin['username'], $default_email, $admin['password_hash']);
            if ($stmt_create->execute()) {
                $member_id = $conn->insert_id;
            }
            $stmt_create->close();
        }

        if ($member_id !== null) {
            $_SESSION['member_id'] = $member_id;
        }
        $stmt_member->close();

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

// Check Membership (case-insensitive username)
$stmt = $conn->prepare('SELECT member_id, username, email, password_hash FROM memberships WHERE LOWER(username) = LOWER(?) LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows > 0) {
    $user = $res->fetch_assoc();
    if (password_verify($password, $user['password_hash'])) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['member_id'] = $user['member_id'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_role'] = 'member';
        
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
