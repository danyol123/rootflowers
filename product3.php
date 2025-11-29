<?php
/*
 * File: product3.php
 * Description: Grand Opening product page.
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
    <title>Root Flower</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
  <?php include 'navigation.php'; ?>
<!-- End of Navbar -->

<!-- Main content -->
  <main> 
  <section class="section-background banner-grand-opening">
    <div class="banner-text">
      <h1>Grand Opening</h1>
      <h2>Products</h2>
    </div>
  </section>

<!-- Product Gallery -->
<div class="page">
  <section class="section_product gallery_grand_opening">
    <div class="gallery">

      <div class="container G1">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Flourished Fortune</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM386</p>
            <p class="new">RM366</p>
          </div>

          <input type="checkbox" id="cartToggle1" class="hidden-checkbox">

          <label for="cartToggle1" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Flourished Fortune" added to your cart</p>
            <div class="actions">
              <label for="cartToggle1" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>

      <div class="container G2">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Rising Prosperity</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM384</p>
            <p class="new">RM364</p>
          </div>

          <input type="checkbox" id="cartToggle2" class="hidden-checkbox">

          <label for="cartToggle2" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Rising Prosperity" added to your cart</p>
            <div class="actions">
              <label for="cartToggle2" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>


      <div class="container G3">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Success in Full Bloom</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM262</p>
            <p class="new">RM248</p>
          </div>

          <input type="checkbox" id="cartToggle3" class="hidden-checkbox">

          <label for="cartToggle3" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Success in Full Bloom" added to your cart</p>
            <div class="actions">
              <label for="cartToggle3" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>


      <div class="container G4">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Majestic Beginnings</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM331</p>
            <p class="new">RM314</p>
          </div>

          <input type="checkbox" id="cartToggle4" class="hidden-checkbox">

          <label for="cartToggle4" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Majestic Beginnings" added to your cart</p>
            <div class="actions">
              <label for="cartToggle4" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>


      <div class="container G5">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Abundant Prospect</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM383</p>
            <p class="new">RM363</p>
          </div>

          <input type="checkbox" id="cartToggle5" class="hidden-checkbox">

          <label for="cartToggle5" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Abundant Prospect" added to your cart</p>
            <div class="actions">
              <label for="cartToggle5" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>


      <div class="container G6">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Golden Triumph</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM350</p>
            <p class="new">RM332</p>
          </div>

          <input type="checkbox" id="cartToggle6" class="hidden-checkbox">

          <label for="cartToggle6" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Golden Triumph" added to your cart</p>
            <div class="actions">
              <label for="cartToggle6" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>


      <div class="container G7">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Prosperous Path</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM345</p>
            <p class="new">RM327</p>
          </div>

          <input type="checkbox" id="cartToggle7" class="hidden-checkbox">

          <label for="cartToggle7" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Prosperous Path" added to your cart</p>
            <div class="actions">
              <label for="cartToggle7" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>


      <div class="container G8">
        <div class="overlay">
          <div class="items"></div>
          <div class="items head">
            <p>Victorious Start</p>
            <hr>
          </div>
          <div class="items price">
            <p class="old">RM223</p>
            <p class="new">RM211</p>
          </div>

          <input type="checkbox" id="cartToggle8" class="hidden-checkbox">

          <label for="cartToggle8" class="items cart add-btn">
            <i class="fa fa-shopping-cart"></i>
            <span>ADD TO CART</span>
          </label>

          <div class="items confirmation">
            <p>"Victorious Start" added to your cart</p>
            <div class="actions">
              <label for="cartToggle8" class="btn no">Undo</label>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Sticky Sidebar -->
 <aside class="sidebar">
      <h2>Products</h2>
      <ul class="category-list">
        <li><a href="#" class="category-item active">All Products</a></li>
        <li><a href="product1.php" class="category-item">Hand Bouquet</a></li>
        <li><a href="product2.php" class="category-item">CNY Decorations</a></li>
        <li><a href="product3.php" class="category-item">Grand Opening</a></li>
        <li><a href="product4.php" class="category-item">Graduation</a></li>
      </ul>

      <table class="sidebar-banner" role="presentation">
        <tr>
          <td class="sb-heading">
            <h3>Special Offer!</h3>
          </td>
        </tr>
        <tr>
          <td class="sb-text">
            <p>Members get 5% off.</p>
          </td>
        </tr>
        <tr>
          <td class="sb-action">
            <a href="membership.php" class="btn">Register Now</a>
          </td>
        </tr>
      </table>
  </aside>
</div>
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>