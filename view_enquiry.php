<?php
session_start();

// Basic admin check - adjust according to your login implementation
if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

// Simple CSRF token helper
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

// Ensure necessary columns exist (for quick setup; use migrations in production)
$alter_sql = "ALTER TABLE `enquiry` 
    ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `completed` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL DEFAULT NULL";
@mysqli_query($conn, $alter_sql);

// Handle POST actions for this page: mark_complete, mark_open
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        die('Invalid CSRF token');
    }

    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        switch ($_POST['action']) {
            case 'mark_complete':
                // Move to Completed list (not to recycle) — set completed flag
                $stmt = $conn->prepare("UPDATE enquiry SET completed = 1, completed_at = NOW() WHERE enquiry_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;

            case 'mark_open':
                // Undo completed (move back to active)
                $stmt = $conn->prepare("UPDATE enquiry SET completed = 0, completed_at = NULL WHERE enquiry_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;

            // keep toggle_complete only if you still want it (optional)
            case 'toggle_complete':
                $stmt = $conn->prepare("SELECT completed FROM enquiry WHERE enquiry_id = ? LIMIT 1");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->bind_result($cur);
                if ($stmt->fetch()) {
                    $stmt->close();
                    $new = $cur ? 0 : 1;
                    if ($new) {
                        $stmt2 = $conn->prepare("UPDATE enquiry SET completed = 1, completed_at = NOW() WHERE enquiry_id = ?");
                    } else {
                        $stmt2 = $conn->prepare("UPDATE enquiry SET completed = 0, completed_at = NULL WHERE enquiry_id = ?");
                    }
                    $stmt2->bind_param('i', $id);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $stmt->close();
                }
                break;
        }
    }

    // Redirect to avoid form re-submission
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch active enquiries: not deleted and not completed
$active_sql = "SELECT * FROM enquiry WHERE deleted = 0 AND completed = 0 ORDER BY submit_date DESC";
$active_result = $conn->query($active_sql);

// Fetch completed enquiries: not deleted and completed
$completed_sql = "SELECT * FROM enquiry WHERE deleted = 0 AND completed = 1 ORDER BY completed_at DESC";
$completed_result = $conn->query($completed_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Root Flower">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
    <title>Root Flower — Enquiries</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class="rf-root">

<main class="admin-main">
    <aside class="admin-sidebar">
        <h2>Admin Panel</h2>
        <a href="view_register.php">Workshop Registrations</a>
        <a href="view_enquiry.php" class="rf-active">Enquiries</a>
        <a href="view_membership.php">Memberships</a>
        <a href="view_login.php">Logins</a>
        <a href="recycle.php">Recycle Bin</a>
        <a href="index.php">Go back to Home</a>
    </aside>

    <section class="admin-content">
        <div class="rf-list-container">

            <!-- Active enquiries -->
            <div class="rf-panel">
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">All Enquiries</h1>
                        <p class="rf-muted">Active enquiries (not deleted and not completed)</p>
                    </div>
                    <div class="rf-nowrap">
                        <small class="rf-muted">Total active: <?php echo $active_result ? $active_result->num_rows : 0; ?></small>
                    </div>
                </div>

                <?php if ($active_result && $active_result->num_rows > 0): ?>
                <div class="rf-table-responsive">
                    <table class="rf-data-table" role="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email / Phone</th>
                                <th>Type</th>
                                <th>Comments</th>
                                <th>Submitted At</th>
                                <th class="rf-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $active_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?><br><small class="rf-muted"><?php echo htmlspecialchars($row['firstname']); ?> <?php echo htmlspecialchars($row['lastname']); ?></small></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['enquiry_type']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['submit_date']); ?></small></td>
                                <td class="rf-nowrap">
                                    <div class="rf-actions">
                                        <!-- Mark Complete: moves enquiry to Completed section -->
                                        <form method="post" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                            <input type="hidden" name="action" value="mark_complete">
                                            <button class="rf-btn rf-btn-complete" type="submit" title="Mark as completed">Complete</button>
                                        </form>

                                        <!-- Delete button — sends to recycle.php (soft-delete) -->
                                        <form method="post" action="recycle.php" class="rf-inline" onsubmit="return confirm('Are you sure you want to delete this enquiry? It will move to Recycle Bin.')">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="table" value="enquiry">
                                            <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                            <input type="hidden" name="action" value="soft_delete">
                                            <button class="rf-btn rf-btn-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No active enquiries found.</p>
                <?php endif; ?>
            </div>

            <!-- Completed enquiries -->
            <div class="rf-panel">
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">Completed Enquiries</h1>
                        <p class="rf-muted">Enquiries marked completed. From here you can reopen them or move them to the Recycle Bin.</p>
                    </div>
                    <div class="rf-nowrap">
                        <small class="rf-muted">Total completed: <?php echo $completed_result ? $completed_result->num_rows : 0; ?></small>
                    </div>
                </div>

                <?php if ($completed_result && $completed_result->num_rows > 0): ?>
                <div class="rf-table-responsive">
                    <table class="rf-data-table" role="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email / Phone</th>
                                <th>Type</th>
                                <th>Comments</th>
                                <th>Completed At</th>
                                <th class="rf-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $completed_result->fetch_assoc()): ?>
                            <tr class="rf-row-completed">
                                <td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['enquiry_type']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['completed_at']); ?></small></td>
                                <td class="rf-nowrap">
                                    <div class="rf-actions">
                                        <!-- Mark Open (undo completed) -->
                                        <form method="post" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                            <input type="hidden" name="action" value="mark_open">
                                            <button class="rf-btn rf-btn-ghost" type="submit">Mark Open</button>
                                        </form>

                                        <!-- Move to Recycle (soft-delete) posts to recycle.php -->
                                        <form method="post" action="recycle.php" class="rf-inline" onsubmit="return confirm('Move this completed enquiry to the Recycle Bin?');">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                            <input type="hidden" name="action" value="soft_delete">
                                            <input type="hidden" name="table" value="enquiry">
                                            <button class="rf-btn rf-btn-danger" type="submit">Move to Recycle</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No completed enquiries.</p>
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
