<?php
/*
 * File: enquiry.php
 * Description: Public-facing enquiry form (honeypot implemented) for customers to send messages.
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="Root Flowersss">
  <meta name="keywords" content="Flowers">
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

  <!-- ====== ENQUIRY PAGE CONTENT ====== -->
  <main>
    <section class="enquiry-section">
      <div class="enquiry-container">
        <h1 class="enquiry-title">Enquiry Form</h1>
        
        <?php
        // Display success message if set
        if (isset($_SESSION['success_message'])) {
            echo '<div class="success-message">' . htmlspecialchars($_SESSION['success_message']) . '</div>';
            unset($_SESSION['success_message']);
        }
        
        // Display error message if set
        if (isset($_SESSION['error_message'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
            unset($_SESSION['error_message']);
        }
        ?>

        <?php
        if (!isset($_SESSION['csrf_token'])) {
          $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
        }
        $csrf = $_SESSION['csrf_token'];
        ?>
        <form action="enquiry_process.php" method="post" class="enquiry-form">
          <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
          <!-- First Name -->
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" maxlength="25" pattern="[A-Za-z]+" required>

          <!-- Last Name -->
          
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" maxlength="25" pattern="[A-Za-z]+" required>

          <!-- Email -->
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" required>

          <!-- Phone -->
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" maxlength="10" pattern="[0-9]{10}" placeholder="0123456789" required>

          <!-- Enquiry Type -->
            <label for="enquiry-type">Type of Enquiry</label>
            <select id="enquiry-type" name="enquiry-type" required>
              <option value="">-- Please select an option --</option>
              <option value="products">Products</option>
              <option value="membership">Membership</option>
              <option value="workshop">Workshop</option>
            </select>

          <!-- Comments -->
            <label for="comments">Comments</label>
            <textarea id="comments" name="comments" rows="5" cols="30" placeholder="Write your enquiry here..." required></textarea>

          <!-- Honeypot field (hidden from users, bots will fill it) -->
            <label for="pot">Don't fill in this field</label>
            <input type="text" name="website" id="pot" tabindex="-1" autocomplete="off">

          <!-- Buttons -->
          <div class="submit-buttons">
            <input type="submit" value="Submit Enquiry">
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