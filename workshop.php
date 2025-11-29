<?php
/*
 * File: workshop.php
 * Description: Public-facing Workshop pages listing upcoming and past workshops.
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
</head>
<body>

<!-- Navbar -->
  <?php include 'navigation.php'; ?>
<!-- End of Navbar -->

<!-- Banner -->
<section class="section-background banner-workshop">
    <div class="banner-text">
      <h1>Workshop</h1>
      <h2>2025</h2>
    </div>
</section>

<!-- Intro -->
<section>
  <div class="activities-intro">
    <h2 class="activities-intro-title">Floral Art Workshop — Let Life Bloom</h2>
    <p class="activities-description">
      Bring floral art into everyday life.<br>
      Let every pair of hands feel the warmth and master the skills of working with flowers.<br>
      Bring romance and gentleness into each day.<br>
      Let everyone tell their own story through flowers.</p>
    <div class="activities-features">
      <div class="feature-item">
        <span class="feature-icon">&#127807;</span>
        <p>1-to-1 orSmall Group<br><span>Learn in a personalized and friendly setting</span></p>
      </div>
      <div class="feature-item">
        <span class="feature-icon">&#10024;</span>
        <p>Beginner-Friendly<br><span>No experience needed, perfect for anyone to start</span></p>
      </div>
      <div class="feature-item">
        <span class="feature-icon">&#128144;</span>
        <p>Take Your Creations Home<br><span>Bring your beautiful floral works with you</span></p>
      </div>
      <div class="feature-item">
        <span class="feature-icon">&#128338;</span>
        <p>Flexible Scheduling<br><span>Choose class times that suit your lifestyle</span></p>
      </div>
      <div class="feature-item">
        <span class="feature-icon">&#127873;</span>
        <p>All Materials Included<br><span>Everything you need is provided for you</span></p>
      </div>
      <div class="feature-item">
        <span class="feature-icon">&#128205;</span>
        <p>Convenient Location<br><span>Easy to reach, located in the heart of Kuching</span></p>
      </div>
    </div>
  </div>
</section>
<main>

<!-- Workshop Content -->
  <section class="workshop-section1">
    <section>
      <div class="workshop-title">
        <h1 id="upcoming-workshops">Upcoming Workshops</h1>
      </div>
      <!-- WORKSHOP 1 -->
      <div class="workshop-content" id="workshop1">
        <h2 class="workshop-content-title">Handtied Bouquet</h2><hr>
        <div class="workshop-details"><p>2 DAYS / 5 CLASS</p></div>
        <div class="workshop-tag">
          <a>Spiral Handtied-Round Layers</a><a>Single Stalk Bouquet</a>
          <a>Single Stalk Bouquet</a><a>Spiral Handtied-Classic Layerst</a>
          <a>Korean Bouquet</a><a>Russian Bouquet</a><a>Mix Flowers Bouquet</a>
        </div>
        <div class="activities-button"><a href="register.php">Book Now</a></div>
      </div>

      <!-- WORKSHOP 2 -->
      <div class="workshop-content" id="workshop2">
        <h2 class="workshop-content-title">Florist To Be 1</h2><hr>
        <div class="workshop-details"><p>4 DAYS / 9 CLASS</p></div>
          <div class="workshop-tag">
          <a>Korean Bouquet</a><a>Spiral Handtied-Round Layers</a>
          <a>Russian Bouquet</a><a>Mix Flowers Bouquet</a>
          <a>Spiral Handtied-Classic Layers</a><a>Single Stalk Bouquet</a>
          <a>Bridal Bouquett</a><a>Boutineer</a>
          <a>Flowers Basket</a><a>Centerpiece</a><a>Flowers Stand</a> 
        </div>
        <div class="activities-button"><a href="register.php">Book Now</a></div>
      </div>

      <!-- WORKSHOP 3 -->
      <div class="workshop-content" id="workshop3">
        <h2 class="workshop-content-title">Florist To Be 2</h2><hr>
        <div class="workshop-details"><p>4 DAYS / 9 CLASS</p></div>
        <div class="workshop-tag">
          <a>Natural Design Bouquet</a><a>Korean Bouquet</a>
          <a>Russian Bouquet</a><a>Mix Flowers Bouquet</a>
          <a>Spiral Handtied-Classic Layers</a><a>Single Stalk Bouquet</a>
          <a>Bridal Bouquett</a><a>Boutineer</a><a>Flowers Basket</a>
          <a>Mirror Flowers Stand</a> <a>Flowers Box</a> 
        </div>
        <div class="activities-button"><a href="register.php">Book Now</a></div>
      </div>

      <!-- Workshop 4 -->
    <div class="workshop-content" id="workshop4">
      <h2 class="workshop-content-title">Hobby Class</h2><hr>
      <div class="workshop-details"><p>30 AUG / 13 SEP / 18 OCT (2025)</p></div>
        <div class="workshop-tag">
          <a>Centerpiece</a><a>Flowers Box</a><a>Flowers Basket</a><a>Mix Flowers Bouquet</a>
        </div>
        <div class="activities-button"><a href="register.php">Book Now</a></div>
    </div>
  </section>

  <aside class="activity-sidebar-container">
      <div class="activity-sidebar">
        <h2>Workshop</h2>
        <ul class="category-list">
          <li><a href="#upcoming-workshops" class="category-item active">Upcoming Workshop</a></li>
          <li><a href="#workshop1" class="category-item">Handtied Bouquet</a></li>
          <li><a href="#workshop2" class="category-item">Florist To Be 1</a></li>
          <li><a href="#workshop3" class="category-item">Florist To Be 2</a></li>
          <li><a href="#workshop4" class="category-item">Hobby Class</a></li>
          <li><a href="#photos" class="category-item active">Photos</a></li>
          <li><a href="#pastworkshop" class="category-item active">Past Workshop</a></li>
        </ul>
      </div>
  </aside>
  </section>

    <section class="workshop-section2">
      <div class="workshop-title">
      <h1 id="photos">Photos</h1>
      </div>
    <div class="workshop-carousel">
      <div class="workshop-group">
        <div class="workshop-card card1"></div>
        <div class="workshop-card card2"></div>
        <div class="workshop-card card3"></div>
        <div class="workshop-card card4"></div>
        <div class="workshop-card card5"></div>
        <div class="workshop-card card6"></div>
      </div>
      <div aria-hidden class="workshop-group">
        <div class="workshop-card card1"></div>
        <div class="workshop-card card2"></div>
        <div class="workshop-card card3"></div>
        <div class="workshop-card card4"></div>
        <div class="workshop-card card5"></div>
        <div class="workshop-card card6"></div>
      </div>
    </div>
    </section>
    
    <section class="workshop-section3">
      <div class="workshop-title">
        <h1 id="pastworkshop">Past Workshops</h1>
      </div>
      <div>
        <figure class="workshop-figure"><img src="Pictures/Workshop/pastworkshop1.png">
          <figcaption>Fig.1 Floral Workshop</figcaption></figure>
        <figure class="workshop-figure"><img src="Pictures/Workshop/pastworkshop2.jpg">
          <figcaption>Fig.2 Christmas Floral Workshop</figcaption></figure>
        <figure class="workshop-figure"><img src="Pictures/Workshop/pastworkshop3.jpg">
          <figcaption>Fig.3 Floral Workshop</figcaption></figure>
        <figure class="workshop-figure"><img src="Pictures/Workshop/pastworkshop4.jpg">
          <figcaption>Fig.4 Bouquet Workshop</figcaption></figure>
        <figure class="workshop-figure"><img src="Pictures/Workshop/pastworkshop5.jpg">
          <figcaption>Fig.5 Bouquet Workshop</figcaption></figure>
        <figure class="workshop-figure"><img src="Pictures/Workshop/pastworkshop6.jpg">
          <figcaption>Fig.6 Floral Workshop</figcaption></figure>
      </div>
    </section>
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>