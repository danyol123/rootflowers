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

<?php

// Step 1: Connect to the database
$servername = "localhost";
$username = "root";      
$password = "";           
$dbname = "DB";   

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("<h2>Database connection failed: " . mysqli_connect_error() . "</h2>");
}

// Step 2: Retrieve and validate form data
$firstname = trim($_POST['firstname']);
$lastname = trim($_POST['lastname']);
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

$errors = [];

// Simple validation
if (empty($firstname) || !preg_match("/^[A-Za-z]+$/", $firstname)) {
    $errors[] = "Invalid first name.";
}
if (empty($lastname) || !preg_match("/^[A-Za-z]+$/", $lastname)) {
    $errors[] = "Invalid last name.";
}

$username = trim($_POST['username']);
if (empty($username) || !preg_match("/^[A-Za-z]+$/", $username)) {
    $errors[] = "Invalid username.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}
if (empty($password) || 
    strlen($password) < 8 || 
    !preg_match('/[0-9]/', $password) ||      
    !preg_match('/[^A-Za-z0-9]/', $password) 
) {
    $errors[] = "Password must be at least 8 characters long and include at least 1 number and 1 symbol.";
}

// Step 3: If errors exist, show them and stop
if (count($errors) > 0) {
    echo "<h2>Form submission failed:</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "<p><a href='membership.php'>Go back to form</a></p>";
    exit();
}

// Step 4: Insert into database
$sql = "INSERT INTO memberships (firstname, lastname, username, email, password)
        VALUES (?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die("<h2>SQL Prepare Failed: " . mysqli_error($conn) . "</h2>");
}

mysqli_stmt_bind_param($stmt, "sssss", $firstname, $lastname, $username, $email, $password);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2>Registration successful!</h2>";
} else {
    echo "<h2>Error inserting data: " . mysqli_error($conn) . "</h2>";
}

// Step 5: Close connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>

<!-- Footer -->
  <?php include 'footer.php'; ?>
</body>
</html>
