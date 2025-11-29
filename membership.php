<?php
/*
 * File: membership.php
 * Description: Public membership registration page (with client-side hints and CSRF token).
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
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
    <title>Root Flower</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>

<!-- Navbar -->
  <?php include 'navigation.php'; ?>

<!-- Main Content -->
<main>
  <section class="form-section">
    <div class="form-container">
        <h1>Join Rootflower</h1>
        <?php
        if (!isset($_SESSION)) session_start();
        if (!isset($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        $csrf = $_SESSION['csrf_token'];
        ?>
        <form action="membership_process.php" method="post">
          <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
            <!-- First Name -->
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" maxlength="25" pattern="[A-Za-z]+" required>

            <!-- Last Name -->
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" maxlength="25" pattern="[A-Za-z]+" required>

            <!-- Username-->
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" maxlength="25" pattern="[A-Za-z]+" required>

            <!-- Email -->
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

            <!-- Password -->
            <label for="password">Password:</label>
            <p>(Password must be at least 8 characters and include at least 1 letter and 1 number.)</p>
            <input type="password" id="password" name="password" pattern="^(?=.*[A-Za-z])(?=.*[0-9]).{8,}$" required>


            <!-- Submit -->
            <div class="submit-buttons">
              <input type="submit" value="Register">
              <input type="reset" value="Reset">
            </div>
        </form>
    </div>
  </section>
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>