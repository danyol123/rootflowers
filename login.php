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
  <?php include 'navigation.php'; ?>
  <!-- End of Navbar -->

  <!-- ====== LOGIN PAGE CONTENT ====== -->
  <main>
  <section class="login-section">
    <div class="login-container">
      <h2 class="login-title">Welcome Back</h2>
      <p class="login-subtitle">Please log in to continue</p>
      <form class="login-form">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Enter your password" required>
        </div>

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