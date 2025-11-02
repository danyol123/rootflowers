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
      <form action="register_process.php" method="get" autocomplete="on">
        <!-- Name Inputs -->
        <label for="firstName">First Name:</label>
        <input type="text" id="firstName" name="firstName" maxlength="25" pattern="[A-Za-z\s]+" required title="Alphabetical characters only">

        <label for="lastName">Last Name:</label>
        <input type="text" id="lastName" name="lastName" maxlength="25" pattern="[A-Za-z\s]+" required title="Alphabetical characters only">

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required>

        <!-- Address Fieldset -->
        <fieldset>
          <legend>Address</legend>
          <label for="street">Street Address:</label>
          <input type="text" id="street" name="street" maxlength="40" required>

          <label for="city">City/Town:</label>
          <input type="text" id="city" name="city" maxlength="20" required>

          <label for="state">State:</label>
          <select id="state" name="state" required>
            <option value="">-- Select State --</option>
            <option value="Johor">Johor</option>
            <option value="Kedah">Kedah</option>
            <option value="Kelantan">Kelantan</option>
            <option value="Melaka">Melaka</option>
            <option value="Negeri Sembilan">Negeri Sembilan</option>
            <option value="Pahang">Pahang</option>
            <option value="Penang">Penang</option>
            <option value="Perak">Perak</option>
            <option value="Perlis">Perlis</option>
            <option value="Sabah">Sabah</option>
            <option value="Sarawak">Sarawak</option>
            <option value="Selangor">Selangor</option>
            <option value="Terengganu">Terengganu</option>
            <option value="Kuala Lumpur">Kuala Lumpur</option>
            <option value="Labuan">Labuan</option>
            <option value="Putrajaya">Putrajaya</option>
          </select>

          <label for="postcode">Postcode:</label>
          <input type="text" id="postcode" name="postcode" pattern="\d{5}" maxlength="5" required title="Exactly 5 digits">
        </fieldset>

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" maxlength="10" pattern="\d{10}" placeholder="0123456789" required title="10 digits">

        <label for="workshopDate">Workshop Date:</label>
        <input type="date" id="workshopDate" name="workshopDate" required>

        <label for="participants">Number of Participants:</label>
        <input type="text" id="participants" name="participants" maxlength="2" pattern="[A-Za-z0-9]{1,2}" required title="Alphanumeric, max 2 characters">

        <!-- Radio Workshop Type -->
        <label>Workshop Type:</label>
        <div class="input-group">
          <div>
          <input type="radio" id="basic" name="workshopType" value="Basic" required>
          <label for="basic">Basic</label>
          </div>
          <div>
          <input type="radio" id="advanced" name="workshopType" value="Advanced">
          <label for="advanced">Advanced</label>
          </div>
        </div>

        <!-- Checkbox Add-ons -->
        <label>Add-ons:</label>
        <div class="input-group">
          <div>
          <input type="checkbox" id="materials" name="addons[]" value="Materials">
          <label for="materials">Include Materials</label>
          </div>
          <div>
          <input type="checkbox" id="certificate" name="addons[]" value="Certificate">
          <label for="certificate">Certificate</label>
          </div>
        </div>

        <!-- Comments -->
        <label for="comments">Comments:</label>
        <textarea id="comments" name="comments" rows="4" cols="40" placeholder="Any special requests?"></textarea>

        <br>
        <div class="submit-buttons">
          <input type="submit" value="Register">
          <input type="reset" value="Reset">
        </div>
      </form>
    </div>
  </section>
</main>

<!-- Footer -->
  <?php include 'footer.php'; ?>

</body>
</html>