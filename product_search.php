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
    <style>
        .search-section {
            padding: 3rem 1rem;
            background-color: #f9f5f0; 
            font-family: 'Lato', sans-serif;
        }

        .search-container {
            max-width: 600px;
            margin: 0 auto 2rem auto;
            display: flex;
            justify-content: center;
        }

        .search-container form {
            width: 100%;
            display: flex;
            gap: 0.5rem;
        }

        .search-container input[type="text"] {
            flex: 1;
            padding: 0.8rem 1rem;
            border: 1px solid #C4B6A4; 
            border-radius: 8px;
            font-size: 1rem;
        }

        .search-container button {
            padding: 0.8rem 1.2rem;
            background-color: #C4B6A4; 
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-container button:hover {
            background-color: #b59e87;
        }

        .search-results {
            max-width: 800px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product-list li {
            list-style: none;
            background-color: #fff;
            padding: 1rem 1.2rem;
            border: 1px solid #C4B6A4;
            border-radius: 12px;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-list li:hover {
            transform: translateY(-4px);
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }

        .product-list li a {
            text-decoration: none;
            color: #3b3b3b;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .banner-search {
            background-color: #e4d8c7; 
            text-align: center;
            padding: 3rem 1rem;
            margin-bottom: 2rem;
        }

        .banner-search h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .banner-search h2 {
            font-family: 'Lato', sans-serif;
            font-weight: 300;
            font-size: 1.2rem;
            color: #555;
        }
    </style>
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
              ['name' => 'Bountiful Roses', 'link' => 'product1.php'],
              ['name' => 'Dahlia Bouquet', 'link' => 'product1.php'],
              ['name' => 'Snow White', 'link' => 'product1.php'],
              ['name' => 'Spring Daydream', 'link' => 'product1.php'],
              ['name' => 'Blue Fragrance', 'link' => 'product1.php'],
              ['name' => 'Soap Roses', 'link' => 'product1.php'],
              ['name' => 'White Prince', 'link' => 'product1.php'],
              ['name' => 'Perfect Kiss', 'link' => 'product1.php'],
              ['name' => 'CNY Lantern Decoration', 'link' => 'product2.php'],
              ['name' => 'Grand Opening Arch', 'link' => 'product3.php'],
              ['name' => 'Graduation Bouquet', 'link' => 'product4.php'],
          ];

          $found = false;

          echo '<ul class="product-list">';
          foreach ($products as $product) {
              if (strpos(strtolower($product['name']), $keyword) !== false) {
                  $found = true;
                  echo '<li><a href="'.$product['link'].'">'.$product['name'].'</a></li>';
              }
          }
          if (!$found) {
              echo '<li>No products or services found for "<strong>'.htmlspecialchars($keyword).'</strong>".</li>';
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
