<?php
    session_start();
    require_once("CTable.php");

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "DB";

    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Data from submitted form
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $enquiry_type = trim($_POST['enquiry-type']);
        $comments = trim($_POST['comments']);
        
        $errors = array();
        
        if (empty($firstname)) $errors[] = "First name is required";
        if (empty($lastname)) $errors[] = "Last name is required";
        if (empty($email)) $errors[] = "Email is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
        if (empty($phone)) $errors[] = "Phone number is required";
        if (!preg_match("/^[0-9]{10}$/", $phone)) $errors[] = "Phone number must be 10 digits";
        if (empty($enquiry_type)) $errors[] = "Enquiry type is required";
        if (empty($comments)) $errors[] = "Comments are required";
        
        if (empty($errors)) {
            // Prepare and execute the SQL query
            $sql = "INSERT INTO enquiry (firstname, lastname, email, phone, enquiry_type, comments) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt->execute([$firstname, $lastname, $email, $phone, $enquiry_type, $comments])) {
                $_SESSION['success_message'] = "Thank you! Your enquiry has been submitted successfully.";
                header("Location: enquiry.php");
                exit();
            } else {
                $_SESSION['error_message'] = "Sorry, there was an error submitting your enquiry. Please try again.";
                header("Location: enquiry.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = implode("<br>", $errors);
            $_SESSION['form_data'] = $_POST; // Save form data for refilling the form
            header("Location: enquiry.php");
            exit();
        }
    } else {
        // If someone tries to access this file directly
        header("Location: enquiry.php");
        exit();
    }
?>
