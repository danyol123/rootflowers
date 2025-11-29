<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Root Flower Enhancements - Module 2">
    <meta name="keywords" content="Enhancement, Admin, Modules">
    <meta name="author" content="Root Flower Team">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
    <title>Root Flower — Enhancements (Module 2)</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<!-- Navbar -->
  <?php include 'navigation.php'; ?>
<!-- End of Navbar -->

<main class="enhancement">
  <section class="section">
    <h1>New Enhancements (Module 2)</h1>
    <p>These are the more advanced features and admin workflows we've implemented server-side.</p>
    <hr>
    <a href="#usermanagement" class="contact_us">User Management</a>
    <a href="#promotionmodule" class="contact_us">Promotion Module</a>
    <a href="#membertopup" class="contact_us">Member Topup</a>
    <a href="#productsearch" class="contact_us">Product Search</a>
    <a href="#antispam" class="contact_us">Anti-spam</a>
    <a href="#tablesorting" class="contact_us">Table Sorting (Server-side)</a>
    <a href="#recyclebin" class="contact_us">Recycle Bin</a>
    <a href="#viewlogin" class="contact_us">View Login History</a>
    <a href="#passwordrules" class="contact_us">Password Strength Rules</a>
  </section>

  <!-- User Management Module (Create, Edit, View, Delete) -->
  <section class="section">
    <h2 id="usermanagement">User Management</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="User Management placeholder">
    <p>
      Admin interface to manage the database: create new entries, update details, view data info, and soft-delete (move to Recycle).
    </p>
    <div class="button-group">
      <a href="view_register.php" class="contact_us">View Workshop Registrations</a>
      <a href="view_enquiry.php" class="contact_us">View Enquiries</a>
      <a href="view_membership.php" class="contact_us">View Memberships</a>
      <a href="view_login.php" class="contact_us">View Login History</a>
    </div>
  </section>

  <!-- Promotion Module -->
  <section class="section">
    <h2 id="promotionmodule">Promotion Module</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Promotion Module placeholder">
    <p>
      Admin module for uploading promotion images and managing sections. Includes image validation and server-side file handling.
    </p>
    <div class="button-group">
      <a href="view_promotion.php" class="contact_us">Promotion Admin</a>
      <a href="promotion.php" class="contact_us">Promotion Listing</a>
    </div>
  </section>

  <!-- Member Topup -->
  <section class="section">
    <h2 id="membertopup">Member Topup</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Member Topup placeholder">
    <p>
      A secure, server-backed topup form to add credits to a member account. The UI uses server-side presets and validates values on the server.
    </p>
    <div class="button-group">
      <a href="top_up.php" class="contact_us">Top Up Wallet</a>
    </div>
  </section>

  <!-- Product Search -->
  <section class="section">
    <h2 id="productsearch">Product Search</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Product Search placeholder">
    <p>
      Server-side product search that looks up products and matches keywords. Results are displayed with safe escaping to avoid XSS.
    </p>
    <div class="button-group">
      <a href="product_search.php" class="contact_us">Product Search</a>
      <a href="product1.php" class="contact_us">Example Product</a>
    </div>
  </section>

  <!-- Anti-spam / Honeypot -->
  <section class="section">
    <h2 id="antispam">Anti-spam Protection (Honeypot)</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Anti-spam placeholder">
    <p>
      A honeypot field is implemented in form submissions (for example the Enquiry form). If this hidden field is populated, the submission is silently discarded to prevent spambots.
    </p>
    <div class="button-group">
      <a href="enquiry.php" class="contact_us">Enquiry Form</a>
    </div>
  </section>

  <!-- Server-side Table Sorting -->
  <section class="section">
    <h2 id="tablesorting">Server-side Table Sorting</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Table Sorting placeholder">
    <p>
      All table headers support server-driven sort parameters using an allow list to avoid SQL injection. See registration/enquiry/membership/login pages for examples.
    </p>
    <div class="button-group">
      <a href="view_register.php" class="contact_us">Registrations</a>
      <a href="view_enquiry.php" class="contact_us">Enquiries</a>
      <a href="view_membership.php" class="contact_us">Memberships</a>
      <a href="view_login.php" class="contact_us">Logins</a>
    </div>
  </section>

  <!-- View Login History -->
  <section class="section">
    <h2 id="viewlogin">View Login History</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="View Login placeholder">
    <p>
      Admin page to view recorded successful logins (username, IP, user-agent and time). Server-driven listing with sorting and recycle bin support.
    </p>
    <div class="button-group">
      <a href="view_login.php" class="contact_us">View Login History</a>
    </div>
  </section>

  <!-- Password complexity: Characters and number -->
  <section class="section">
    <h2 id="passwordrules">Password Complexity (Characters & Number)</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Password Rules placeholder">
    <p>
      Membership passwords require a mixture of characters and numbers upon creation. This check is enforced server-side and presented in the create/edit UI.
    </p>
    <div class="button-group">
      <a href="membership.php" class="contact_us">Membership Registration</a>
    </div>
  </section>
  <!-- Recycle Bin -->
  <section class="section">
    <h2 id="recyclebin">Recycle Bin</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Recycle Bin placeholder">
    <p>
      A server-side Recycle Bin (soft-delete) that centralizes restore and permanent delete operations for records across admin tables (registrations, enquiries, memberships, login history).
    </p>
    <div class="button-group">
      <a href="recycle.php" class="contact_us">Open Recycle Bin</a>
    </div>
  </section>
  
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>
