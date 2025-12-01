<?php
/*
 * File: josiah.php
 * Description: Team profile page for Josiah Chew Shao Jie.
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

<!-- Profile -->
<main class="profile-main">
  <figure class="profile-pic" id="josiah-profile-pic">
      <img src="Pictures/Profile/josiah.JPG" alt="Josiah Chew Shao Jie">
  </figure>

  <section>
      <h1 id="josiah-title">Joisah Chew Shao Jie</h1>
      <h2>105803916</h2>
      <h2>Bachelor of Computer Science</h2>
  </section>

  <div class="josiah">
      <table>
          <tr>
              <th>Demographic Information</th>
              <td>Male, Malaysian nationality, Chinese ethnicity, born in 2006</td>
          </tr>
          <tr>
              <th>Hometown</th>
              <td>My hometown is Gua Musang, which is a town that located in the Southern Part of Kelantan, 
                Malaysia. Gua Musang is surrounded by limestone hills, making it a peaceful and naturally beautiful place. 
                There do have some delicious local food I really miss a lot such as Nasi Kerabu and Nasi Dagang</td>
          </tr>
          <tr>
              <th>Achievement</th>
              <td>One of my achievements is I was recognized with the High-Performing Student 
                Award in 2024 during my high school graduation ceremony. It was a meaningful 
                recognition of the effort and passion I put into my school life as I'm president of 
                astronomical society that times. Those duties could be challenging and exhausting, but 
                the experience was truly unforgettable and taught me valuable lessons in leadership, 
                communication, and teamwork.</td>
          </tr>
          <tr>
              <th>Favourite</th>
              <td>I love astronomy and stargazing. I enjoy lying on the carpet in a place without light pollution, 
                surrounded by countless stars. It’s truly amazing — it makes me realize how small we are compared to 
                this vast universe. Sometimes, I also like hiking and my favourite fruits is coconut!</td>
          </tr>
      </table>
      <a href="mailto:105803916@students.swinburne.edu.my">Contact Me!</a>
    </div>
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>