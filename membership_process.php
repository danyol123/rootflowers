<?php
/*
 * File: membership_process.php
 * Description: Receives membership registration input and stores it in `memberships` table.
 * Author: Root Flower Team
 * Created: 2025-10-29
 */
session_start();
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
    <title>Root Flower</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>

    <!-- Navbar -->
    <?php include 'navigation.php'; ?>
    <!-- End of Navbar -->

    <main>
        <section class="form-section">
            <div class="form-container">
                <h1>Membership Registration Confirmation</h1>

                <?php
                // DB connection
                $servername = "localhost";
                $db_user = "root";
                $db_pass = "";
                $dbname = "DB";

                $conn = mysqli_connect($servername, $db_user, $db_pass, $dbname);

                if (!$conn) {
                    die("<p>Error: " . mysqli_connect_error() . "</p>");
                }

                // Honeypot check
                if (!empty($_POST['website'])) {
                    echo "<p>Error: Invalid submission detected.</p>";
                    echo "<p><a href='membership.php'>Return to the Entry Page</a></p>";
                    mysqli_close($conn);
                    exit();
                }

                // Retrieve form data
                $firstname = htmlspecialchars($_POST['firstname']);
                $lastname = htmlspecialchars($_POST['lastname']);
                $username = htmlspecialchars($_POST['username']);
                $email = htmlspecialchars($_POST['email']);

                // Verify CSRF token
                if (!isset($_POST['csrf']) || !isset($_SESSION['csrf_token']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
                    echo "<p class=\"rf-alert rf-alert-danger\">Invalid CSRF token.</p>";
                    echo "<p><a href='membership.php'>Return to the Entry Page</a></p>";
                    mysqli_close($conn);
                    include 'footer.php';
                    exit();
                }

                // Accept password field OR password_hash
                if (isset($_POST['password'])) {
                    $password_raw = $_POST['password'];
                } else {
                    $password_raw = isset($_POST['password_hash']) ? $_POST['password_hash'] : '';
                }

                // Validate password complexity: at least 8 characters, contains at least one letter and one number
                $pw_ok = true;
                if (trim($password_raw) === '') {
                    $pw_ok = false;
                    echo "<p class=\"rf-alert rf-alert-danger\">Password is required.</p>";
                } else if (!preg_match('/[A-Za-z]/', $password_raw) || !preg_match('/[0-9]/', $password_raw) || strlen($password_raw) < 8) {
                    $pw_ok = false;
                    echo "<p class=\"rf-alert rf-alert-danger\">Password must be at least 8 characters and contain both letters and numbers.</p>";
                }

                if (!$pw_ok) {
                    echo "<p><a href='membership.php'>Return to the Entry Page</a></p>";
                    mysqli_close($conn);
                    include 'footer.php';
                    exit();
                }

                // Hash password
                $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

                // Display submitted information
                echo "<p>Thank you for registering, <strong>$firstname $lastname!</strong></p>";
                echo "<p><strong>Username:</strong> $username</p>";
                echo "<p><strong>Email:</strong> $email</p>";

                // Check for duplicate username
                // Check for duplicate username
                $check_sql = "SELECT member_id FROM memberships WHERE username = ?";
                $check_stmt = mysqli_prepare($conn, $check_sql);
                mysqli_stmt_bind_param($check_stmt, "s", $username);
                mysqli_stmt_execute($check_stmt);
                mysqli_stmt_store_result($check_stmt);

                if (mysqli_stmt_num_rows($check_stmt) > 0) {
                    echo "<p class=\"rf-alert rf-alert-danger\">Username '<strong>" . $username . "</strong>' is already taken. Please choose another one.</p>";
                    echo "<p><a href='membership.php'>Return to the Entry Page</a></p>";
                    mysqli_stmt_close($check_stmt);
                    mysqli_close($conn);
                    include 'footer.php';
                    exit();
                }
                mysqli_stmt_close($check_stmt);

                // Insert data
                $sql = "INSERT INTO memberships (firstname, lastname, username, email, password_hash)
                VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssss", $firstname, $lastname, $username, $email, $password_hash);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<p>Your membership has been recorded successfully.</p>";
                } else {
                    echo "<p>Error: " . mysqli_error($conn) . "</p>";
                }

                echo "<p><a href='membership.php'>Return to the Entry Page</a></p>";

                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

</body>

</html>