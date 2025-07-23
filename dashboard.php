<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM memories WHERE user_id=$user_id ORDER BY created_at DESC");
?>
<html>
<head>
  <title>User Dashboard</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Welcome to your Time Capsule</h2>
<a href="create.php">➕ Add New Memory</a> | <a href="logout.php">Exit</a><br><br>
<?php
if ($res->num_rows == 0) {
    echo "<p>No memories yet.</p>";
}

date_default_timezone_set('Asia/Manila'); // Set timezone
$now = date("Y-m-d H:i:s");

while ($row = $res->fetch_assoc()) {
    $unlock_datetime = date("Y-m-d H:i:s", strtotime($row['unlock_datetime']));
    $isUnlocked = strtotime($now) >= strtotime($unlock_datetime);

    echo "<div>";
    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
    echo "<p>" . htmlspecialchars($row['description']) . "</p>";

    if ($isUnlocked && !empty($row['image_path'])) {
        echo "<img src='" . htmlspecialchars($row['image_path']) . "' width='200'>";
    } else {
        echo "<p><em>Locked until {$row['unlock_datetime']}</em></p>";
    }

    // ✅ Add Edit and Delete buttons
    echo "<p>
            <a href='edit_memory.php?id={$row['id']}'>✏ Edit</a> | 
            <a href='delete_memory.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this memory?');\">🗑 Delete</a>
          </p>";

    echo "</div><hr>";
}
?>
</body>
</html>
