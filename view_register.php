<?php
session_start();

if (!isset($_SESSION['is_admin']) && (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin')) {
	header('Location: login.php');
	exit();
}

$servername = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbname = 'DB';

$conn = mysqli_connect($servername, $dbuser, $dbpass, $dbname);
if (!$conn) {
	die('Database connection failed: ' . mysqli_connect_error());
}

$allowed_sorts = [
	'id' => 'registration_id',
	'name' => "CONCAT(firstname,' ',lastname)",
	'email' => 'email',
	'phone' => 'phone',
	'workshop_date' => 'workshop_date',
	'participants' => 'participants',
	'type' => 'workshop_type',
	'addons' => 'addons',
	'reg_date' => 'reg_date'
];

$sort = isset($_GET['sort']) ? $_GET['sort'] : 'reg_date';
$dir = (isset($_GET['dir']) && strtolower($_GET['dir']) === 'asc') ? 'asc' : 'desc';

// resolve column safely
$order_by = isset($allowed_sorts[$sort]) ? $allowed_sorts[$sort] : $allowed_sorts['reg_date'];

$sql = "SELECT * FROM registrations ORDER BY $order_by " . ($dir === 'asc' ? 'ASC' : 'DESC');
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin — Workshop Registrations</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>

<main class="admin-main">
	<!-- Sidebar -->
    <?php include 'admin_sidebar.php'; ?>

	<!-- Content area: table lives here -->
	<div class="admin-content">
		<section class="list-container">
			<h1>Workshop Registrations</h1>
			<p>Total: <?php echo $result ? $result->num_rows : 0; ?></p>

			<?php if ($result && $result->num_rows > 0): ?>
				<div class="table-responsive">
				<table class="data-table" id="registrations-table">
					<thead>
						<tr>
							<?php
							// helper to build header links that toggle sort direction
							function header_link($key, $label) {
								global $sort, $dir;
								$next = ($sort === $key && $dir === 'asc') ? 'desc' : 'asc';
								$indicator = ($sort === $key) ? ($dir === 'asc' ? '▲' : '▼') : '';
								$href = '?sort=' . urlencode($key) . '&dir=' . $next;
								return '<th class="sortable"><a href="' . htmlspecialchars($href) . '">' . htmlspecialchars($label) . ' <span class="sort-indicator">' . $indicator . '</span></a></th>';
							}
							echo header_link('id', '#');
							echo header_link('name', 'Name');
							echo header_link('email', 'Email');
							echo header_link('phone', 'Phone');
							echo header_link('workshop_date', 'Workshop Date');
							echo header_link('participants', 'Participants');
							echo header_link('type', 'Type');
							echo header_link('addons', 'Add-ons');
							?>
							<th>Comments</th>
							<?php echo header_link('reg_date', 'Registered At'); ?>
						</tr>
					</thead>
					<tbody>
					<?php while ($row = $result->fetch_assoc()): ?>
						<tr>
							<td><?php echo htmlspecialchars($row['registration_id']); ?></td>
							<td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
							<td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
							<td><?php echo htmlspecialchars($row['phone']); ?></td>
							<td><?php echo htmlspecialchars($row['workshop_date']); ?></td>
							<td><?php echo htmlspecialchars($row['participants']); ?></td>
							<td><?php echo htmlspecialchars($row['workshop_type']); ?></td>
							<td><?php echo nl2br(htmlspecialchars($row['addons'])); ?></td>
							<td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
							<td><?php echo htmlspecialchars($row['reg_date']); ?></td>
						</tr>
					<?php endwhile; ?>
					</tbody>
				</table>
				</div>
			<?php else: ?>
				<p>No registrations found.</p>
			<?php endif; ?>
		</section>
	</div>
</main>

</body>
</html>

<?php
$conn->close();
?>


