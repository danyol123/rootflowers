<?php
/*
 * File: view_enquiry.php
 * Description: Admin page for viewing and managing enquiries (server-side CRUD, sorting).
 * Author: Root Flower Team
 * Created: 2025-10-29
 */
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

$create_errors = [];
// Ensure necessary columns exist (for quick setup; use migrations in production)
$alter_sql = "ALTER TABLE `enquiry` 
    ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `completed` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `completed_at` DATETIME NULL DEFAULT NULL";
@mysqli_query($conn, $alter_sql);

// Handle POST actions for this page: mark_complete, mark_open, update, create
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        die('Invalid CSRF token');
    }

    if (isset($_POST['action']) && (isset($_POST['id']) || $_POST['action'] === 'create')) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($_POST['action'] === 'create') $id = 0;
        $id = intval($_POST['id']);
        $shouldRedirect = true;
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
            case 'update':
                // Update enquiry values
                $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
                $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
                $enquiry_type = isset($_POST['enquiry_type']) ? trim($_POST['enquiry_type']) : '';
                $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
                $stmt = $conn->prepare("UPDATE enquiry SET firstname=?, lastname=?, email=?, phone=?, enquiry_type=?, comments=? WHERE enquiry_id=?");
                $stmt->bind_param('ssssssi', $firstname, $lastname, $email, $phone, $enquiry_type, $comments, $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'create':
                $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
                $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
                $enquiry_type = isset($_POST['enquiry_type']) ? trim($_POST['enquiry_type']) : '';
                $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
                $create_errors = [];
                if ($firstname === '') $create_errors[] = 'First name is required.';
                if ($lastname === '') $create_errors[] = 'Last name is required.';
                if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $create_errors[] = 'Valid email is required.';
                if (empty($create_errors)) {
                    $stmt = $conn->prepare("INSERT INTO enquiry (firstname, lastname, email, phone, enquiry_type, comments) VALUES (?,?,?,?,?,?)");
                    $stmt->bind_param('ssssss', $firstname, $lastname, $email, $phone, $enquiry_type, $comments);
                    $stmt->execute();
                    $stmt->close();
                    $_SESSION['flash'] = 'Enquiry created successfully.';
                    // redirect will happen below
                } else {
                    // keep create modal open with values
                    $modal_row = ['firstname'=>$firstname, 'lastname'=>$lastname, 'email'=>$email, 'phone'=>$phone, 'enquiry_type'=>$enquiry_type, 'comments'=>$comments];
                    $create_mode = true;
                    $shouldRedirect = false;
                }
                break;
        }
    }
    // Redirect to avoid form re-submission unless we want to re-render the create modal
    if ($shouldRedirect) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Add server-side sorting allow-list and order clause
$allowed_sorts = [
    'id' => 'enquiry_id',
    'name' => "CONCAT(firstname, ' ', lastname)",
    'email' => 'email',
    'phone' => 'phone',
    'type' => 'enquiry_type',
    'submit_date' => 'submit_date',
    'completed_at' => 'completed_at'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'submit_date';
$dir = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'asc' : 'desc';
$order_by = $allowed_sorts[$sort];
$order_clause = "ORDER BY $order_by " . ($dir === 'asc' ? 'ASC' : 'DESC');

// Fetch active enquiries: not deleted and not completed
$active_sql = "SELECT * FROM enquiry WHERE deleted = 0 AND completed = 0 " . $order_clause;
$active_result = $conn->query($active_sql);

// Fetch completed enquiries: not deleted and completed
$completed_sql = "SELECT * FROM enquiry WHERE deleted = 0 AND completed = 1 " . $order_clause;
$completed_result = $conn->query($completed_sql);

// GET-based identifiers for views and edits
$view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$create_mode = isset($_GET['create']) ? true : false;
$modal_row = null;
if ($view_id || $edit_id) {
    $eid = $view_id ?: $edit_id;
    if ($eid > 0) {
        $stmt = $conn->prepare('SELECT * FROM enquiry WHERE enquiry_id = ? LIMIT 1');
        $stmt->bind_param('i',$eid);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) $modal_row = $res->fetch_assoc();
        $stmt->close();
    }
}
// Flash message (from prior actions, e.g., create)
$flash = null;
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}
?>
<?php
// helper for rendering sortable header links (declare once)
if (!function_exists('header_link')) {
    function header_link($key, $label) {
        global $sort, $dir;
        $next = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
        $indicator = ($sort === $key) ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
        $href = '?sort=' . urlencode($key) . '&dir=' . $next;
        return '<th><a href="' . htmlspecialchars($href) . '">' . htmlspecialchars($label) . htmlspecialchars($indicator) . '</a></th>';
    }
}
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
        <!-- Sidebar -->
    	<?php include 'admin_sidebar.php'; ?>
    </aside>

    <section class="admin-content">
        <div class="rf-list-container">

            <!-- Active enquiries -->
            <div class="rf-panel">
                <?php if ($modal_row || $create_mode): ?>
                <div class="rf-modal-inline">
                    <?php if ($view_id): ?>
                        <h2>View Enquiry #<?php echo htmlspecialchars($modal_row['enquiry_id']); ?></h2>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($modal_row['firstname'] . ' ' . $modal_row['lastname']); ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($modal_row['email']); ?>"><?php echo htmlspecialchars($modal_row['email']); ?></a></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($modal_row['phone']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($modal_row['enquiry_type']); ?></p>
                        <p><strong>Comments:</strong><br><?php echo nl2br(htmlspecialchars($modal_row['comments'])); ?></p>
                        <p><a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($modal_row['enquiry_id']); ?>">Edit</a> <a class="rf-btn rf-btn-ghost" href="view_enquiry.php">Close</a></p>
                        <hr>
                    <?php elseif ($edit_id): ?>
                        <h2>Edit Enquiry #<?php echo htmlspecialchars($modal_row['enquiry_id']); ?></h2>
                        <form method="post">
                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                            <input type="hidden" name="id" value="<?php echo intval($modal_row['enquiry_id']); ?>">
                            <input type="hidden" name="action" value="update">
                            <label>First name<input type="text" name="firstname" value="<?php echo htmlspecialchars($modal_row['firstname']); ?>"></label>
                            <label>Last name<input type="text" name="lastname" value="<?php echo htmlspecialchars($modal_row['lastname']); ?>"></label>
                            <label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($modal_row['email']); ?>"></label>
                            <label>Phone<input type="tel" name="phone" value="<?php echo htmlspecialchars($modal_row['phone']); ?>"></label>
                            <label>Type<input type="text" name="enquiry_type" value="<?php echo htmlspecialchars($modal_row['enquiry_type']); ?>"></label>
                            <label>Comments<textarea name="comments"><?php echo htmlspecialchars($modal_row['comments']); ?></textarea></label>
                            <div class="rf-inline">
                                <button class="rf-btn rf-btn-complete" type="submit">Save</button>
                                <a class="rf-btn rf-btn-ghost" href="view_enquiry.php">Cancel</a>
                            </div>
                        </form>
                        <hr>
                    <?php elseif ($create_mode): ?>
                        <h2>Create Enquiry</h2>
                        <?php if (!empty($create_errors)): ?>
                            <div class="rf-alert rf-alert-danger">
                                <ul>
                                    <?php foreach ($create_errors as $err): ?>
                                        <li><?php echo htmlspecialchars($err); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                            <input type="hidden" name="action" value="create">
                            <label>First name<input type="text" name="firstname" value="<?php echo htmlspecialchars($modal_row['firstname'] ?? ''); ?>"></label>
                            <label>Last name<input type="text" name="lastname" value="<?php echo htmlspecialchars($modal_row['lastname'] ?? ''); ?>"></label>
                            <label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($modal_row['email'] ?? ''); ?>"></label>
                            <label>Phone<input type="tel" name="phone" value="<?php echo htmlspecialchars($modal_row['phone'] ?? ''); ?>"></label>
                            <label>Type<input type="text" name="enquiry_type" value="<?php echo htmlspecialchars($modal_row['enquiry_type'] ?? ''); ?>"></label>
                            <label>Comments<textarea name="comments"><?php echo htmlspecialchars($modal_row['comments'] ?? ''); ?></textarea></label>
                            <div class="rf-inline">
                                <button class="rf-btn rf-btn-complete" type="submit">Create</button>
                                <a class="rf-btn rf-btn-ghost" href="view_enquiry.php">Cancel</a>
                            </div>
                            <hr>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">All Enquiries</h1>
                        <p class="rf-muted">Active enquiries (not deleted and not completed)</p>
                    </div>
                        <div class="rf-nowrap">
                        <small class="rf-muted">Total active: <?php echo $active_result ? $active_result->num_rows : 0; ?></small>
                            <a class="rf-btn rf-btn-ghost rf-btn-ml" href="?create=1">Create</a>
                    </div>
                </div>
                <?php if (!empty($flash)): ?>
                    <div class="rf-alert rf-alert-success">
                        <?php echo htmlspecialchars($flash); ?>
                    </div>
                <?php endif; ?>

                <?php if ($active_result && $active_result->num_rows > 0): ?>
                <div class="rf-table-responsive">
                    <table class="rf-data-table" role="table">
                        <thead>
                            <tr>
                                <?php echo header_link('id', '#'); ?>
                                <?php echo header_link('name', 'Name'); ?>
                                <?php echo header_link('email', 'Email / Phone'); ?>
                                <?php echo header_link('type', 'Type'); ?>
                                <th>Comments</th>
                                <?php echo header_link('submit_date', 'Submitted At'); ?>
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
                                        <a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['enquiry_id']); ?>">View</a>
                                        <a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($row['enquiry_id']); ?>">Edit</a>
                                        <!-- Mark Complete: moves enquiry to Completed section -->
                                        <form method="post" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                            <input type="hidden" name="action" value="mark_complete">
                                            <button class="rf-btn rf-btn-complete" type="submit" title="Mark as completed">Complete</button>
                                        </form>

                                        <!-- Delete button — sends to recycle.php (soft-delete) -->
                                        <form method="post" action="recycle.php" class="rf-inline">
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
                                <?php echo header_link('id', '#'); ?>
                                <?php echo header_link('name', 'Name'); ?>
                                <?php echo header_link('email', 'Email / Phone'); ?>
                                <?php echo header_link('type', 'Type'); ?>
                                <th>Comments</th>
                                <?php echo header_link('completed_at', 'Completed At'); ?>
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
                                        <a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['enquiry_id']); ?>">View</a>
                                        <a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($row['enquiry_id']); ?>">Edit</a>
                                        <!-- Mark Open (undo completed) -->
                                        <form method="post" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['enquiry_id']); ?>">
                                            <input type="hidden" name="action" value="mark_open">
                                            <button class="rf-btn rf-btn-ghost rf-btn-complete" type="submit">Mark Open</button>
                                        </form>

                                        <!-- Move to Recycle (soft-delete) posts to recycle.php -->
                                        <form method="post" action="recycle.php" class="rf-inline">
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
