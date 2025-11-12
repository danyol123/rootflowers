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
<!-- End of Navbar -->

<!-- Main Content -->
<main>
  <section class="form-section">
    <div class="form-container">
        <h1>Join Rootflower</h1>
        <form action="membership_process.php" method="post">
            <!-- First Name -->
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" maxlength="25" pattern="[A-Za-z]+" required>

            <!-- Last Name -->
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" maxlength="25" pattern="[A-Za-z]+" required>

            <!-- Email -->
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

            <!-- Password -->
            <label for="password">Password:</label>
            <input type="password" id="password" name="password"
                   maxlength="25"
                   pattern="^[A-Za-z0-9@#$%^&+=!]{6,25}$"
                   title="Password must be 6–25 characters long and can include letters, numbers, and @#$%^&+=!"
                   required>

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
