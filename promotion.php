<?php
// DB Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function to get images for a section
function getImages($conn, $section) {
    $stmt = $conn->prepare("SELECT image_path FROM promotion_images WHERE section = ?");
    $stmt->bind_param("s", $section);
    $stmt->execute();
    $result = $stmt->get_result();
    $images = [];
    while ($row = $result->fetch_assoc()) {
        $images[] = $row['image_path'];
    }
    $stmt->close();
    return $images;
}

$special_discount_images = getImages($conn, 'Special Discount');
$early_bird_images = getImages($conn, 'Early Bird');
$give_away_images = getImages($conn, 'Give Away');

$conn->close();
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

<!-- Banner -->
<section class="section-background banner-promotion">
  <div class="banner-text" id="banner">
    <h1>Promotion</h1>
    <h2>2025</h2>
  </div> 
</section>
<!-- Intro -->
<section>
  <div class="activities-intro">
    <h2 class="activities-intro-title">Join Our Membership!</h2>
    <div class="activities-features activities-features-promotion">
      <div class="feature-item">
        <span class="feature-icon">&#127807;</span>
        <p>Exclusive Member Discounts<br>
        <span>Enjoy 5% off all orders and 10% off workshop registration for group sign-ups (5+ participants).</span></p>
      </div>

      <div class="feature-item">
        <span class="feature-icon">&#128179;</span>
        <p>Easy Account Top-Up<br>
        <span>Conveniently add funds to your member account for hassle-free payments.</span></p>
      </div>

      <div class="feature-item">
        <span class="feature-icon">&#128101;</span>
        <p>Group Perks<br>
        <span>Save more and have fun learning together when you register as a group.</span></p>
      </div>

    </div>

    <div class="activities-button">
        <a href="membership.php">Join Us Now</a>
    </div>
  </div>
</section>
<!-- Promotion Section -->
<main id="promotion-main">
  <section style="flex: 1; padding-left: 10%;">
    <div class="promotion-title">
      <h1 id="special-discount">
        Special Discount
      </h1>
      <div class="discount">
        <?php foreach ($special_discount_images as $img): ?>
            <figure><img src="<?php echo $img; ?>" alt="Special Discount"></figure>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="promotion-title">
      <h1 id="early-bird">
        Early Bird
      </h1>
      <div class="earlybird">
        <?php foreach ($early_bird_images as $img): ?>
            <figure><img src="<?php echo $img; ?>" alt="Early Bird"></figure>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="promotion-title">
      <h1 id="giveaway">
      Give Away
      </h1>
      <div class="giveaway">
        <?php foreach ($give_away_images as $img): ?>
            <figure><img src="<?php echo $img; ?>" alt="Give Away"></figure>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <aside class="activity-sidebar-container" id="promotion-sidebar">
      <div class="activity-sidebar">
      <h2>Promotion</h2>
      <ul class="category-list">
        <li><a href="#special-discount" class="category-item">Special Discount</a></li>
        <li><a href="#early-bird" class="category-item">Early Bird</a></li>
        <li><a href="#giveaway" class="category-item">Give Away</a></li>
      </ul>
    </div>
  </aside>
</main>
<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>