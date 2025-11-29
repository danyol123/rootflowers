<?php
/*
 * File: view_register.php
 * Description: Admin page for viewing and managing registrations (server-side CRUD, sorting).
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

/* Server-side sorting: allow list to prevent SQL injection */
$allowed_sorts = [
    'id' => 'registration_id',
    'name' => "CONCAT(firstname, ' ', lastname)",
    'email' => 'email',
    'phone' => 'phone',
    'workshop_date' => 'workshop_date',
    'participants' => 'participants',
    'type' => 'workshop_type',
    'processed_at' => 'processed_at',
    'addons' => 'addons',
    'comments' => 'comments',
    'reg_date' => 'reg_date'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'reg_date';
$dir = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'asc' : 'desc';
$order_by = $allowed_sorts[$sort];

// Ensure necessary columns exist (for quick setup; use migrations in production)
$alter_sql = "ALTER TABLE `registrations` 
    ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `processed` TINYINT(1) NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS `processed_at` DATETIME NULL DEFAULT NULL";
@mysqli_query($conn, $alter_sql);

// Handle POST actions for processing registrations: mark_processed, mark_open
// initialize create errors container
$create_errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
        die('Invalid CSRF token');
    }

    $shouldRedirect = true;
    if (isset($_POST['action']) && (isset($_POST['id']) || $_POST['action'] === 'create')) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        switch ($_POST['action']) {
            case 'mark_processed':
                $stmt = $conn->prepare("UPDATE registrations SET processed = 1, processed_at = NOW() WHERE registration_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'mark_open':
                $stmt = $conn->prepare("UPDATE registrations SET processed = 0, processed_at = NULL WHERE registration_id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'toggle_processed':
                $stmt = $conn->prepare("SELECT processed FROM registrations WHERE registration_id = ? LIMIT 1");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->bind_result($cur);
                if ($stmt->fetch()) {
                    $stmt->close();
                    $new = $cur ? 0 : 1;
                    if ($new) {
                        $stmt2 = $conn->prepare("UPDATE registrations SET processed = 1, processed_at = NOW() WHERE registration_id = ?");
                    } else {
                        $stmt2 = $conn->prepare("UPDATE registrations SET processed = 0, processed_at = NULL WHERE registration_id = ?");
                    }
                    $stmt2->bind_param('i', $id);
                    $stmt2->execute();
                    $stmt2->close();
                } else {
                    $stmt->close();
                }
                break;
            case 'update':
                // Update registration — allow editing of specific fields
                $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
                $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
                $street = isset($_POST['street']) ? trim($_POST['street']) : '';
                $city = isset($_POST['city']) ? trim($_POST['city']) : '';
                $state = isset($_POST['state']) ? trim($_POST['state']) : '';
                $postcode = isset($_POST['postcode']) ? trim($_POST['postcode']) : '';
                $workshop_date = isset($_POST['workshop_date']) ? trim($_POST['workshop_date']) : null;
                $participants = isset($_POST['participants']) ? trim($_POST['participants']) : '';
                $workshop_type = isset($_POST['workshop_type']) ? trim($_POST['workshop_type']) : '';
                $addons = isset($_POST['addons']) ? trim($_POST['addons']) : '';
                $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
                $stmt = $conn->prepare("UPDATE registrations SET firstname=?, lastname=?, email=?, phone=?, street=?, city=?, state=?, postcode=?, workshop_date=?, participants=?, workshop_type=?, addons=?, comments=? WHERE registration_id=?");
                $stmt->bind_param('sssssssssssssi', $firstname, $lastname, $email, $phone, $street, $city, $state, $postcode, $workshop_date, $participants, $workshop_type, $addons, $comments, $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'create':
                // Create new registration; do simple validation
                $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
                $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
                $email = isset($_POST['email']) ? trim($_POST['email']) : '';
                $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
                $street = isset($_POST['street']) ? trim($_POST['street']) : '';
                $city = isset($_POST['city']) ? trim($_POST['city']) : '';
                $state = isset($_POST['state']) ? trim($_POST['state']) : '';
                $postcode = isset($_POST['postcode']) ? trim($_POST['postcode']) : '';
                $workshop_date = isset($_POST['workshop_date']) ? trim($_POST['workshop_date']) : null;
                $participants = isset($_POST['participants']) ? trim($_POST['participants']) : '';
                $workshop_type = isset($_POST['workshop_type']) ? trim($_POST['workshop_type']) : '';
                $addons = isset($_POST['addons']) ? trim($_POST['addons']) : '';
                $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';
                $create_errors = [];
                if ($firstname === '') $create_errors[] = 'First name is required.';
                if ($lastname === '') $create_errors[] = 'Last name is required.';
                if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $create_errors[] = 'Valid email is required.';
                if (empty($create_errors)) {
                    $stmt = $conn->prepare("INSERT INTO registrations (firstname, lastname, email, street, city, state, postcode, phone, workshop_date, participants, workshop_type, addons, comments) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt->bind_param('sssssssssssss', $firstname, $lastname, $email, $street, $city, $state, $postcode, $phone, $workshop_date, $participants, $workshop_type, $addons, $comments);
                    $stmt->execute();
                    $stmt->close();
                    $_SESSION['flash'] = 'Registration created successfully.';
                    // redirect on success
                    $shouldRedirect = true;
                } else {
                    $shouldRedirect = false; // do not redirect if there were validation errors
                    // show create UI again with values
                    $modal_row = [
                        'firstname'=>$firstname,'lastname'=>$lastname,'email'=>$email,'phone'=>$phone,'street'=>$street,'city'=>$city,'state'=>$state,'postcode'=>$postcode,'workshop_date'=>$workshop_date,'participants'=>$participants,'workshop_type'=>$workshop_type,'addons'=>$addons,'comments'=>$comments
                    ];
                    $create_mode = true;
                }
                break;
        }
    }

    // Redirect to avoid form re-submission (unless $shouldRedirect was set false)
    if ($shouldRedirect) {
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

$order_clause = "ORDER BY $order_by " . ($dir === 'asc' ? 'ASC' : 'DESC');
// Fetch active registrations: not deleted and not processed
$active_sql = "SELECT * FROM registrations WHERE deleted = 0 AND processed = 0 " . $order_clause;
$active_result = $conn->query($active_sql);

$completed_sql = "SELECT * FROM registrations WHERE deleted = 0 AND processed = 1 " . $order_clause;
$completed_result = $conn->query($completed_sql);

// GET-based view/edit identifiers, no-JS modal
$view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$create_mode = isset($_GET['create']) ? true : false;
$modal_row = null;
if ($view_id || $edit_id) {
    $rid = $view_id ?: $edit_id;
    if ($rid > 0) {
        $stmt = $conn->prepare("SELECT * FROM registrations WHERE registration_id = ? LIMIT 1");
        $stmt->bind_param('i', $rid);
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
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="Pictures/Index/logo.png" type="image/png">
    <title>Root Flower — Registrations</title>
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
                <?php if ($modal_row || $create_mode): ?>
                <div class="rf-modal-inline">
                    <?php if ($view_id): ?>
                        <h2>View Registration #<?php echo htmlspecialchars($modal_row['registration_id']); ?></h2>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($modal_row['firstname'] . ' ' . $modal_row['lastname']); ?></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($modal_row['email']); ?>"><?php echo htmlspecialchars($modal_row['email']); ?></a></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($modal_row['phone']); ?></p>
                        <p><strong>Workshop Date:</strong> <?php echo htmlspecialchars($modal_row['workshop_date']); ?></p>
                        <p><strong>Participants:</strong> <?php echo htmlspecialchars($modal_row['participants']); ?></p>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($modal_row['workshop_type']); ?></p>
                        <p><strong>Add-ons:</strong><br><?php echo nl2br(htmlspecialchars($modal_row['addons'])); ?></p>
                        <p><strong>Comments:</strong><br><?php echo nl2br(htmlspecialchars($modal_row['comments'])); ?></p>
                        <p><a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($modal_row['registration_id']); ?>">Edit</a> <a class="rf-btn rf-btn-ghost" href="view_register.php">Close</a></p>
                        <hr>
                    <?php elseif ($edit_id): ?>
                        <h2>Edit Registration #<?php echo htmlspecialchars($modal_row['registration_id']); ?></h2>
                        <form method="post">
                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                            <input type="hidden" name="id" value="<?php echo intval($modal_row['registration_id']); ?>">
                            <input type="hidden" name="action" value="update">
                            <label>First name<input type="text" name="firstname" value="<?php echo htmlspecialchars($modal_row['firstname']); ?>"></label>
                            <label>Last name<input type="text" name="lastname" value="<?php echo htmlspecialchars($modal_row['lastname']); ?>"></label>
                            <label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($modal_row['email']); ?>"></label>
                            <label>Phone<input type="tel" name="phone" value="<?php echo htmlspecialchars($modal_row['phone']); ?>"></label>
                            <label>Street<input type="text" name="street" value="<?php echo htmlspecialchars($modal_row['street']); ?>"></label>
                            <label>City<input type="text" name="city" value="<?php echo htmlspecialchars($modal_row['city']); ?>"></label>
                            <label>State<input type="text" name="state" value="<?php echo htmlspecialchars($modal_row['state']); ?>"></label>
                            <label>Postcode<input type="text" name="postcode" value="<?php echo htmlspecialchars($modal_row['postcode']); ?>"></label>
                            <label>Workshop Date<input type="date" name="workshop_date" value="<?php echo htmlspecialchars($modal_row['workshop_date']); ?>"></label>
                            <label>Participants<input type="text" name="participants" value="<?php echo htmlspecialchars($modal_row['participants']); ?>"></label>
                            <label>Workshop Type<input type="text" name="workshop_type" value="<?php echo htmlspecialchars($modal_row['workshop_type']); ?>"></label>
                            <label>Add-ons<textarea name="addons"><?php echo htmlspecialchars($modal_row['addons']); ?></textarea></label>
                            <label>Comments<textarea name="comments"><?php echo htmlspecialchars($modal_row['comments']); ?></textarea></label>
                            <div class="rf-inline">
                                <button class="rf-btn rf-btn-complete" type="submit">Save</button>
                                <a class="rf-btn rf-btn-ghost" href="view_register.php">Cancel</a>
                            </div>
                        </form>
                        <hr>
                    <?php elseif ($create_mode): ?>
                        <h2>Create Registration</h2>
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
                            <label>Street<input type="text" name="street" value="<?php echo htmlspecialchars($modal_row['street'] ?? ''); ?>"></label>
                            <label>City<input type="text" name="city" value="<?php echo htmlspecialchars($modal_row['city'] ?? ''); ?>"></label>
                            <label>State<input type="text" name="state" value="<?php echo htmlspecialchars($modal_row['state'] ?? ''); ?>"></label>
                            <label>Postcode<input type="text" name="postcode" value="<?php echo htmlspecialchars($modal_row['postcode'] ?? ''); ?>"></label>
                            <label>Workshop Date<input type="date" name="workshop_date" value="<?php echo htmlspecialchars($modal_row['workshop_date'] ?? ''); ?>"></label>
                            <label>Participants<input type="text" name="participants" value="<?php echo htmlspecialchars($modal_row['participants'] ?? ''); ?>"></label>
                            <label>Workshop Type<input type="text" name="workshop_type" value="<?php echo htmlspecialchars($modal_row['workshop_type'] ?? ''); ?>"></label>
                            <label>Add-ons<textarea name="addons"><?php echo htmlspecialchars($modal_row['addons'] ?? ''); ?></textarea></label>
                            <label>Comments<textarea name="comments"><?php echo htmlspecialchars($modal_row['comments'] ?? ''); ?></textarea></label>
                            <div class="rf-inline">
                                <button class="rf-btn rf-btn-complete" type="submit">Create</button>
                                <a class="rf-btn rf-btn-ghost" href="view_register.php">Cancel</a>
                            </div>
                            <hr>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">All Registrations</h1>
                        <p class="rf-muted">Showing active registrations (not deleted)</p>
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
                                        <?php
                                        function header_link($key, $label) {
                                            global $sort, $dir;
                                            $next = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
                                            $indicator = ($sort === $key) ? ($dir === 'asc' ? ' ▲' : ' ▼') : '';
                                            $href = '?sort=' . urlencode($key) . '&dir=' . $next;
                                            return '<th><a href="' . htmlspecialchars($href) . '">' . htmlspecialchars($label) . htmlspecialchars($indicator) . '</a></th>';
                                        }
                                        echo header_link('id', '#');
                                        echo header_link('name', 'Name');
                                        echo header_link('email', 'Email / Phone');
                                        echo header_link('workshop_date', 'Workshop Date');
                                        echo header_link('participants', 'Participants');
                                        echo header_link('type', 'Type');
                                        echo header_link('addons', 'Add-ons');
                                        echo header_link('comments', 'Comments');
                                        echo header_link('reg_date', 'Registered At');
                                        ?>
                                        <th class="rf-nowrap">Actions</th>
                                    </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $active_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['registration_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?><br><small class="rf-muted"><?php echo htmlspecialchars($row['street'] . ', ' . $row['city']); ?></small></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['workshop_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['participants']); ?></td>
                                <td><?php echo htmlspecialchars($row['workshop_type']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['addons'])); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['reg_date']); ?></small></td>
                                <td class="rf-nowrap">
                                    <div class="rf-actions">
                                        <!-- View / Edit -->
                                         
                                        <a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['registration_id']); ?>">View</a>
                                        <a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($row['registration_id']); ?>">Edit</a>

                                        <!-- Mark Processed (moves to Completed section) -->
                                        <form method="post" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['registration_id']); ?>">
                                            <input type="hidden" name="action" value="mark_processed">
                                            <button class="rf-btn rf-btn-complete" type="submit">Complete</button>
                                        </form>

                                        <!-- Delete button — sends to recycle.php (soft-delete) -->
                                        <form method="post" action="recycle.php" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="table" value="registrations">
                                            <input type="hidden" name="id" value="<?php echo intval($row['registration_id']); ?>">
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
                    <p>No registrations found.</p>
                <?php endif; ?>
            </div>

            <!-- Completed / Processed registrations (matches enquiry layout) -->
            <div class="rf-panel">
                <div class="rf-meta">
                    <div>
                        <h1 class="rf-h1">Completed Registrations</h1>
                        <p class="rf-muted">Registrations marked completed/processed. From here you can reopen them or move them to the Recycle Bin.</p>
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
                                <?php
                                echo header_link('id', '#');
                                echo header_link('name', 'Name');
                                echo header_link('email', 'Email / Phone');
                                echo header_link('workshop_date', 'Workshop Date');
                                echo header_link('participants', 'Participants');
                                echo header_link('type', 'Type');
                                echo header_link('addons', 'Add-ons');
                                echo header_link('comments', 'Comments');
                                echo header_link('processed_at', 'Completed At');
                                ?>
                                <th class="rf-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $completed_result->fetch_assoc()): ?>
                            <tr class="rf-row-completed">
                                <td><?php echo htmlspecialchars($row['registration_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?><br><small class="rf-muted"><?php echo htmlspecialchars($row['street'] . ', ' . $row['city']); ?></small></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a><br><small class="rf-muted"><?php echo htmlspecialchars($row['phone']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['workshop_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['participants']); ?></td>
                                <td><?php echo htmlspecialchars($row['workshop_type']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['addons'])); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
                                <td><small class="rf-muted"><?php echo htmlspecialchars($row['processed_at']); ?></small></td>
                                <td class="rf-nowrap">
                                    <div class="rf-actions">
                                        
                                        <a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['registration_id']); ?>">View</a>
                                        <a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($row['registration_id']); ?>">Edit</a>
                                        <!-- Mark Open (undo completed) -->
                                        <form method="post" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['registration_id']); ?>">
                                            <input type="hidden" name="action" value="mark_open">
                                            <button class="rf-btn rf-btn-ghost rf-btn-complete" type="submit">Mark Open</button>
                                        </form>

                                        <!-- Move to Recycle (soft-delete) posts to recycle.php -->
                                        <form method="post" action="recycle.php" class="rf-inline">
                                            <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                            <input type="hidden" name="id" value="<?php echo intval($row['registration_id']); ?>">
                                            <input type="hidden" name="action" value="soft_delete">
                                            <input type="hidden" name="table" value="registrations">
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
                    <p>No completed registrations.</p>
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
