<?php
    $servername="localhost";
    $username="root";
    $password="";
    $dbname="DB";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if(!$conn){
        die("Connection failed: ".mysqli_connect_error());
    }

    // Example table (Copy paste)
    $sql_users = "CREATE TABLE IF NOT EXISTS Users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        firstname VARCHAR(30) NOT NULL,
        lastname VARCHAR(30) NOT NULL,
        email VARCHAR(50),
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
        email VARCHAR(100) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql_members) === FALSE) {
        echo "Error creating Memberships table: " . $conn->error;
    }

    $conn->close();
?>