<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Root Flower">
    <meta name="keywords" content="Flowers, Shop, Kuching, Sarawak, Malaysia">
    <meta name="author" content="Daniel, Josiah, Alvin, Kheldy">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
    <title>Top Up - Rootflower</title>
    <link rel="stylesheet" href="styles/styles.css">
    
</head>
<body>

<?php include 'navigation.php'; ?>



<main>
  <section class="form-section">
    <div class="form-container">
        <h1>Top Up Wallet</h1>

        <!-- Display Current Balance -->
        <?php
        if(!isset($current_balance)) {
            $current_balance = 0.00; // default value if not set
        }
        ?>
        <h3>Your Current Balance: RM <?php echo number_format($current_balance, 2); ?></h3>

        <form action="topup_process.php" method="post">

            <!-- Enter Amount -->
            <label for="amount">Top Up Amount (RM):</label>
            <input type="number" id="amount" name="amount" min="5" max="500" required>

          <!-- Preset Amount Buttons -->
<div class="preset-buttons">
    <!-- Server-driven preset buttons: submit the form with an amount value -->
    <button type="submit" class="preset-btn" name="amount" value="50">RM50</button>
    <button type="submit" class="preset-btn" name="amount" value="100">RM100</button>
    <button type="submit" class="preset-btn" name="amount" value="150">RM150</button>
    <button type="submit" class="preset-btn" name="amount" value="200">RM200</button>
</div>


            <!-- Payment Method Selection -->
            <label for="payment_method">Select Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="">-- Choose Payment Method --</option>
                <option value="card">Debit / Credit Card</option>
                <option value="online_banking">Online Banking / FPX</option>
                <option value="tng">Touch 'N Go eWallet</option>
                <option value="grabpay">GrabPay</option>
                <option value="shopeepay">ShopeePay</option>
                <option value="cash">Cash at Store</option>
            </select>

            <!-- Bank Selection (For FPX / Online Banking) -->
            <label for="bank">Select Bank (Malaysia):</label>
            <select id="bank" name="bank">
                <option value="">-- Select Bank --</option>
                <option value="maybank">Maybank</option>
                <option value="cimb">CIMB Bank</option>
                <option value="public">Public Bank</option>
                <option value="rhb">RHB Bank</option>
                <option value="hlb">Hong Leong Bank</option>
                <option value="ambank">AmBank</option>
            </select>

            <!-- Account Number -->
            <label for="account_number">Your Bank / E-Wallet Account Number:</label>
            <input type="text" id="account_number" name="account_number" maxlength="20" required>

            <!-- Submit -->
            <div class="submit-buttons">
              <input type="submit" value="Confirm Top Up">
              <input type="reset" value="Reset">
            </div>
        </form>
    </div>
  </section>



</main>

<!-- No JavaScript — preset options submit the selected amount server-side. -->

<?php include 'footer.php'; ?>

</body>
</html>
