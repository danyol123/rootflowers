<?php
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

// Ensure necessary columns exist (for quick setup; in production use migrations)
$alter_sql = "ALTER TABLE `memberships` 
	ADD COLUMN IF NOT EXISTS `deleted` TINYINT(1) NOT NULL DEFAULT 0,
	ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
	ADD COLUMN IF NOT EXISTS `processed` TINYINT(1) NOT NULL DEFAULT 0,
	ADD COLUMN IF NOT EXISTS `processed_at` DATETIME NULL DEFAULT NULL";
@mysqli_query($conn, $alter_sql);

// Handle POST actions for memberships (mark processed/mark open)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!isset($_POST['csrf']) || $_POST['csrf'] !== $csrf) {
		die('Invalid CSRF token');
	}
	if (isset($_POST['action']) && isset($_POST['id'])) {
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
		}
	}
	// Redirect to avoid form re-submission
	header('Location: ' . $_SERVER['PHP_SELF']);
	exit();
}

// Fetch active memberships (not deleted and not processed)
$active_sql = "SELECT member_id, firstname, lastname, email, username, reg_date FROM memberships WHERE deleted = 0 AND processed = 0 ORDER BY reg_date DESC";
$active_result = $conn->query($active_sql);

// Fetch processed memberships (not deleted and processed)
$completed_sql = "SELECT member_id, firstname, lastname, email, username, processed_at FROM memberships WHERE deleted = 0 AND processed = 1 ORDER BY processed_at DESC";
$completed_result = $conn->query($completed_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Memberships — Admin</title>
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
				<div class="rf-meta">
					<div>
						<h1 class="rf-h1">All Memberships</h1>
						<p class="rf-muted">Active memberships (not deleted and not processed)</p>
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
								<th>Email</th>
								<th>Username</th>
								<th>Registered At</th>
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
										<!-- Mark Processed/Activated -->
										<form method="post" class="rf-inline" style="display:inline-block; margin-right:.35rem">
											<input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
											<input type="hidden" name="id" value="<?php echo intval($row['member_id']); ?>">
											<input type="hidden" name="action" value="mark_processed">
											<button class="rf-btn rf-btn-complete" type="submit">Mark Processed</button>
										</form>
										<!-- Delete -> send to recycle.php -->
										<form method="post" action="recycle.php" class="rf-inline" onsubmit="return confirm('Move this membership to Recycle Bin?');" style="display:inline-block">
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
								<th>#</th>
								<th>Name</th>
								<th>Email</th>
								<th>Username</th>
								<th>Processed At</th>
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
										<!-- Mark Open -->
										<form method="post" class="rf-inline" style="display:inline-block; margin-right:.35rem">
											<input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
											<input type="hidden" name="id" value="<?php echo intval($row['member_id']); ?>">
											<input type="hidden" name="action" value="mark_open">
											<button class="rf-btn rf-btn-ghost" type="submit">Mark Open</button>
										</form>
										<!-- Move to Recycle -->
										<form method="post" action="recycle.php" class="rf-inline" onsubmit="return confirm('Move this membership to Recycle Bin?');" style="display:inline-block">
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

