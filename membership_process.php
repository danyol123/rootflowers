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
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}
if (empty($password) || strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
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

// Step 4: Hash password (for security)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Step 5: Insert into database
$sql = "INSERT INTO membership (firstname, lastname, email, password)
        VALUES (?, ?, ?, ?)";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $firstname, $lastname, $email, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    echo "<h2>Registration Successful!</h2>";
    echo "<p>Welcome, <strong>$firstname $lastname</strong>.</p>";
    echo "<p>Your membership record has been saved.</p>";
    echo "<a href='index.php'>Return to Home</a>";
} else {
    echo "<h2>Error:</h2> " . mysqli_error($conn);
}

// Step 6: Close connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
