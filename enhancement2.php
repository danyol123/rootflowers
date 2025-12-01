<?php
/*
 * File: enhancement2.php
 * Description: Enhancement showcase (Assignment 2) listing server-side admin features and improvements.
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Root Flower Enhancements - Module 2">
    <meta name="keywords" content="Enhancement, Admin, Modules">
    <meta name="author" content="Root Flower Team">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
    <title>Root Flower</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<!-- Navbar -->
  <?php include 'navigation.php'; ?>
<!-- End of Navbar -->

<main class="enhancement">
  <section class="section">
    <h1>Our Enhancements (Assignment 2)</h1>
    <p>These are the more advanced features and admin workflows we've implemented server-side.</p>
    <hr>
    <a href="#usermanagement" class="contact_us">User Management</a>
    <a href="#viewlogin" class="contact_us">View Login History</a>
    <a href="#recyclebin" class="contact_us">Recycle Bin</a>
    <a href="#promotionmodule" class="contact_us">Promotion Module</a>
    <a href="#membertopup" class="contact_us">Member Topup</a>
    <a href="#productsearch" class="contact_us">Product Search</a>
    <a href="#tablesorting" class="contact_us">Table Sorting</a>
    <a href="#antispam" class="contact_us">Anti-spam (Honeypot)</a>
    <a href="#passwordrules" class="contact_us">Password Strength Rules</a>
    <a href="#caseinsensitivelogin" class="contact_us">Case-Insensitive Login</a>
  </section>

  <!-- User Management Module (Create, Edit, View, Delete) -->
  <section class="section">
    <h2 id="usermanagement">User Management</h2>
    <img src="Pictures/Enhancements2/crud.png" alt="User Management">
    <img src="Pictures/Enhancements2/create.png" alt="User Management - Create">
    <p>
      Admin interface to manage the database: create new entries, update details, view data info, and soft-delete (move to Recycle).
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> The user management UI is fully server-driven with inline modals for create / view / edit, server-side sorting, CSRF tokens on all forms, and soft-delete flags that allow restore operations.</p>
      <p><strong>Implementation steps:</strong> Add admin authentication checks for pages; implement POST-based forms with `csrf` tokens; use prepared statements for database actions; add `deleted`/`deleted_at` columns for soft deletes; implement `?view`/`?edit` GET-driven modals; add server-side validation and escape output via `htmlspecialchars()`.</p>
    </div>
    <div class="button-group">
      <a href="view_register.php" class="contact_us">View Workshop Registrations</a>
      <a href="view_enquiry.php" class="contact_us">View Enquiries</a>
      <a href="view_membership.php" class="contact_us">View Memberships</a>
      <a href="view_login.php" class="contact_us">View Login History</a>
    </div>
  </section>

  <!-- View Login History -->
  <section class="section">
    <h2 id="viewlogin">View Login History</h2>
    <img src="Pictures/Enhancements2/view_login.png" alt="View Login">
    <p>
      Admin page to view recorded successful logins (username, IP, user-agent and time). Server-driven listing with sorting and recycle bin support.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> Persistent login history auditing is added to track security events and user activity. It gives admins audit trails for logins and optionally integrates with Recycle Bin for management.</p>
      <p><strong>Implementation steps:</strong> Create `login_history` table; append successful logins to the table in `login_process.php`; implement `view_login.php` to read, sort and view records; add per-record soft-delete to `recycle.php` and admin forms with CSRF.</p>
    </div>
    <div class="button-group">
      <a href="view_login.php" class="contact_us">View Login History</a>
    </div>
  </section>

  <!-- Recycle Bin -->
  <section class="section">
    <h2 id="recyclebin">Recycle Bin</h2>
    <img src="Pictures/Enhancements2/recycle_bin1.png" alt="Recycle Bin">
    <img src="Pictures/Enhancements2/recycle_bin2.png" alt="Recycle UI">
    <p>
      A server-side Recycle Bin (soft-delete) that centralizes restore and permanent delete operations for records across admin tables (registrations, enquiries, memberships, login history).
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> A central Recycle Bin consolidates soft-delete & restore workflows across multiple tables with a common, secure confirmation UI and makes data recoveries safer than direct deletes.</p>
      <p><strong>Implementation steps:</strong> Provide a whitelist map of tables and primary key names in `recycle.php`; implement actions (`soft_delete`, `restore`, `perma_delete`) with CSRF protection; preview the row to be deleted; for file-backed rows (e.g., promotion images) remove files on `perma_delete` while considering `deleted_at` metadata.</p>
    </div>
    <div class="button-group">
      <a href="recycle.php" class="contact_us">Recycle Bin</a>
    </div>
  </section>

  <!-- Promotion Module -->
  <section class="section">
    <h2 id="promotionmodule">Promotion Module</h2>
    <img src="Pictures/Enhancements2/promotion.png" alt="Promotion Module">
    <p>
      Admin module for uploading promotion images and managing sections. Includes image validation and server-side file handling.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> This module does server-side image validation, MIME checks using `finfo`, renames files to server-generated safe filenames, and stores the path in the DB.</p>
      <p><strong>Implementation steps:</strong> Add `promotion_images` table and per-section grouping; implement `finfo` + `getimagesize()` checks for uploaded files; restrict file sizes and extensions; generate secure filenames (random hex); move files into `Pictures/Promotion/`; store file paths in DB; use POST+CSRF for deletion; optionally soft-delete via `recycle.php`.</p>
    </div>
    <div class="button-group">
      <a href="view_promotion.php" class="contact_us">Promotion Admin</a>
      <a href="promotion.php" class="contact_us">Promotion Listing</a>
    </div>
  </section>

  <!-- Member Topup -->
  <section class="section">
    <h2 id="membertopup">Member Topup</h2>
    <img src="Pictures/Enhancements2/topup.png" alt="Member Topup">
    <p>
      A secure, server-backed topup form to add credits to a member account. The UI uses server-side presets and validates values on the server.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> The top-up flow stores transaction logs (`topup_history`), performs server-side validation and sanitization of amounts and account details, and uses secure transactions to update balance.</p>
      <p><strong>Implementation steps:</strong> Add a `topup_history` table; convert preset client buttons to server-submitted `preset_amount`; validate `amount` server-side; insert transaction and update balance in a single safe transaction; set session feedback message and redirect to `topup_result.php`.</p>
    </div>
    <div class="button-group">
      <a href="top_up.php" class="contact_us">Top Up Wallet</a>
    </div>
  </section>

  <!-- Product Search -->
  <section class="section">
    <h2 id="productsearch">Product Search</h2>
    <img src="Pictures/Enhancements2/product_search.png" alt="Product Search">
    <p>
      Server-side product search that looks up products and matches keywords.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> A server-backed, SQL-safe search implementation that supports partial matches and safely escapes output.</p>
      <p><strong>Implementation steps:</strong> Add DB `products` table (or mapping); implement prepared `LIKE` or full-text `MATCH` query; sanitize `keyword` input; escape and paginate results. Implement a search results page with server-driven links to product pages.</p>
    </div>
    <div class="button-group">
      <a href="product_search.php" class="contact_us">Product Search</a>
      <a href="product1.php" class="contact_us">Example Product</a>
    </div>
  </section>

  <!-- Table Sorting -->
  <section class="section">
    <h2 id="tablesorting">Table Sorting</h2>
    <img src="Pictures/Enhancements2/sorting.png" alt="Table Sorting">
    <p>
      All table headers support server-driven sort parameters using an allow list to avoid SQL injection. See registration/enquiry/membership/login pages for examples.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> This approach enforces safe sorting by using server-maintained whitelist of allowed columns and toggling sort direction.</p>
      <p><strong>Implementation steps:</strong> Implement an allow-list mapping `GET` sort keys to database columns; sanitize `dir` to `asc` or `desc`; build `ORDER BY` clause from validated keys only; render header links with toggled `dir` and direction icons; index columns used in sorts for performance.</p>
    </div>
    <div class="button-group">
      <a href="view_register.php" class="contact_us">View Workshop Registrations</a>
      <a href="view_enquiry.php" class="contact_us">View Enquiries</a>
      <a href="view_membership.php" class="contact_us">View Memberships</a>
      <a href="view_login.php" class="contact_us">View Login History</a>
    </div>
  </section>

  <!-- Anti-spam / Honeypot -->
  <section class="section">
    <h2 id="antispam">Anti-spam Protection (Honeypot)</h2>
    <img src="Pictures/Enhancements2/anti_spam.png" alt="Anti-spam">
    <p>
      A honeypot field is implemented in form submissions. If this hidden field is populated, the submission is silently discarded to prevent spambots.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> Honeypot offers backend spam protection without adding CAPTCHA friction. It is invisible to users and prevents many automated spam bots while keeping UX smooth.</p></p>
      <p><strong>Implementation steps:</strong> Add a field to the form (e.g., `website`); if populated, block or silently discard the submission and optionally log the attempt.</p>
    </div>
    <div class="button-group">
      <a href="register.php" class="contact_us">Workshop Registration</a>
      <a href="enquiry.php" class="contact_us">Enquiry Form</a>
      <a href="membership.php" class="contact_us">Membership Registration</a>
      <a href="login.php" class="contact_us">Login</a>
      <a href="top_up.php" class="contact_us">Top-up</a>
    </div>
  </section>

  <!-- Password complexity: Characters and number -->
  <section class="section">
    <h2 id="passwordrules">Password Complexity (Characters & Number)</h2>
    <img src="Pictures/Enhancements2/password.png" alt="Password Rules placeholder">
    <p>
      Membership passwords require a mixture of characters and numbers upon creation. This check is enforced server-side and presented in the create/edit UI.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> Enforcing rules server-side improves security and prevents weak passwords. The change includes using `password_hash()` and `password_verify()` for secure password storage and verifying complexity on create/update.</p>
      <p><strong>Implementation steps:</strong> Add a regex check server-side to validate length and included character types; show error messages in the create/edit UI; store passwords with `password_hash()` and verify with `password_verify()` during login; consider enforcing stronger rules (symbols, mixed case) if policy requires.</p>
    </div>
    <div class="button-group">
      <a href="membership.php" class="contact_us">Membership Registration</a>
    </div>
  </section>

  <!-- Case-Insensitive Login (Username Matching) -->
  <section class="section">
    <h2 id="caseinsensitivelogin">Case-Insensitive Login (Username)</h2>
    <img src="Pictures/Enhancements/placeholder.gif" alt="Case-Insensitive Login placeholder">
    <p>
      The login system allows users to enter their username in any letter case (e.g. <code>Alice</code>, <code>alice</code>, <code>ALICE</code>) and still authenticate successfully, reducing friction and user error without weakening password security.
    </p>
    <div class="enhancement-details">
      <p><strong>Beyond requirements:</strong> Enhances usability by normalizing username comparison at query-time while preserving the originally cased username in storage and sessions for display consistency. Security is maintained by keeping passwords case-sensitive and hashed using <code>password_hash()</code>.</p>
      <p><strong>Implementation steps:</strong> Use prepared statements with <code>LOWER(username) = LOWER(?)</code> in queries (see <code>login_process.php</code> for admin and membership checks); keep the stored username unchanged; apply <code>password_verify()</code> directly to the submitted password (no lowercasing) to retain full entropy; escape any echoed username with <code>htmlspecialchars()</code>; ensure updates to usernames in admin tools continue to use consistent case-insensitive matching logic; optionally add an index on <code>LOWER(username)</code> (or a generated column) for scalability.</p>
    </div>
    <div class="button-group">
      <a href="login.php" class="contact_us">Login Form</a>
      <a href="login_process.php" class="contact_us">Login Logic</a>
      <a href="admin_panel.php" class="contact_us">Admin Settings</a>
    </div>
  </section>
  
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>
