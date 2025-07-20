<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);
    $stmt1 = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt1->bind_param("i", $uid);
    $stmt1->execute();
    $stmt1->close();

    $stmt2 = $conn->prepare("DELETE FROM memories WHERE user_id=?");
    $stmt2->bind_param("i", $uid);
    $stmt2->execute();
    $stmt2->close();
}

$users = $conn->query("SELECT u.*, COUNT(m.id) as memory_count FROM users u LEFT JOIN memories m ON u.id = m.user_id GROUP BY u.id");
?>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Admin Panel</h2>
<a href="logout.php">Exit</a>
<table border="1">
<tr><th>ID</th><th>Email</th><th>Role</th><th>Memories</th><th>Actions</th></tr>
<?php
while ($row = $users->fetch_assoc()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['email']}</td><td>{$row['role']}</td><td>{$row['memory_count']}</td>
    <td><a href='?delete={$row['id']}' onclick=\"return confirm('Delete user?');\">Delete</a></td></tr>";
}
?>
</table>
</body>
</html>
