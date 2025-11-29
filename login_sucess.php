<?php
/*
 * File: login_sucess.php
 * Description: Post-login confirmation for membership users.
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
session_start();

// If the user is not logged in, redirect to the login page.
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <?php include 'navigation.php'; ?>
    <main>
        <section class="form-section">
            <div class="form-container">
                <h1>Login Successful</h1>
                <p>Thank you for logging in, <strong><?php echo htmlspecialchars($_SESSION['username']); ?>!</strong></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <p><strong>Email:</strong> <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'N/A'; ?></p>
                <p>You have successfully logged in.</p>
                <p><a href="index.php">Return to the Entry Page</a></p>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>