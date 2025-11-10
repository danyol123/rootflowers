<?php
    $servername="localhost";
    $username="root";
    $password="";

    $conn = mysqli_connect($servername, $username, $password);
    
    if(!$conn){
        die("Connection failed: ".mysqli_connect_error());
    }

    $sql = "CREATE DATABASE IF NOT EXISTS DB";
    if ($conn->query($sql) === FALSE) {
        die("Error creating database: " . $conn->error);
    }

    $conn->close();
?> 