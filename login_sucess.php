<?php
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
    <main class="form-section">
        <div class="form-container">
            <h1>Successful login</h1>
            <p>Thank you for logging in, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>