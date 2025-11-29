<?php
/*
 * File: login.php
 * Description: Login page for members and admin with a simple form.
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
session_start();
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
  <title>Root Flower - Login</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>

  <!-- Navbar -->
  <?php
  include 'navigation.php';
  ?>
  <!-- End of Navbar -->

  <!-- ====== LOGIN PAGE CONTENT ====== -->
  <main>
  <section class="login-section">
    <div class="login-container">
      <h2 class="login-title">Welcome Back</h2>
      <p class="login-subtitle">Please log in to continue</p>
      <?php
      if (!isset($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
      }
      $csrf = $_SESSION['csrf_token'];
      ?>
      <form class="login-form" action="login_process.php" method="post">
        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
        <div class="form-group">
          <label for="name">Username</label>
          <input type="text" id="name" name="username" placeholder="Enter your username" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <?php
        if (isset($_SESSION['login_error'])) {
            echo '<p>' . htmlspecialchars($_SESSION['login_error']) . '</p>';
            unset($_SESSION['login_error']);
        }
        ?>

        <button type="submit" class="login-btn">Login</button>

        <div class="login-links">
          <a href="#">Forgot Password?</a>
          <a href="membership.php">Create an Account</a>
        </div>
      </form>
    </div>
  </section>
  </main>
  <!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>