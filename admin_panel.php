<?php
/*
 * File: admin_panel.php
 * Description: Admin dashboard - quick links to admin functions.
 * Author: Root Flower Team
 * Created: 2025-11-17
 */
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Handle Admin Credentials Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_admin'])) {
    // Verify CSRF
    if (!isset($_POST['csrf']) || !isset($_SESSION['csrf_token']) || $_POST['csrf'] !== $_SESSION['csrf_token']) {
        $error = "Invalid CSRF token.";
    } else {
        $new_username = trim($_POST['username']);
        $new_password = $_POST['password'];
        $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;

        if (empty($new_username)) {
            $error = "Username cannot be empty.";
        } elseif ($admin_id == 0) {
            $error = "Invalid admin session.";
        } else {
            $servername = 'localhost';
            $dbuser = 'root';
            $dbpass = '';
            $dbname = 'DB';
            $conn = mysqli_connect($servername, $dbuser, $dbpass, $dbname);

            if ($conn) {
                // Update username
                $sql = "UPDATE admin SET username = ? WHERE id = ?";
                // If password is provided, update it too
                if (!empty($new_password)) {
                    $sql = "UPDATE admin SET username = ?, password_hash = ? WHERE id = ?";
                }

                $stmt = $conn->prepare($sql);
                if (!empty($new_password)) {
                    // Hash the lowercase version of the password to ensure case-insensitivity
                    $hashed_password = password_hash(strtolower($new_password), PASSWORD_DEFAULT);
                    $stmt->bind_param("ssi", $new_username, $hashed_password, $admin_id);
                } else {
                    $stmt->bind_param("si", $new_username, $admin_id);
                }

                if ($stmt->execute()) {
                    $message = "Admin credentials updated successfully.";
                    $_SESSION['username'] = $new_username; // Update session username
                } else {
                    $error = "Error updating credentials: " . $conn->error;
                }
                $stmt->close();
                $conn->close();
            } else {
                $error = "Database connection failed.";
            }
        }
    }
}

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="description" content="Root Flower">
  <meta name="keywords" content="Flowers, Shop, Kuching, Sarawak, Malaysia">
  <meta name="author" content="Daniel, Josiah, Alvin, Kheldy">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
  <title>Root Flower — Admin Panel</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<main class="admin-main">
    <!-- Sidebar -->
    <?php include 'admin_sidebar.php'; ?>

    <!-- Right Content -->
    <section class="admin-content" style="padding: 20px; width: 100%;">
      <div class="rf-list-container">
        <div class="rf-panel">
          <div class="rf-meta">
            <div>
              <h1 class="rf-h1">Admin Dashboard</h1>
              <p class="rf-muted">Quick links to manage site content and view records.</p>
            </div>
          </div>

          <div>
            <a class="rf-btn rf-btn-ghost" href="view_register.php">Workshop Registrations</a>
            <a class="rf-btn rf-btn-ghost" href="view_enquiry.php">Enquiries</a>
            <a class="rf-btn rf-btn-ghost" href="view_membership.php">Memberships</a>
            <a class="rf-btn rf-btn-ghost" href="view_login.php">Login History</a>
            <a class="rf-btn rf-btn-ghost" href="recycle.php">Recycle Bin</a>
            <a class="rf-btn rf-btn-ghost" href="view_promotion.php">Promotion Module</a>
          </div>
        </div>

        <!-- Admin Settings Panel -->
        <div class="rf-panel">
          <div class="rf-meta">
            <div>
              <h2 class="rf-h2" style="margin:0; font-size: 1.5rem;">Admin Settings</h2>
              <p class="rf-muted">Update your admin username and password.</p>
            </div>
          </div>

          <?php if ($message): ?>
              <div class="rf-alert-info" style="background: #d1fae5; color: #065f46; margin-bottom: 1rem;">
                  <?php echo htmlspecialchars($message); ?>
              </div>
          <?php endif; ?>
          
          <?php if ($error): ?>
              <div class="rf-alert-info" style="background: #fee2e2; color: #991b1b; margin-bottom: 1rem;">
                  <?php echo htmlspecialchars($error); ?>
              </div>
          <?php endif; ?>

          <form action="admin_panel.php" method="POST">
              <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf_token']; ?>">
              <input type="hidden" name="update_admin" value="1">
              
              <label for="username">Username</label>
              <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
              
              <label for="password">New Password (leave blank to keep current)</label>
              <input type="password" name="password" id="password">
              
              <button type="submit" class="rf-btn rf-btn-primary" style="background-color: #463b2b; color: white;">Update Credentials</button>
          </form>
        </div>

      </div>
    </section>
</main>
</body>
</html>
