<?php
/*
 * File: topup_result.php
 * Description: Displays result of the top-up operation (success or failure).
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
session_start();

// Redirect if no status is set (prevent direct access)
if (!isset($_SESSION['topup_status'])) {
    header("Location: top_up.php");
    exit();
}

$status = $_SESSION['topup_status'];
$message = $_SESSION['topup_message'];
$amount = isset($_SESSION['topup_amount']) ? $_SESSION['topup_amount'] : 0;
$new_balance = isset($_SESSION['new_balance']) ? $_SESSION['new_balance'] : 0;

// Clear session data after retrieving
unset($_SESSION['topup_status']);
unset($_SESSION['topup_message']);
unset($_SESSION['topup_amount']);
unset($_SESSION['new_balance']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Root Flower">
    <meta name="keywords" content="Flowers, Shop, Kuching, Sarawak, Malaysia">
    <meta name="author" content="Daniel, Josiah, Alvin, Kheldy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
    <title>Top Up Result - Rootflower</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

<?php include 'navigation.php'; ?>

<main>
  <section class="form-section">
    <div class="form-container topup-result-container">
        <?php if ($status == 'success'): ?>
            <h1 class="topup-success-title">Top Up Successful!</h1>
            <p><?php echo $message; ?></p>
            
            <div class="topup-details">
                <p><strong>Amount Top Up:</strong> RM <?php echo number_format($amount, 2); ?></p>
                <p><strong>New Balance:</strong> RM <?php echo number_format($new_balance, 2); ?></p>
            </div>
            
            <p>Your transaction has been completed.</p>
        <?php else: ?>
            <h1 class="topup-failure-title">Top Up Failed</h1>
            <p><?php echo $message; ?></p>
            <p>Please try again or contact support.</p>
        <?php endif; ?>

        <p class="topup-return-link-container">
            <a href="top_up.php" class="topup-return-link">Return to Top Up Page</a>
        </p>
    </div>
  </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
