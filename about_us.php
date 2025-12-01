<?php
/*
 * File: about_us.php
 * Description: About Us page with team details and member profile links.
 * Author: Root Flower Team
 * Created: 2025-10-22
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
  <!-- End of Navbar -->

  <main>
    <!-- Flip -->
    <section class="aboutus_section">
      <h1>About Us</h1>
      <div class="aboutus_cardBox">
        <div class="aboutus_card">
          <div class="front">
            <img src="Pictures/Profile/daniel.png" alt="Daniel">
          </div>
          <div class="back">
            <h3>Daniel Williem</h3>
            <p>105803411</p>
            <a href="daniel.php">About Me!</a>
          </div>
        </div>
      </div>

      <div class="aboutus_cardBox">
        <div class="aboutus_card">
          <div class="front">
            <img src="Pictures/Profile/josiah.JPG" alt="Josiah">
          </div>
          <div class="back">
            <h3>Josiah Chew</h3>
            <p>105803916</p>
            <a href="josiah.php">About Me!</a>
          </div>
        </div>
      </div>

      <div class="aboutus_cardBox">
        <div class="aboutus_card">
          <div class="front">
            <img src="Pictures/Profile/Alvin.jpg" alt="Alvin">
          </div>
          <div class="back">
            <h3>Alvin Tiong</h3>
            <p>104395124</p>
            <a href="alvin.php">About Me!</a>
          </div>
        </div>
      </div>

      <div class="aboutus_cardBox">
        <div class="aboutus_card">
          <div class="front">
            <img src="Pictures/Profile/Kheldy.jpeg" alt="Kheldy">
          </div>
          <div class="back">
            <h3>Kheldy Nathanael</h3>
            <p>105802845</p>
            <a href="kheldy.php">About Me!</a>
          </div>
        </div>
      </div>
    </section>
  </main>
<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>