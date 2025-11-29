<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle File Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload'])) {
    $section = $_POST['section'];
    $target_dir = "Pictures/Promotion/";
    
    // Ensure directory exists
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $filename = basename($_FILES["fileToUpload"]["name"]);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $message = "Sorry, file already exists.";
        $uploadOk = 0;
    }

    // Check file size (limit to 5MB)
    if ($_FILES["fileToUpload"]["size"] > 5000000) {
        $message = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        if (empty($message)) $message = "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            // Insert into DB
            $stmt = $conn->prepare("INSERT INTO promotion_images (section, image_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $section, $target_file);
            if ($stmt->execute()) {
                $message = "The file ". htmlspecialchars($filename). " has been uploaded.";
            } else {
                $message = "Error updating database: " . $conn->error;
            }
            $stmt->close();
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }
}

// Handle Deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Get file path to delete file
    $stmt = $conn->prepare("SELECT image_path FROM promotion_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $file_path = $row['image_path'];
        // Delete from DB
        $del_stmt = $conn->prepare("DELETE FROM promotion_images WHERE id = ?");
        $del_stmt->bind_param("i", $id);
        if ($del_stmt->execute()) {
            // Delete file from server
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $message = "Image deleted successfully.";
        } else {
            $message = "Error deleting record: " . $conn->error;
        }
        $del_stmt->close();
    }
    $stmt->close();
}

// Fetch Images
$sections = ['Special Discount', 'Early Bird', 'Give Away'];
$images_by_section = [];

foreach ($sections as $sec) {
    $stmt = $conn->prepare("SELECT * FROM promotion_images WHERE section = ?");
    $stmt->bind_param("s", $sec);
    $stmt->execute();
    $result = $stmt->get_result();
    $images_by_section[$sec] = [];
    while ($row = $result->fetch_assoc()) {
        $images_by_section[$sec][] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Root Flower - Promotion Module</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body class="rf-root">

<main class="admin-main">
    <aside class="admin-sidebar">
        <!-- Sidebar -->
        <?php include 'admin_sidebar.php'; ?>
    </aside>

    <section class="admin-content">
        <div class="rf-list-container">
            
            <div class="rf-meta rf-meta-header">
                <div>
                    <h1 class="rf-h1">Promotion Module Management</h1>
                    <p class="rf-muted">Manage the images displayed on the public promotion page.</p>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="rf-alert rf-alert-info">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="promotion-admin-wrapper">
                <!-- Main Content Area -->
                <div class="promotion-content-area">
                    <?php foreach ($sections as $sec): ?>
                        <?php $secId = strtolower(str_replace(' ', '-', $sec)); ?>
                        <div id="<?php echo $secId; ?>" class="rf-panel">
                            <div class="section-header">
                                <h2><?php echo $sec; ?></h2>
                            </div>
                            
                            <div class="image-grid">
                                <?php if (count($images_by_section[$sec]) > 0): ?>
                                    <?php foreach ($images_by_section[$sec] as $img): ?>
                                        <div class="image-card">
                                            <img src="<?php echo $img['image_path']; ?>" alt="Promotion Image" class="image-preview">
                                            <div class="image-actions">
                                                <a href="?delete_id=<?php echo $img['id']; ?>" class="rf-btn rf-btn-danger">Delete</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="rf-muted rf-empty">No images in this section.</p>
                                <?php endif; ?>
                            </div>

                            <div class="upload-area">
                                <h3 class="rf-upload-heading">Add New Image</h3>
                                <form action="" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="section" value="<?php echo $sec; ?>">
                                    <input type="file" name="fileToUpload" required class="rf-btn rf-btn-ghost">
                                    <button type="submit" name="upload" class="rf-btn rf-btn-complete">Upload Image</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Inner Sidebar Area -->
                <div class="promotion-sidebar-area">
                    <aside class="activity-sidebar-container activity-no-pad" id="promotion-sidebar">
                        <div class="activity-sidebar">
                            <h2>Promotion</h2>
                            <ul class="category-list">
                                <?php foreach ($sections as $sec): ?>
                                    <?php $secId = strtolower(str_replace(' ', '-', $sec)); ?>
                                    <li><a href="#<?php echo $secId; ?>" class="category-item"><?php echo $sec; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                </div>
            </div>

        </div>
    </section>
</main>

</body>
</html>
