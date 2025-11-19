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

$sql = "SELECT * FROM registrations ORDER BY reg_date DESC";
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
				<table class="data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Workshop Date</th>
							<th>Participants</th>
							<th>Type</th>
							<th>Add-ons</th>
							<th>Comments</th>
							<th>Registered At</th>
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


