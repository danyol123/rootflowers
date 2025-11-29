<?php
/*
 * File: admin_panel.php
 * Description: Admin dashboard - quick links to admin functions.
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
  <title>Root Flower — Admin Panel</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
<main class="admin-main">
    <!-- Sidebar -->
    <?php include 'admin_sidebar.php'; ?>

    <!-- Right Content -->
    <section class="admin-content">
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
            <a class="rf-btn rf-btn-ghost" href="view_login.php">Logins</a>
            <a class="rf-btn rf-btn-ghost" href="recycle.php">Recycle Bin</a>
            <a class="rf-btn rf-btn-ghost" href="view_promotion.php">Promotion Module</a>
          </div>
        </div>
      </div>
    </section>
</main>
</body>
</html>
