<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') header("Location: index.php");
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
if ($res->num_rows == 0) echo "<p>No memories yet.</p>";
while ($row = $res->fetch_assoc()) {
    date_default_timezone_set('Asia/Manila'); // Set to your timezone
    $now = date("Y-m-d H:i:s");
    $unlock_datetime = date("Y-m-d H:i:s", strtotime($row['unlock_datetime']));
    $isUnlocked = strtotime($now) >= strtotime($unlock_datetime);
    echo "<div><h3>{$row['title']}</h3>";
    echo "<p>{$row['description']}</p>";
    if ($isUnlocked && !empty($row['image_path'])) {
        echo "<img src='{$row['image_path']}' width='200'>";
    } else {
        echo "<p><em>Locked until {$row['unlock_datetime']}</em></p>";
    }
    echo "</div><hr>";
}
?>
</body>
</html>