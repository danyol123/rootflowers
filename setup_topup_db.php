<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Add 'balance' column to 'memberships' table if it doesn't exist
$sql = "SHOW COLUMNS FROM memberships LIKE 'balance'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "ALTER TABLE memberships ADD COLUMN balance DECIMAL(10,2) DEFAULT 0.00 AFTER password_hash";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'balance' added to 'memberships' table successfully.<br>";
    } else {
        echo "Error adding column 'balance': " . $conn->error . "<br>";
    }
} else {
    echo "Column 'balance' already exists in 'memberships' table.<br>";
}

// 2. Create 'topup_history' table
$sql = "CREATE TABLE IF NOT EXISTS topup_history (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    member_id INT(6) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    bank VARCHAR(50),
    account_number VARCHAR(50),
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'success'
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'topup_history' created successfully (or already exists).<br>";
} else {
    echo "Error creating table 'topup_history': " . $conn->error . "<br>";
}

$conn->close();
?>
