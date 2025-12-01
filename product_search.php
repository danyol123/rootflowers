<?php
/*
 * File: product_search.php
 * Description: Product Search page for listing product results.
 * Author: Root Flower Team
 * Created: 2025-11-27
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="description" content="Root Flower">
  <meta name="keywords" content="Flowers, Shop, Kuching, Sarawak, Malaysia">
  <meta name="author" content="Rootflower Team">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
  <title>Product Search - Root Flower</title>
  <link rel="stylesheet" href="styles/styles.css">
  <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Lato:300" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

</head>

<body>

  <!-- Navbar -->
  <?php include 'navigation.php'; ?>
  <!-- End Navbar -->

  <main>
    <section class="section-background banner-search">
      <div class="banner-text">
        <h1>Product Search</h1>
        <h2>Find your favorite flowers or services</h2>
      </div>
    </section>

    <section class="search-section">
      <div class="search-container">
        <form method="GET" action="product_search.php">
          <input type="text" name="keyword" placeholder="Search products or services..." required>
          <button type="submit"><i class="fa fa-search"></i> Search</button>
        </form>
      </div>

      <div class="search-results">
        <?php
        if (isset($_GET['keyword'])) {
          $keyword = strtolower(trim($_GET['keyword']));


          $products = [
            // Hand Bouquet (product1.php)
            ['name' => 'Bountiful Roses', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'Dahlia Bouquet', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'Snow White', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'Spring Daydream', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'Blue Fragrance', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'Soap Roses', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'White Prince', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],
            ['name' => 'Perfect Kiss', 'link' => 'product1.php', 'category' => 'Hand Bouquet'],

            // CNY Decorations (product2.php)
            ['name' => 'Blossoms of Fortune', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Prosperity Bloom', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Harmony in Unison', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Red Lantern Blossoms', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Joyful Blessing', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Peach Dreams', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Radiant Luck', 'link' => 'product2.php', 'category' => 'CNY Decorations'],
            ['name' => 'Sprouting Abundance', 'link' => 'product2.php', 'category' => 'CNY Decorations'],

            // Grand Opening (product3.php)
            ['name' => 'Flourished Fortune', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Rising Prosperity', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Success in Full Bloom', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Majestic Beginnings', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Abundant Prospect', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Golden Triumph', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Prosperous Path', 'link' => 'product3.php', 'category' => 'Grand Opening'],
            ['name' => 'Victorious Start', 'link' => 'product3.php', 'category' => 'Grand Opening'],

            // Graduation (product4.php)
            ['name' => 'Strange New Times', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'Wings of Success', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'O Sun, Witness Me', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'Dreams in Front', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'Journey of Triumph', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'The Next Chapter', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'Courage to Soar', 'link' => 'product4.php', 'category' => 'Graduation'],
            ['name' => 'Starting Petals', 'link' => 'product4.php', 'category' => 'Graduation'],
          ];

          $found = false;

          echo '<ul class="product-list">';
          foreach ($products as $product) {
            // Check if keyword matches name OR category
            if (strpos(strtolower($product['name']), $keyword) !== false || strpos(strtolower($product['category']), $keyword) !== false) {
              $found = true;
              echo '<li><a href="' . $product['link'] . '">' . $product['name'] . '</a></li>';
            }
          }
          if (!$found) {
            echo '<li>No products or services found for "<strong>' . htmlspecialchars($keyword) . '</strong>".</li>';
          }
          echo '</ul>';
        }
        ?>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <?php include 'footer.php'; ?>

</body>

</html>