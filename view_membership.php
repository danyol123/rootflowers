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

$sql = "SELECT member_id, firstname, lastname, email, loginid, reg_date FROM memberships ORDER BY reg_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>View Memberships</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<?php include 'navigation.php'; ?>

	<main class="admin-list">
		<section class="list-container">
			<h1>Membership Registrations</h1>
			<p>Total: <?php echo $result ? $result->num_rows : 0; ?></p>

			<?php if ($result && $result->num_rows > 0): ?>
				<div class="table-responsive">
				<table class="data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Name</th>
							<th>Email</th>
							<th>Login ID</th>
							<th>Registered At</th>
						</tr>
					</thead>
					<tbody>
					<?php while ($row = $result->fetch_assoc()): ?>
						<tr>
							<td><?php echo htmlspecialchars($row['member_id']); ?></td>
							<td><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname']); ?></td>
							<td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
							<td><?php echo htmlspecialchars($row['loginid']); ?></td>
							<td><?php echo htmlspecialchars($row['reg_date']); ?></td>
						</tr>
					<?php endwhile; ?>
					</tbody>
				</table>
				</div>
			<?php else: ?>
				<p>No membership registrations found.</p>
			<?php endif; ?>
		</section>
	</main>

	<?php include 'footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>

