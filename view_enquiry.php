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

// Ensure the enquiry table has the necessary columns. If you prefer to run this once in your DB admin, comment it out.
$alter_sql = "ALTER TABLE `enquiry` 
    ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `completed` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL DEFAULT NULL";
// MySQL doesn't support IF NOT EXISTS for ADD COLUMN on older versions; run safely and ignore errors.
@mysqli_query($conn, $alter_sql);

// Handle POST actions: soft-delete, restore, permadelete, toggle complete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        die('Invalid CSRF token');
    }

    if (isset($_POST['action']) && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        switch ($_POST['action']) {
            case 'soft_delete':
                $stmt = $conn->prepare("UPDATE enquiry SET deleted = 1, deleted_at = NOW() WHERE enquiry_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'restore':
                $stmt = $conn->prepare("UPDATE enquiry SET deleted = 0, deleted_at = NULL WHERE enquiry_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'perma_delete':
                $stmt = $conn->prepare("DELETE FROM enquiry WHERE enquiry_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'toggle_complete':
                // Toggle complete state
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

// Fetch active enquiries (deleted = 0)
$active_sql = "SELECT * FROM enquiry WHERE deleted = 0 ORDER BY submit_date DESC";
$active_result = $conn->query($active_sql);

// Fetch deleted enquiries (deleted = 1)
$deleted_sql = "SELECT * FROM enquiry WHERE deleted = 1 ORDER BY deleted_at DESC";
$deleted_result = $conn->query($deleted_sql);
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
    <title>Root Flower — Enquiries</title>
    <!-- Use the namespaced stylesheet -->
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body class="rf-root">

<main class="admin-main">
    <aside class="admin-sidebar">
        <!-- Sidebar -->
    	<?php include 'admin_sidebar.php'; ?>
    </aside>

    <section class="admin-content">
        <div class="rf-list-container">
            <div class="rf-panel">
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">All Enquiries</h1>
                        <p class="rf-muted">Showing active enquiries (not deleted)</p>
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
                            <?php $completed = intval($row['completed']); ?>
                            <tr class="<?php echo $completed ? 'rf-row-completed' : ''; ?>">
                                <td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?><br><small class="rf-muted"><?php echo htmlspecialchars($row['firstname']); ?> <?php echo htmlspecialchars($row['lastname']); ?></small></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['enquiry_type']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['submit_date']); ?></small></td>
                                <td class="rf-nowrap">
                                    <!-- Complete toggle -->
                                    <form method="post" style="display:inline-block; margin-right:.35rem">
                                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                        <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                        <input type="hidden" name="action" value="toggle_complete">
                                        <button class="rf-btn rf-btn-ghost" title="Toggle complete" type="submit"><?php echo $completed ? 'Mark Open' : 'Complete'; ?></button>
                                    </form>

                                    <!-- Soft delete (move to deleted) -->
                                    <form method="post" onsubmit="return confirm('Move this enquiry to Deleted?');" style="display:inline-block">
                                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                        <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
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
                    <p>No enquiries found.</p>
                <?php endif; ?>
            </div>

            <!-- Deleted / Recycle area -->
            <div class="rf-panel rf-deleted-section">
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">Deleted Enquiries (Recycle)</h1>
                        <p class="rf-muted">You can restore or permanently delete entries from here.</p>
                    </div>
                    <div class="rf-nowrap">
                        <small class="rf-muted">Total deleted: <?php echo $deleted_result ? $deleted_result->num_rows : 0; ?></small>
                    </div>
                </div>

                <?php if ($deleted_result && $deleted_result->num_rows > 0): ?>
                <div class="rf-table-responsive">
                    <table class="rf-data-table" role="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email / Phone</th>
                                <th>Type</th>
                                <th>Comments</th>
                                <th>Deleted At</th>
                                <th class="rf-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $deleted_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['enquiry_type']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['deleted_at']); ?></small></td>
                                <td class="rf-nowrap">
                                    <!-- Restore -->
                                    <form method="post" style="display:inline-block; margin-right:.35rem">
                                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                        <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                        <input type="hidden" name="action" value="restore">
                                        <button class="rf-btn rf-btn-restore" type="submit">Restore</button>
                                    </form>

                                    <!-- Permanent Delete -->
                                    <form method="post" onsubmit="return confirm('Permanently delete this enquiry? This cannot be undone.');" style="display:inline-block">
                                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                        <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                        <input type="hidden" name="action" value="perma_delete">
                                        <button class="rf-btn rf-btn-danger" type="submit">Delete Permanently</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p>No deleted enquiries. The recycle bin is empty.</p>
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
