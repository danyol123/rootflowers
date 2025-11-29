<?php
/*
 * File: navigation.php
 * Description: Top navigation bar shared across pages
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
echo
'
<header>
  <nav class="navbar">
    <a href="index.php"><img src="Pictures/Index/logo.png" class="logo" alt="Logo"></a>
    <div class="nav-links-container">
      <ul class="nav-links">
        <li class="dropdown"><a href="index.php">Home</a></li>
        <li class="dropdown">
          <a href="#">Products</a>
          <ul class="dropdown-content">
            <li><a href="product1.php">Hand bouquet</a></li>
            <li><a href="product2.php">CNY decoration</a></li>
            <li><a href="product3.php">Grand Opening</a></li>
            <li><a href="product4.php">Graduation</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#">Activities</a>
          <ul class="dropdown-content">
            <li><a href="workshop.php">Workshop</a></li>
            <li><a href="promotion.php">Promotion</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#">Forms</a>
          <ul class="dropdown-content">
            <li><a href="register.php">Workshop Registration</a></li>
            <li><a href="enquiry.php">Enquiry Form</a></li>
            <li><a href="membership.php">Membership Registration</a></li>
            <li><a href="login.php">Login</a></li>
          </ul>
        </li>
        <li class="dropdown"><a href="top_up.php" class="topup-btn">Top Up Wallet</a></li>
        <li class="dropdown"><a href="product_search.php">Product Search</a></li>
        <li class="dropdown"><a href="about_us.php">About Us</a></li>
      </ul>
    </div>
  </nav>
</header>';
?>
