<?php
session_start();
/*
 * File: enquiry_process.php
 * Description: Handles front-facing enquiry submissions, performs honeypot anti-spam check, and stores records.
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
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
        <h1>Enquiry Confirmation</h1>
        <?php
        // Database connection
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "DB";

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Honeypot check - if filled, it's likely a bot
        if (!empty($_POST['website'])) {
            echo "<p>Error: Invalid submission detected.</p>";
            echo "<p><a href='enquiry.php'>Return to the Entry Page</a></p>";
            mysqli_close($conn);
            exit();
        }

        // CSRF validation
        if (!isset($_POST['csrf']) || !isset($_SESSION['csrf_token']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
          echo "<p>Error: Invalid CSRF token.</p>";
          echo "<p><a href='enquiry.php'>Return to the Entry Page</a></p>";
          mysqli_close($conn);
          exit();
        }

        // Get form data
        $firstname = htmlspecialchars($_POST["firstname"]);
        $lastname = htmlspecialchars($_POST["lastname"]);
        $email = htmlspecialchars($_POST["email"]);
        $phone = htmlspecialchars($_POST["phone"]);
        $enquiry_type = htmlspecialchars($_POST["enquiry-type"]);
        $comments = htmlspecialchars($_POST["comments"]);

        // Display submitted information
        echo "<p>Thank you for your enquiry, <strong>" . $firstname . " " . $lastname . "!</strong></p>";
        echo "<p><strong>Email:</strong> " . $email . "</p>";
        echo "<p><strong>Phone:</strong> " . $phone . "</p>";
        echo "<p><strong>Enquiry Type:</strong> " . $enquiry_type . "</p>";
        echo "<p><strong>Comments:</strong> " . nl2br($comments) . "</p>";

        // Insert into database
        $sql = "INSERT INTO enquiry (firstname, lastname, email, phone, enquiry_type, comments) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $firstname, $lastname, $email, $phone, $enquiry_type, $comments);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Your enquiry has been recorded successfully.</p>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }

        echo "<p><a href='enquiry.php'>Return to the Entry Page</a></p>";

        // Close connection
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