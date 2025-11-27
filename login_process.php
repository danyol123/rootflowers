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

if (strtolower($username) == strtolower('Admin') && strtolower($password) == strtolower('Admin')){
    $_SESSION['is_admin'] = true;
    $_SESSION['username'] = 'admin';

    // Record login into logins table
    $servername = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'DB';
    $conn = mysqli_connect($servername, $dbuser, $dbpass, $dbname);
    if ($conn) {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $stmt = $conn->prepare("INSERT INTO login_history (username, ip, user_agent) VALUES (?, ?, ?)");
        if ($stmt) {
            $u = 'admin';
            $stmt->bind_param('sss', $u, $ip, $ua);
            $stmt->execute();
            $stmt->close();
        }
        $conn->close();
    }

    // Redirect to admin panel
    header('Location: admin_panel.php');
    exit();
} else {
    // Try matching a membership user
    $servername = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbname = 'DB';
    $conn = mysqli_connect($servername, $dbuser, $dbpass, $dbname);
    if ($conn) {
        $stmt = $conn->prepare('SELECT member_id, username, password_hash FROM memberships WHERE username = ? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res && $res->num_rows > 0) {
                $user = $res->fetch_assoc();
                $stmt->close();
                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['member_id'] = $user['member_id'];
                    // record login for membership user
                    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
                    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
                    $stmt2 = $conn->prepare("INSERT INTO login_history (username, ip, user_agent) VALUES (?, ?, ?)");
                    if ($stmt2) {
                        $u = $user['username'];
                        $stmt2->bind_param('sss', $u, $ip, $ua);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                    $conn->close();
                    header('Location: login_sucess.php');
                    exit();
                }
            } else {
                $stmt->close();
            }
        }
        $conn->close();
    }
    // Set an error message and redirect back to login
    $_SESSION['login_error'] = 'Invalid credentials. Please try again.';
    header('Location: login.php');
    exit();
}
?>
