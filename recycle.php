<?php
session_start();

// Basic admin check
if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin')) {
    header('Location: login.php');
    exit();
}

// CSRF token (reuse session token)
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

/*
 * Setup: ensure the four tables have deleted/deleted_at columns.
 * Replace with proper migrations for production.
 */
$tables_to_alter = ['registrations','enquiry','memberships','login_history'];
foreach ($tables_to_alter as $t) {
    $sql = "ALTER TABLE `{$t}` 
        ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
        ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL";
    @mysqli_query($conn, $sql);
}
// For enquiry also ensure completed exists (if not already added).
@mysqli_query($conn, "ALTER TABLE `enquiry` ADD COLUMN IF NOT EXISTS `completed` TINYINT(1) NOT NULL DEFAULT 0, ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL DEFAULT NULL");

/*
 * POST actions: restore / perma_delete / soft_delete.
 * We accept only safe table names (whitelist) and known PK columns.
 */
$whitelist = [
    'registrations' => 'registration_id',
    'enquiry' => 'enquiry_id',
    'memberships' => 'membership_id',
    'login_history' => 'history_id'
];

$action = $_POST['action'] ?? null;
$table  = $_POST['table']  ?? null;
$id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
$confirm = isset($_POST['confirm']) && $_POST['confirm'] == '1';

// CPU: if a destructive action is posted, but not yet confirmed, show confirmation page.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['soft_delete','perma_delete']) && array_key_exists($table, $whitelist) && $id > 0 && !$confirm) {
    // Fetch item preview (small set of fields depending on table)
    $pk = $whitelist[$table];
    $preview = null;
    $stmt = $conn->prepare("SELECT * FROM {$table} WHERE {$pk} = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $preview = $res->fetch_assoc();
    }
    $stmt->close();

    // Render confirmation page (no JS) with two forms (confirm or cancel)
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Confirm Action — Recycle</title>
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <link rel="stylesheet" href="styles/styles.css">
        <style>
            /* small namespaced helpers just for the confirmation UI (no inline attributes) */
            .rf-confirm { max-width:760px; margin:40px auto; padding:24px; background:#fff; border-radius:12px; box-shadow:0 8px 30px rgba(0,0,0,0.06); }
            .rf-confirm h2 { margin:0 0 12px; color:#3b2a74; }
            .rf-confirm .rf-meta { margin-bottom:16px; color: #444; }
            .rf-confirm .rf-preview { background:#faf7ff; padding:12px; border-radius:8px; margin-bottom:16px; }
            .rf-confirm .rf-actions { display:flex; gap:10px; }
        </style>
    </head>
    <body class="rf-root">
    <main class="admin-main">
        <aside class="admin-sidebar">
            <!-- Sidebar -->
            <?php include 'admin_sidebar.php'; ?>
        </aside>

        <section class="admin-content">
            <div class="rf-confirm">
                <h2>Confirm <?php echo htmlspecialchars($action === 'soft_delete' ? 'Move to Recycle' : 'Permanent Delete'); ?></h2>
                <div class="rf-meta">You are about to <?php echo $action === 'soft_delete' ? 'move this item to the Recycle Bin' : 'permanently delete this item'; ?>. This action <?php echo $action === 'perma_delete' ? 'cannot be undone.' : 'can be restored from the Recycle Bin.'; ?></div>

                            <?php if ($preview): ?>
                    <div class="rf-preview">
                        <?php
                        // show a few helpful fields depending on table
                        if ($table === 'enquiry') {
                            echo '<strong>Enquiry ID:</strong> ' . htmlspecialchars($preview['enquiry_id']) . '<br>';
                            echo '<strong>Name:</strong> ' . htmlspecialchars($preview['firstname'] . ' ' . $preview['lastname']) . '<br>';
                            echo '<strong>Email:</strong> ' . htmlspecialchars($preview['email']) . '<br>';
                            echo '<strong>Comments:</strong> <div class="rf-preserve">' . htmlspecialchars($preview['comments']) . '</div>';
                        } else {
                            // generic preview for other tables
                            echo '<pre class="rf-preserve rf-pre-zero">' . htmlspecialchars(print_r($preview, true)) . '</pre>';
                        }
                        ?>
                    </div>
                <?php else: ?>
                    <div class="rf-preview"><em>Item not found (it may already have been removed).</em></div>
                <?php endif; ?>

                <div class="rf-actions">
                    <!-- Confirm form -->
                    <form method="post">
                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                        <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                        <input type="hidden" name="id" value="<?php echo intval($id); ?>">
                        <input type="hidden" name="action" value="<?php echo htmlspecialchars($action); ?>">
                        <input type="hidden" name="confirm" value="1">
                        <button class="rf-btn rf-btn-danger" type="submit"><?php echo $action === 'soft_delete' ? 'Confirm Move to Recycle' : 'Confirm Permanent Delete'; ?></button>
                    </form>

                    <!-- Cancel: go back to recycle list -->
                    <form method="get" action="recycle.php">
                        <button class="rf-btn rf-btn-ghost" type="submit">Cancel</button>
                    </form>
                </div>
            </div>
        </section>
    </main>
    </body>
    </html>
    <?php
    // stop here, do not continue with default list render
    exit();
}

// If reached here and POST is destructive + confirm=1, perform action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['soft_delete','perma_delete']) && array_key_exists($table, $whitelist) && $id > 0 && $confirm) {
    $pk = $whitelist[$table];
    if ($action === 'soft_delete') {
        $stmt = $conn->prepare("UPDATE {$table} SET deleted = 1, deleted_at = NOW() WHERE {$pk} = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'perma_delete') {
        $stmt = $conn->prepare("DELETE FROM {$table} WHERE {$pk} = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
    }
    // After performing action, redirect to avoid resubmit and show updated recycle list
    header('Location: recycle.php');
    exit();
}

// Restore action (no confirmation, safe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'restore' && array_key_exists($table, $whitelist) && $id > 0) {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        die('Invalid CSRF token');
    }
    $pk = $whitelist[$table];
    $stmt = $conn->prepare("UPDATE {$table} SET deleted = 0, deleted_at = NULL WHERE {$pk} = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: recycle.php');
    exit();
}

/*
 * If not a confirmation flow, show the recycle listing (deleted items grouped by table).
 */

// Enquiry filter: optionally show only completed deleted enquiries (via GET)
$show_completed = isset($_GET['show_completed']) && $_GET['show_completed'] == '1' ? 1 : null;
$enquiry_query = "SELECT * FROM enquiry WHERE deleted = 1";
if ($show_completed !== null) {
    $enquiry_query .= " AND completed = " . intval($show_completed);
}
$enquiry_query .= " ORDER BY deleted_at DESC";
$deleted_enquiries = $conn->query($enquiry_query);

// Deleted users, registrations, memberships
// users table no longer used
$deleted_regs  = $conn->query("SELECT * FROM registrations WHERE deleted = 1 ORDER BY deleted_at DESC");
$deleted_members = $conn->query("SELECT * FROM memberships WHERE deleted = 1 ORDER BY deleted_at DESC");
// Deleted logins
$deleted_logins = $conn->query("SELECT * FROM login_history WHERE deleted = 1 ORDER BY deleted_at DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recycle Bin — Root Flower</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
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
                        <h1 class="rf-h1">Recycle Bin</h1>
                        <p class="rf-muted">Deleted items from Users, Registrations, Enquiries and Memberships. Restore or permanently delete.</p>
                    </div>
                    <div class="rf-nowrap">
                        <small class="rf-muted">Tip: use the action buttons to restore or permanently delete items.</small>
                    </div>
                </div>

                <!-- Enquiries (deleted) -->
                <div class="rf-panel">
                    <div class="rf-meta">
                        <div><h2 style="margin:0">Deleted Enquiries</h2></div>
                        <div class="rf-nowrap">
                            <form method="get" class="rf-inline">
                                <label class="rf-filter-label">
                                    <input type="checkbox" name="show_completed" value="1" <?php echo ($show_completed === 1) ? 'checked' : ''; ?>>
                                    Show only completed
                                </label>
                                <button type="submit" class="rf-btn rf-btn-ghost rf-btn-ml">Apply</button>
                            </form>
                        </div>
                    </div>

                    <?php if ($deleted_enquiries && $deleted_enquiries->num_rows > 0): ?>
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
                                        <th>Status</th>
                                        <th class="rf-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($row = $deleted_enquiries->fetch_assoc()): ?>
                                    <?php $completed = intval($row['completed']); ?>
                                    <tr class="<?php echo $completed ? 'rf-row-completed' : ''; ?>">
                                        <td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
                                        <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                        <td><?php echo htmlspecialchars($row['enquiry_type']); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                        <td><small class="rf-muted"><?php echo htmlspecialchars($row['deleted_at']); ?></small></td>
                                        <td><?php echo $completed ? '<small class="rf-status-completed">Completed</small>' : '<small class="rf-muted">Open</small>'; ?></td>
                                        <td class="rf-nowrap">
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="enquiry">
                                                <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                                <input type="hidden" name="action" value="restore">
                                                <button class="rf-btn rf-btn-restore" type="submit">Restore</button>
                                            </form>

                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="enquiry">
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
                        <p>No deleted enquiries found.</p>
                    <?php endif; ?>
                </div>

                <!-- Registrations -->
                <div class="rf-panel">
                    <h3 style="margin-top:0">Deleted Registrations</h3>
                    <?php if ($deleted_regs && $deleted_regs->num_rows > 0): ?>
                        <div class="rf-table-responsive">
                            <table class="rf-data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name / Email</th>
                                        <th>Deleted At</th>
                                        <th class="rf-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($r = $deleted_regs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['registration_id']); ?></td>
                                        <td><?php echo htmlspecialchars($r['name'] ?? ($r['firstname'].' '.$r['lastname'] ?? '')) . ' / ' . htmlspecialchars($r['email'] ?? ''); ?></td>
                                        <td><small class="rf-muted"><?php echo htmlspecialchars($r['deleted_at']); ?></small></td>
                                        <td class="rf-nowrap">
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="registrations">
                                                <input type="hidden" name="id" value="<?php echo intval($r['registration_id']); ?>">
                                                <input type="hidden" name="action" value="restore">
                                                <button class="rf-btn rf-btn-restore" type="submit">Restore</button>
                                            </form>
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="registrations">
                                                <input type="hidden" name="id" value="<?php echo intval($r['registration_id']); ?>">
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
                        <p>No deleted registrations found.</p>
                    <?php endif; ?>
                </div>

                <!-- Users section removed, not used anymore -->

                <!-- Memberships -->
                <div class="rf-panel">
                    <h3 style="margin-top:0">Deleted Memberships</h3>
                    <?php if ($deleted_members && $deleted_members->num_rows > 0): ?>
                        <div class="rf-table-responsive">
                            <table class="rf-data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name / Email</th>
                                        <th>Deleted At</th>
                                        <th class="rf-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($m = $deleted_members->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($m['membership_id']); ?></td>
                                        <td><?php echo htmlspecialchars($m['name'] ?? ($m['firstname'].' '.$m['lastname'] ?? '')) . ' / ' . htmlspecialchars($m['email'] ?? ''); ?></td>
                                        <td><small class="rf-muted"><?php echo htmlspecialchars($m['deleted_at']); ?></small></td>
                                        <td class="rf-nowrap">
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="memberships">
                                                <input type="hidden" name="id" value="<?php echo intval($m['membership_id']); ?>">
                                                <input type="hidden" name="action" value="restore">
                                                <button class="rf-btn rf-btn-restore" type="submit">Restore</button>
                                            </form>
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="memberships">
                                                <input type="hidden" name="id" value="<?php echo intval($m['membership_id']); ?>">
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
                        <p>No deleted memberships found.</p>
                    <?php endif; ?>
                </div>

                <!-- Logins -->
                <div class="rf-panel">
                    <h3 style="margin-top:0">Deleted Logins</h3>
                    <?php if ($deleted_logins && $deleted_logins->num_rows > 0): ?>
                        <div class="rf-table-responsive">
                            <table class="rf-data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Username / IP</th>
                                        <th>Deleted At</th>
                                        <th class="rf-nowrap">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php while ($l = $deleted_logins->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($l['history_id']); ?></td>
                                        <td><?php echo htmlspecialchars($l['username'] ?? '') . ' / ' . htmlspecialchars($l['ip'] ?? ''); ?></td>
                                        <td><small class="rf-muted"><?php echo htmlspecialchars($l['deleted_at']); ?></small></td>
                                        <td class="rf-nowrap">
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="login_history">
                                                <input type="hidden" name="id" value="<?php echo intval($l['history_id']); ?>">
                                                <input type="hidden" name="action" value="restore">
                                                <button class="rf-btn rf-btn-restore" type="submit">Restore</button>
                                            </form>
                                            <form method="post" class="rf-inline">
                                                <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                                <input type="hidden" name="table" value="login_history">
                                                <input type="hidden" name="id" value="<?php echo intval($l['history_id']); ?>">
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
                        <p>No deleted logins found.</p>
                    <?php endif; ?>
                </div>


            </div>
        </div>
    </section>
</main>

</body>
</html>

<?php
$conn->close();
?>