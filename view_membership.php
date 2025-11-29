<?php
/*
 * File: view_membership.php
 * Description: Admin page for managing memberships (server-side create/edit/delete and sorting).
 * Author: Root Flower Team
 * Created: 2025-11-29
 */
session_start();

// Basic admin check
if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin')) {
	header('Location: login.php');
	exit();
}

// CSRF token
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
// create error container
$create_errors = [];
$errors = [];

// Ensure necessary columns exist (for quick setup; in production use migrations)
$alter_sql = "ALTER TABLE `memberships` 
	ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
	ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
	ADD COLUMN IF NOT EXISTS `processed` TINYINT(1) NOT NULL DEFAULT 0,
	ADD COLUMN IF NOT EXISTS `processed_at` DATETIME NULL DEFAULT NULL";
@mysqli_query($conn, $alter_sql);

// Handle POST actions for memberships (mark processed/mark open, update, create)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
		die('Invalid CSRF token');
	}
	if (isset($_POST['action']) && (isset($_POST['id']) || $_POST['action'] === 'create')) {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		if ($_POST['action'] === 'create') $id = 0;
		$shouldRedirect = true;
		$id = intval($_POST['id']);
		switch ($_POST['action']) {
			case 'mark_processed':
				$stmt = $conn->prepare("UPDATE memberships SET processed = 1, processed_at = NOW() WHERE member_id = ?");
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$stmt->close();
			break;
			case 'mark_open':
				$stmt = $conn->prepare("UPDATE memberships SET processed = 0, processed_at = NULL WHERE member_id = ?");
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$stmt->close();
			break;
			case 'toggle_processed':
				$stmt = $conn->prepare("SELECT processed FROM memberships WHERE member_id = ? LIMIT 1");
				$stmt->bind_param('i', $id);
				$stmt->execute();
				$stmt->bind_result($cur);
				if ($stmt->fetch()) {
					$stmt->close();
					$new = $cur ? 0 : 1;
					if ($new) {
						$stmt2 = $conn->prepare("UPDATE memberships SET processed = 1, processed_at = NOW() WHERE member_id = ?");
					} else {
						$stmt2 = $conn->prepare("UPDATE memberships SET processed = 0, processed_at = NULL WHERE member_id = ?");
					}
					$stmt2->bind_param('i', $id);
					$stmt2->execute();
					$stmt2->close();
				} else {
					$stmt->close();
				}
				break;
			case 'update':
				$firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
				$lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				$email = isset($_POST['email']) ? trim($_POST['email']) : '';
				$password = isset($_POST['password']) ? trim($_POST['password']) : '';
				// Validate password complexity if provided
				if ($password !== '') {
					if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || strlen($password) < 8) {
						$create_errors[] = 'Password must be at least 8 characters long and contain both letters and numbers.';
						$shouldRedirect = false;
						// fetch modal row to show in form
						$stmt = $conn->prepare('SELECT * FROM memberships WHERE member_id = ? LIMIT 1');
						$stmt->bind_param('i', $id);
						$stmt->execute();
						$res = $stmt->get_result();
						if ($res && $res->num_rows > 0) $modal_row = $res->fetch_assoc();
						$stmt->close();
						break;
					}
				}
				if ($password !== '') {
					$password_hash = password_hash($password, PASSWORD_DEFAULT);
					$stmt = $conn->prepare("UPDATE memberships SET firstname=?, lastname=?, username=?, email=?, password_hash=? WHERE member_id=?");
					$stmt->bind_param('sssssi', $firstname, $lastname, $username, $email, $password_hash, $id);
				} else {
					$stmt = $conn->prepare("UPDATE memberships SET firstname=?, lastname=?, username=?, email=? WHERE member_id=?");
					$stmt->bind_param('ssssi', $firstname, $lastname, $username, $email, $id);
				}
				$stmt->execute();
				$stmt->close();
				break;
			case 'create':
				$firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
				$lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
				$username = isset($_POST['username']) ? trim($_POST['username']) : '';
				$email = isset($_POST['email']) ? trim($_POST['email']) : '';
				$password = isset($_POST['password']) ? trim($_POST['password']) : '';
				$create_errors = [];
				if ($firstname === '') $create_errors[] = 'First name is required.';
				if ($lastname === '') $create_errors[] = 'Last name is required.';
				if ($username === '') $create_errors[] = 'Username is required.';
				if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $create_errors[] = 'Valid email is required.';
				if ($password === '') $create_errors[] = 'Password is required.';
				// Password complexity enforcement: require at least 8 chars, letters and numbers
				if ($password !== '' && (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password) || strlen($password) < 8)) {
					$create_errors[] = 'Password must be at least 8 characters long and contain both letters and numbers.';
				}
				if (empty($create_errors)) {
					$password_hash = password_hash($password, PASSWORD_DEFAULT);
					$stmt = $conn->prepare("INSERT INTO memberships (firstname, lastname, username, email, password_hash) VALUES (?,?,?,?,?)");
					$stmt->bind_param('sssss', $firstname, $lastname, $username, $email, $password_hash);
					$stmt->execute();
					$stmt->close();
					$_SESSION['flash'] = 'Membership created successfully.';
				} else {
					$modal_row = ['firstname'=>$firstname,'lastname'=>$lastname,'username'=>$username,'email'=>$email];
					$create_mode = true;
					$shouldRedirect = false;
				}
				break;
		}
	}
	// Redirect to avoid form re-submission
	if ($shouldRedirect) {
		header('Location: ' . $_SERVER['PHP_SELF']);
		exit();
	}
}

// Fetch active memberships (not deleted and not processed)
$allowed_sorts = [
	'id' => 'member_id',
	'name' => "CONCAT(firstname, ' ', lastname)",
	'email' => 'email',
	'username' => 'username',
	'reg_date' => 'reg_date',
	'processed_at' => 'processed_at'
];
$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $allowed_sorts) ? $_GET['sort'] : 'reg_date';
$dir = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'asc' : 'desc';
$order_by = $allowed_sorts[$sort];
$order_clause = "ORDER BY $order_by " . ($dir === 'asc' ? 'ASC' : 'DESC');

$active_sql = "SELECT * FROM memberships WHERE deleted = 0 AND processed = 0 " . $order_clause;
$active_result = $conn->query($active_sql);

// Fetch processed memberships (not deleted and processed)
$completed_sql = "SELECT * FROM memberships WHERE deleted = 0 AND processed = 1 " . $order_clause;
$completed_result = $conn->query($completed_sql);

// GET-based identifiers for the modal-driven UI
$view_id = isset($_GET['view']) ? intval($_GET['view']) : 0;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$create_mode = isset($_GET['create']) ? true : false;
$modal_row = null;
if ($view_id || $edit_id) {
	$mid = $view_id ?: $edit_id;
	if ($mid > 0) {
		$stmt = $conn->prepare('SELECT * FROM memberships WHERE member_id = ? LIMIT 1');
		$stmt->bind_param('i', $mid);
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
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Root Flower — Memberships</title>
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
				<?php if ($modal_row || $create_mode): ?>
				<div class="rf-modal-inline">
					<?php if ($view_id): ?>
						<h2>View Membership #<?php echo htmlspecialchars($modal_row['member_id']); ?></h2>
						<p><strong>Name:</strong> <?php echo htmlspecialchars($modal_row['firstname'] . ' ' . $modal_row['lastname']); ?></p>
						<p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($modal_row['email']); ?>"><?php echo htmlspecialchars($modal_row['email']); ?></a></p>
						<p><strong>Username:</strong> <?php echo htmlspecialchars($modal_row['username']); ?></p>
						<p><a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($modal_row['member_id']); ?>">Edit</a> <a class="rf-btn rf-btn-ghost" href="view_membership.php">Close</a></p>
						<hr>
					<?php elseif ($edit_id): ?>
						<h2>Edit Membership #<?php echo htmlspecialchars($modal_row['member_id']); ?></h2>
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
							<input type="hidden" name="id" value="<?php echo intval($modal_row['member_id']); ?>">
							<input type="hidden" name="action" value="update">
							<label>First name<input type="text" name="firstname" value="<?php echo htmlspecialchars($modal_row['firstname']); ?>"></label>
							<label>Last name<input type="text" name="lastname" value="<?php echo htmlspecialchars($modal_row['lastname']); ?>"></label>
							<label>Username<input type="text" name="username" value="<?php echo htmlspecialchars($modal_row['username']); ?>"></label>
							<label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($modal_row['email']); ?>"></label>
							<label>New Password (leave blank to keep current)<input type="password" name="password" value=""></label>
							<div class="rf-inline">
								<button class="rf-btn rf-btn-complete" type="submit">Save</button>
								<a class="rf-btn rf-btn-ghost" href="view_membership.php">Cancel</a>
							</div>
							<?php if (!empty($flash)): ?>
								<div class="rf-alert rf-alert-success">
									<?php echo htmlspecialchars($flash); ?>
								</div>
							<?php endif; ?>
						</form>
						<hr>
					<?php elseif ($create_mode): ?>
						<h2>Create Membership</h2>
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
							<label>Username<input type="text" name="username" value="<?php echo htmlspecialchars($modal_row['username'] ?? ''); ?>"></label>
							<label>Email<input type="email" name="email" value="<?php echo htmlspecialchars($modal_row['email'] ?? ''); ?>"></label>
							<label>Password<input type="password" name="password" value=""></label>
							<div class="rf-inline">
								<button class="rf-btn rf-btn-complete" type="submit">Create</button>
								<a class="rf-btn rf-btn-ghost" href="view_membership.php">Cancel</a>
							</div>
							<hr>
						</form>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<div class="rf-meta">
					<div>
						<h1 class="rf-h1">All Memberships</h1>
						<p class="rf-muted">Active memberships (not deleted and not processed)</p>
					</div>
					<div class="rf-nowrap">
						<small class="rf-muted">Total active: <?php echo $active_result ? $active_result->num_rows : 0; ?></small>
						<a class="rf-btn rf-btn-ghost rf-btn-ml" href="?create=1">Create</a>
					</div>
				</div>

				<?php if ($active_result && $active_result->num_rows > 0): ?>
				<div class="rf-table-responsive">
					<table class="rf-data-table" role="table">
						<thead>
							<tr>
								<?php echo header_link('id', '#'); ?>
								<?php echo header_link('name', 'Name'); ?>
								<?php echo header_link('email', 'Email'); ?>
								<?php echo header_link('username', 'Username'); ?>
								<?php echo header_link('reg_date', 'Registered At'); ?>
								<th class="rf-nowrap">Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php while ($row = $active_result->fetch_assoc()): ?>
							<tr>
								<td><?php echo htmlspecialchars($row['member_id']); ?></td>
								<td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
								<td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
								<td><?php echo htmlspecialchars($row['username']); ?></td>
								<td><small class="rf-muted"><?php echo htmlspecialchars($row['reg_date']); ?></small></td>
								<td class="rf-nowrap">
									<div class="rf-actions">
										<a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['member_id']); ?>">View</a>
										<a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($row['member_id']); ?>">Edit</a>
										<!-- Mark Processed/Activated -->
										<form method="post" class="rf-inline">
											<input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
											<input type="hidden" name="id" value="<?php echo intval($row['member_id']); ?>">
											<input type="hidden" name="action" value="mark_processed">
											<button class="rf-btn rf-btn-complete" type="submit">Mark Processed</button>
										</form>
										<!-- Delete -> send to recycle.php -->
										<form method="post" action="recycle.php" class="rf-inline">
											<input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
											<input type="hidden" name="table" value="memberships">
											<input type="hidden" name="id" value="<?php echo intval($row['member_id']); ?>">
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
					<p>No memberships registered.</p>
				<?php endif; ?>
			</div>

			<!-- Completed memberships -->
			<div class="rf-panel">
				<div class="rf-meta">
					<div>
						<h1 class="rf-h1">Processed Memberships</h1>
						<p class="rf-muted">Memberships marked processed. You can reopen or move to Recycle Bin.</p>
					</div>
					<div class="rf-nowrap">
						<small class="rf-muted">Total processed: <?php echo $completed_result ? $completed_result->num_rows : 0; ?></small>
					</div>
				</div>

				<?php if ($completed_result && $completed_result->num_rows > 0): ?>
				<div class="rf-table-responsive">
					<table class="rf-data-table" role="table">
						<thead>
							<tr>
								<?php echo header_link('id', '#'); ?>
								<?php echo header_link('name', 'Name'); ?>
								<?php echo header_link('email', 'Email'); ?>
								<?php echo header_link('username', 'Username'); ?>
								<?php echo header_link('processed_at', 'Processed At'); ?>
								<th class="rf-nowrap">Actions</th>
							</tr>
						</thead>
						<tbody>
						<?php while ($row = $completed_result->fetch_assoc()): ?>
							<tr class="rf-row-completed">
								<td><?php echo htmlspecialchars($row['member_id']); ?></td>
								<td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
								<td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
								<td><?php echo htmlspecialchars($row['username']); ?></td>
								<td><small class="rf-muted"><?php echo htmlspecialchars($row['processed_at']); ?></small></td>
								<td class="rf-nowrap">
									<div class="rf-actions">
										<a class="rf-btn rf-btn-ghost rf-btn-view" href="?view=<?php echo intval($row['member_id']); ?>">View</a>
										<a class="rf-btn rf-btn-ghost rf-btn-edit" href="?edit=<?php echo intval($row['member_id']); ?>">Edit</a>
										<!-- Mark Open -->
										<form method="post" class="rf-inline">
											<input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
											<input type="hidden" name="id" value="<?php echo intval($row['member_id']); ?>">
											<input type="hidden" name="action" value="mark_open">
											<button class="rf-btn rf-btn-ghost rf-btn-complete" type="submit">Mark Open</button>
										</form>
										<!-- Move to Recycle -->
										<form method="post" action="recycle.php" class="rf-inline">
											<input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
											<input type="hidden" name="table" value="memberships">
											<input type="hidden" name="id" value="<?php echo intval($row['member_id']); ?>">
											<input type="hidden" name="action" value="soft_delete">
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
					<p>No processed memberships.</p>
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

