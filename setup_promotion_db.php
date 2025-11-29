<?php
/*
 * File: setup_promotion_db.php
 * Description: Helper to set up promotion_images table and seed initial data if required.
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
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

// Create table
$sql = "CREATE TABLE IF NOT EXISTS promotion_images (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    section VARCHAR(50) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Initial data
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
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $stmt = $conn->prepare("INSERT INTO promotion_images (section, image_path) VALUES (?, ?)");
    foreach ($initial_data as $item) {
        $stmt->bind_param("ss", $item['section'], $item['image_path']);
        $stmt->execute();
    }
    $stmt->close();
}
$conn->close();
?>
