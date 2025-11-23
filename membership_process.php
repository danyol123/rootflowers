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

        // Retrieve form data
        $firstname = htmlspecialchars($_POST['firstname']);
        $lastname = htmlspecialchars($_POST['lastname']);
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);

        // Accept password field OR password_hash
        if (isset($_POST['password'])) {
            $password_raw = $_POST['password'];
        } else {
            $password_raw = $_POST['password_hash'];
        }

        // Hash password
        $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

        // Display submitted information
        echo "<p>Thank you for registering, <strong>$firstname $lastname!</strong></p>";
        echo "<p><strong>Username:</strong> $username</p>";
        echo "<p><strong>Email:</strong> $email</p>";

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
