<?php
/*
 * File: logout.php
 * Description: Ends the current user session and redirects to login.
 */
session_start();

// Clear all session data
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

session_destroy();

header('Location: login.php');
exit();
?>
