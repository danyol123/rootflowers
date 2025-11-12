<?php
session_start();

// Basic admin check - adjust according to your login implementation
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

$sql = "SELECT * FROM enquiry ORDER BY submit_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>View Enquiries</title>
	<link rel="stylesheet" href="styles/styles.css">
</head>
<body>
	<?php include 'navigation.php'; ?>

	<main class="admin-list">
		<section class="list-container">
			<h1>All Enquiries</h1>
			<p>Total: <?php echo $result ? $result->num_rows : 0; ?></p>

			<?php if ($result && $result->num_rows > 0): ?>
				<div class="table-responsive">
				<table class="data-table">
					<thead>
						<tr>
							<th>#</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Type</th>
							<th>Comments</th>
							<th>Submitted At</th>
						</tr>
					</thead>
					<tbody>
					<?php while ($row = $result->fetch_assoc()): ?>
						<tr>
							<td><?php echo htmlspecialchars($row['enquiry_id']); ?></td>
							<td><?php echo htmlspecialchars($row['firstname']); ?></td>
							<td><?php echo htmlspecialchars($row['lastname']); ?></td>
							<td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
							<td><?php echo htmlspecialchars($row['phone']); ?></td>
							<td><?php echo htmlspecialchars($row['enquiry_type']); ?></td>
							<td><?php echo nl2br(htmlspecialchars($row['comments'])); ?></td>
							<td><?php echo htmlspecialchars($row['submit_date']); ?></td>
						</tr>
					<?php endwhile; ?>
					</tbody>
				</table>
				</div>
			<?php else: ?>
				<p>No enquiries found.</p>
			<?php endif; ?>
		</section>
	</main>

	<?php include 'footer.php'; ?>

</body>
</html>

<?php
$conn->close();
?>

