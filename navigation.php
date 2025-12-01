<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$top_up_link = 'top_up.php';

$login_url = 'login.php';
$login_label = 'Login';
if (!empty($_SESSION['is_logged_in'])) {
  $login_url = 'logout.php';
  $login_label = 'Logout';
}

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
            <li><a href="product_search.php">Product Search</a></li>
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
            <li><a href="' . $top_up_link . '" class="topup-btn">Top Up Wallet</a></li>
          </ul>
        </li>
        <li class="dropdown"><a href="about_us.php">About Us</a></li>
        <li class="dropdown"><a href="' . $login_url . '" class="login-navbar">' . $login_label . '</a></li>
      </ul>
    </div>
  </nav>
</header>';
?>