<?php
session_start();
/*
 * File: register_process.php
 * Description: Process workshop registration submissions and shows confirmation.
 * Author: Root Flower Team
 * Created: 2025-10-29
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
        <h1>Workshop Registration</h1>
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "DB";

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Verify CSRF token
        if (!isset($_POST['csrf']) || !isset($_SESSION['csrf_token']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
            echo "<p>Invalid CSRF token. Please try again.</p>";
            echo "<p><a href='register.php'>Return to the Entry Page</a></p>";
            exit();
        }
        echo "<p>Thank you for registering, <strong>" . htmlspecialchars($_POST["firstName"]) . " " . htmlspecialchars($_POST["lastName"]) . "!</strong></p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($_POST["email"]) . "</p>";
        echo "<p><strong>Address:</strong> " . htmlspecialchars($_POST["street"]) . ", " . htmlspecialchars($_POST["city"]) . ", " . htmlspecialchars($_POST["state"]) . ", " . htmlspecialchars($_POST["postcode"]) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($_POST["phone"]) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($_POST["workshopDate"]) . "</p>";
        echo "<p><strong>Number of Participants:</strong> " . htmlspecialchars($_POST["participants"]) . "</p>";
        echo "<p><strong>Workshop Type:</strong> " . htmlspecialchars($_POST["workshopType"]) . "</p>";
        echo "<p><strong>Add-ons:</strong> </p>";
        if (!empty($_POST["addons"])) {
            echo "<ul>";
            foreach ($_POST["addons"] as $addon) {
                echo "<li>" . htmlspecialchars($addon) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No add-ons selected.</p>";
        }
        echo "<p><strong>Comments:</strong> " . nl2br(htmlspecialchars($_POST["comments"])) . "</p>";

        $firstname = htmlspecialchars($_POST["firstName"]);
        $lastname = htmlspecialchars($_POST["lastName"]);
        $email = htmlspecialchars($_POST["email"]);
        $street = htmlspecialchars($_POST["street"]);
        $city = htmlspecialchars($_POST["city"]);
        $state = htmlspecialchars($_POST["state"]);
        $postcode = htmlspecialchars($_POST["postcode"]);
        $phone = htmlspecialchars($_POST["phone"]);
        $workshop_date = htmlspecialchars($_POST["workshopDate"]);
        $participants = htmlspecialchars($_POST["participants"]);
        $workshop_type = htmlspecialchars($_POST["workshopType"]);
        
        $addons = "";
        if (!empty($_POST["addons"])) {
            $addons = implode(", ", $_POST["addons"]);
        }
        $comments = htmlspecialchars($_POST["comments"]);

        $sql = "INSERT INTO registrations (firstname, lastname, email, street, city, state, postcode, phone, workshop_date, participants, workshop_type, addons, comments) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssssss", $firstname, $lastname, $email, $street, $city, $state, $postcode, $phone, $workshop_date, $participants, $workshop_type, $addons, $comments);

        if (mysqli_stmt_execute($stmt)) {
            echo "<p>Your registration details have been received and saved successfully.</p>";
        } else {
            echo "<p>Error: " . mysqli_error($conn) . "</p>";
        }

        echo "<p><a href='register.php'>Return to the Entry Page</a></p>";

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