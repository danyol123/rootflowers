<?php
/*
 * File: topup_process.php
 * Description: Handles top-up form submission for member accounts (accepts preset amounts or user input).
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
session_start();

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // validate CSRF
    if (!isset($_POST['csrf']) || !isset($_SESSION['csrf_token']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $_SESSION['topup_message'] = "Invalid CSRF token.";
        $_SESSION['topup_status'] = "error";
        header("Location: top_up.php");
        exit();
    }

    // Honeypot check
    if (!empty($_POST['website'])) {
        $_SESSION['topup_message'] = "Invalid submission detected.";
        $_SESSION['topup_status'] = "error";
        header("Location: top_up.php");
        exit();
    }

    // use preset_amount if provided else fallback to amount
    $amount = 0;
    if (isset($_POST['preset_amount'])) {
        $amount = floatval($_POST['preset_amount']);
    } else {
        $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    }
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $bank = isset($_POST['bank']) ? trim($_POST['bank']) : '';
    $account_number = isset($_POST['account_number']) ? trim($_POST['account_number']) : '';
    $member_id = $_SESSION['member_id'];

    if ($amount < 5 || $amount > 500) {
        $_SESSION['topup_message'] = "Invalid amount. Please top up between RM5 and RM500.";
        $_SESSION['topup_status'] = "error";
        header("Location: top_up.php");
        exit();
    }
    // sanitize account number (digits only) to avoid injection
    $account_number = preg_replace('/[^0-9]/', '', $account_number);

    // 1. Record transaction
    $stmt = $conn->prepare("INSERT INTO topup_history (member_id, amount, payment_method, bank, account_number, status) VALUES (?, ?, ?, ?, ?, 'success')");
    $stmt->bind_param("idsss", $member_id, $amount, $payment_method, $bank, $account_number);
    
    if ($stmt->execute()) {
        $stmt->close();

        // 2. Update user balance
        $stmt2 = $conn->prepare("UPDATE memberships SET balance = balance + ? WHERE member_id = ?");
        $stmt2->bind_param("di", $amount, $member_id);
        
        if ($stmt2->execute()) {
            // Fetch new balance
            $stmt3 = $conn->prepare("SELECT balance FROM memberships WHERE member_id = ?");
            $stmt3->bind_param("i", $member_id);
            $stmt3->execute();
            $res3 = $stmt3->get_result();
            $new_balance = 0;
            if ($row3 = $res3->fetch_assoc()) {
                $new_balance = $row3['balance'];
            }
            $stmt3->close();

            $_SESSION['topup_message'] = "Your wallet has been successfully topped up.";
            $_SESSION['topup_status'] = "success";
            $_SESSION['topup_amount'] = $amount;
            $_SESSION['new_balance'] = $new_balance;
        } else {
            $_SESSION['topup_message'] = "Transaction recorded but failed to update balance. Contact support.";
            $_SESSION['topup_status'] = "error";
        }
        $stmt2->close();
    } else {
        $_SESSION['topup_message'] = "Transaction failed. Please try again.";
        $_SESSION['topup_status'] = "error";
    }

    header("Location: topup_result.php");
    exit();
}

$conn->close();
?>
