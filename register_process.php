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
  <section class="form-section">
    <div class="form-container">
        <h1>Workshop Registration</h1>
        <?php
        echo "<p>Thank you for registering, <strong>" . htmlspecialchars($_GET["firstName"]) . " " . htmlspecialchars($_GET["lastName"]) . "!</strong></p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($_GET["email"]) . "</p>";
        echo "<p><strong>Address:</strong> " . htmlspecialchars($_GET["street"]) . ", " . htmlspecialchars($_GET["city"]) . ", " . htmlspecialchars($_GET["state"]) . ", " . htmlspecialchars($_GET["postcode"]) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($_GET["phone"]) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($_GET["workshopDate"]) . "</p>";
        echo "<p><strong>Number of Participants:</strong> " . htmlspecialchars($_GET["participants"]) . "</p>";
        echo "<p><strong>Workshop Type:</strong> " . htmlspecialchars($_GET["workshopType"]) . "</p>";
        echo "<p><strong>Add-ons:</strong> </p>";
        if (!empty($_GET["addons"])) {
            echo "<ul>";
            foreach ($_GET["addons"] as $addon) {
                echo "<li>" . htmlspecialchars($addon) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No add-ons selected.</p>";
        }
        echo "<p><strong>Comments:</strong> " . nl2br(htmlspecialchars($_GET["comments"])) . "</p>";

        echo "<p>Your registration details have been received successfully.</p>";
        echo "<p><a href='register.php'>Return to the Entry Page</a></p>"
        ?>
    </div>
  </section>
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>