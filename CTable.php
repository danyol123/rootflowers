<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DB";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Example table (Copy paste)
$sql_users = "CREATE TABLE IF NOT EXISTS Logins (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(25) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP
    )";

if ($conn->query($sql_users) === FALSE) {
    echo "Error creating Users table: " . $conn->error;
}

// Enquiry table
$sql_enquiry = "CREATE TABLE IF NOT EXISTS enquiry (
        enquiry_id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(25) NOT NULL,
        lastname VARCHAR(25) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(10) NOT NULL,
        enquiry_type VARCHAR(50) NOT NULL,
        comments TEXT NOT NULL,
        submit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

if ($conn->query($sql_enquiry) === FALSE) {
    echo "Error creating Enquiry table: " . $conn->error;
}

// Workshop registrations table
$sql_registrations = "CREATE TABLE IF NOT EXISTS registrations (
        registration_id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(25) NOT NULL,
        lastname VARCHAR(25) NOT NULL,
        email VARCHAR(100) NOT NULL,
        street VARCHAR(100),
        city VARCHAR(50),
        state VARCHAR(50),
        postcode VARCHAR(10),
        phone VARCHAR(15),
        workshop_date DATE,
        participants VARCHAR(10),
        workshop_type VARCHAR(30),
        addons TEXT,
        comments TEXT,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

if ($conn->query($sql_registrations) === FALSE) {
    echo "Error creating Registrations table: " . $conn->error;
}

// Memberships table
$sql_members = "CREATE TABLE IF NOT EXISTS memberships (
        member_id INT AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(25) NOT NULL,
        lastname VARCHAR(25) NOT NULL,
        username VARCHAR(25) NOT NULL,
        email VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

if ($conn->query($sql_members) === FALSE) {
    echo "Error creating Memberships table: " . $conn->error;
}

// Login history table
$sql_logins = "CREATE TABLE IF NOT EXISTS login_history (
        history_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NULL,
        ip VARCHAR(45) NULL,
        user_agent TEXT NULL,
        login_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

if ($conn->query($sql_logins) === FALSE) {
    echo "Error creating Logins table: " . $conn->error;
}

// Promotion Images table
$sql_promo = "CREATE TABLE IF NOT EXISTS promotion_images (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        section VARCHAR(50) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

if ($conn->query($sql_promo) === FALSE) {
    echo "Error creating Promotion Images table: " . $conn->error;
}

// Initial data for Promotion Images
$initial_data = [
    ['section' => 'Special Discount', 'image_path' => 'Pictures/Promotion/discount2-1.jpg'],
    ['section' => 'Special Discount', 'image_path' => 'Pictures/Promotion/discount2.jpg'],
    ['section' => 'Special Discount', 'image_path' => 'Pictures/Promotion/discount2-2.jpg'],
    ['section' => 'Early Bird', 'image_path' => 'Pictures/Promotion/earlybird1.jpg'],
    ['section' => 'Early Bird', 'image_path' => 'Pictures/Promotion/earlybird2.jpg'],
    ['section' => 'Early Bird', 'image_path' => 'Pictures/Promotion/earlybird3.jpg'],
    ['section' => 'Give Away', 'image_path' => 'Pictures/Promotion/giveaway1.jpg'],
    ['section' => 'Give Away', 'image_path' => 'Pictures/Promotion/giveaway2.jpg']
];

// Check if table is empty before inserting
$result = $conn->query("SELECT count(*) as count FROM promotion_images");
if ($result) {
    $row = $result->fetch_assoc();
    if ($row['count'] == 0) {
        $stmt = $conn->prepare("INSERT INTO promotion_images (section, image_path) VALUES (?, ?)");
        foreach ($initial_data as $item) {
            $stmt->bind_param("ss", $item['section'], $item['image_path']);
            $stmt->execute();
        }
        $stmt->close();
    }
}

// Add 'balance' column to 'memberships' table if it doesn't exist
$sql_check_bal = "SHOW COLUMNS FROM memberships LIKE 'balance'";
$result_bal = $conn->query($sql_check_bal);
if ($result_bal->num_rows == 0) {
    $sql_add_bal = "ALTER TABLE memberships ADD COLUMN balance DECIMAL(10,2) DEFAULT 0.00 AFTER password_hash";
    if ($conn->query($sql_add_bal) === TRUE) {
        echo "Column 'balance' added to 'memberships' table successfully.<br>";
    } else {
        echo "Error adding column 'balance': " . $conn->error . "<br>";
    }
}

// Create 'topup_history' table
$sql_topup = "CREATE TABLE IF NOT EXISTS topup_history (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        member_id INT(6) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        bank VARCHAR(50),
        account_number VARCHAR(50),
        transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'success'
    )";

if ($conn->query($sql_topup) === FALSE) {
    echo "Error creating topup_history table: " . $conn->error;
}

$conn->close();
?>