<?php
/*
 * File: top_up.php
 * Description: Public top-up page to allow members to add credit to wallet.
 * Author: Root Flower Team
 * Created: 2025-11-27
 */
session_start();

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    $_SESSION['login_message'] = 'Please login first to access Top Up Wallet.';
    header('Location: login.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "DB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch current balance
$member_id = $_SESSION['member_id'];
$current_balance = 0.00;

$stmt = $conn->prepare("SELECT balance FROM memberships WHERE member_id = ?");
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $current_balance = $row['balance'];
}
$stmt->close();
$conn->close();

// Handle form state preservation
$amount = '';
$payment_method = '';
$bank = '';
$account_number = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['preset_amount'])) {
        $amount = htmlspecialchars($_POST['preset_amount']);
    } elseif (isset($_POST['amount'])) {
        $amount = htmlspecialchars($_POST['amount']);
    }

    if (isset($_POST['payment_method']))
        $payment_method = htmlspecialchars($_POST['payment_method']);
    if (isset($_POST['bank']))
        $bank = htmlspecialchars($_POST['bank']);
    if (isset($_POST['account_number']))
        $account_number = htmlspecialchars($_POST['account_number']);
}
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
                <h3>Your Current Balance: RM <?php echo number_format($current_balance, 2); ?></h3>

                <?php
                // CSRF token generation for top up
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
                }
                $csrf = $_SESSION['csrf_token'];
                ?>
                <form action="topup_process.php" method="post">
                    <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">

                    <!-- Enter Amount -->
                    <label for="amount">Top Up Amount (RM):</label>
                    <input type="number" id="amount" name="amount" min="5" max="500" required
                        value="<?php echo $amount; ?>">

                    <!-- Preset Amount Buttons -->
                    <div class="preset-buttons">
                        <button type="submit" class="preset-btn" name="preset_amount" value="50" formaction="top_up.php"
                            formnovalidate>RM50</button>
                        <button type="submit" class="preset-btn" name="preset_amount" value="100"
                            formaction="top_up.php" formnovalidate>RM100</button>
                        <button type="submit" class="preset-btn" name="preset_amount" value="150"
                            formaction="top_up.php" formnovalidate>RM150</button>
                        <button type="submit" class="preset-btn" name="preset_amount" value="200"
                            formaction="top_up.php" formnovalidate>RM200</button>
                    </div>

                    <!-- Payment Method Selection -->
                    <label for="payment_method">Select Payment Method:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">-- Choose Payment Method --</option>
                        <option value="card" <?php if ($payment_method == 'card')
                            echo 'selected'; ?>>Debit / Credit Card
                        </option>
                        <option value="online_banking" <?php if ($payment_method == 'online_banking')
                            echo 'selected'; ?>>
                            Online Banking / FPX</option>
                        <option value="tng" <?php if ($payment_method == 'tng')
                            echo 'selected'; ?>>Touch 'N Go eWallet
                        </option>
                        <option value="grabpay" <?php if ($payment_method == 'grabpay')
                            echo 'selected'; ?>>GrabPay
                        </option>
                        <option value="shopeepay" <?php if ($payment_method == 'shopeepay')
                            echo 'selected'; ?>>ShopeePay
                        </option>
                        <option value="cash" <?php if ($payment_method == 'cash')
                            echo 'selected'; ?>>Cash at Store
                        </option>
                    </select>

                    <!-- Bank Selection (For FPX / Online Banking) -->
                    <div id="bank-selection-container">
                        <label for="bank">Select Bank (Malaysia):</label>
                        <select id="bank" name="bank">
                            <option value="">-- Select Bank --</option>
                            <option value="maybank" <?php if ($bank == 'maybank')
                                echo 'selected'; ?>>Maybank</option>
                            <option value="cimb" <?php if ($bank == 'cimb')
                                echo 'selected'; ?>>CIMB Bank</option>
                            <option value="public" <?php if ($bank == 'public')
                                echo 'selected'; ?>>Public Bank</option>
                            <option value="rhb" <?php if ($bank == 'rhb')
                                echo 'selected'; ?>>RHB Bank</option>
                            <option value="hlb" <?php if ($bank == 'hlb')
                                echo 'selected'; ?>>Hong Leong Bank</option>
                            <option value="ambank" <?php if ($bank == 'ambank')
                                echo 'selected'; ?>>AmBank</option>
                        </select>
                    </div>

                    <!-- Account Number -->
                    <label for="account_number">Your Bank / E-Wallet Account Number:</label>
                    <input type="text" id="account_number" name="account_number" maxlength="20" required
                        value="<?php echo $account_number; ?>">

                    <!-- Submit -->
                    <div class="submit-buttons">
                        <input type="submit" value="Confirm Top Up">
                        <input type="reset" value="Reset">
                    </div>
                </form>
        </section>

    </main>

    <?php include 'footer.php'; ?>

</body>

</html>