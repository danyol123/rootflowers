<?php
session_start();

// Admin guard
if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

// CSRF helper
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];

$servername = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'DB';

$conn = mysqli_connect($servername, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Sorting allow-list
$allowed_sorts = [
    'id' => 'history_id',
    'username' => 'username',
    'ip' => 'ip',
    'login_at' => 'login_at'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'login_at';
$dir = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'asc' : 'desc';
$order_by = $allowed_sorts[$sort];
$order_clause = "ORDER BY $order_by " . ($dir === 'asc' ? 'ASC' : 'DESC');

// Get view modal
$view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
$modal_row = null;
if ($view_id > 0) {
    $stmt = $conn->prepare('SELECT * FROM login_history WHERE history_id = ? LIMIT 1');
    $stmt->bind_param('i', $view_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) $modal_row = $res->fetch_assoc();
    $stmt->close();
}

// Fetch logins
$sql = "SELECT * FROM login_history " . $order_clause;
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Root Flower — Logins</title>
    <link rel="stylesheet" href="styles/styles.css">
    
</head>
<body class="rf-root">
<main class="admin-main">
    <aside class="admin-sidebar">
        <?php include 'admin_sidebar.php'; ?>
    </aside>
    <section class="admin-content">
        <div class="rf-list-container">
            <div class="rf-panel">
                <?php if ($modal_row): ?>
                <div class="rf-modal-inline">
                    <h2>View Login #<?php echo intval($modal_row['history_id']); ?></h2>
                    <p><strong>User:</strong> <?php echo htmlspecialchars($modal_row['username']); ?></p>
                    <p><strong>IP:</strong> <?php echo htmlspecialchars($modal_row['ip']); ?></p>
                    <p><strong>User Agent:</strong><br><div class="rf-agent"><?php echo htmlspecialchars($modal_row['user_agent']); ?></div></p>
                    <p><strong>Logged At:</strong> <?php echo htmlspecialchars($modal_row['login_at']); ?></p>
                    <p><a class="rf-btn rf-btn-ghost" href="view_login.php">Close</a></p>
                    <hr>
                </div>
                <?php endif; ?>

                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">User Logins</h1>
                        <p class="rf-muted">All successful login events</p>
                    </div>
                    <div class="rf-nowrap">
                        <small class="rf-muted">Total: <?php echo $result ? $result->num_rows : 0; ?></small>
                    </div>
                </div>

                <?php if ($result && $result->num_rows > 0): ?>
                <div class="rf-table-responsive">
                    <table class="rf-data-table" role="table">
                        <thead>
                            <tr>
                                <?php
                                function header_link_login($key, $label) {
                                    global $sort, $dir;
                                    $next = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
                                    $indicator = ($sort === $key) ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
                                    $href = '?sort=' . urlencode($key) . '&dir=' . $next;
                                    return '<th><a href="' . htmlspecialchars($href) . '">' . htmlspecialchars($label) . htmlspecialchars($indicator) . '</a></th>';
                                }
                                echo header_link_login('id', '#');
                                echo header_link_login('username', 'Username');
                                echo header_link_login('ip', 'IP');
                                echo header_link_login('login_at', 'Logged At');
                                ?>
                                <th class="rf-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['history_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['ip']); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['login_at']); ?></small></td>
                                <td class="rf-nowrap">
                                    <a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['history_id']); ?>">View</a>
                                        <form method="post" action="recycle.php" class="rf-inline">
                                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                        <input type="hidden" name="table" value="login_history">
                                        <input type="hidden" name="id" value="<?php echo intval($row['history_id']); ?>">
                                        <input type="hidden" name="action" value="soft_delete">
                                        <button class="rf-btn rf-btn-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No logins found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
</body>
</html>
<?php
$conn->close();
?>
