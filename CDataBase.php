<?php
/*
 * File: CDataBase.php
 * Description: Database helper functions and connection logic for the site.
 * Author: Root Flower Team
 * Created: 2025-11-05
 */
    $servername="localhost";
    $username="root";
    $password="";

    $conn = mysqli_connect($servername, $username, $password);
    
    if(!$conn){
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "CREATE DATABASE IF NOT EXISTS DB";
    if (!$conn->query($sql)) {
        die("Error creating database: " . $conn->error);
    }

    $conn->close();
?> 