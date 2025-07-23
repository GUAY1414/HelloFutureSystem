<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM memories WHERE user_id=$user_id ORDER BY created_at DESC");
?>

<?php include 'header.php'; ?>
<!-- User Memory Time Capsule Section -->
<h2>Welcome to your Time Capsule</h2>
<a href="create.php" class="btn">➕ Add New Memory</a>
<a href="logout.php" class="btn">Exit</a>
<br><br>

<?php
if ($res->num_rows == 0) {
    echo "<p>No memories yet.</p>";
}
while ($row = $res->fetch_assoc()) {
    date_default_timezone_set('Asia/Manila');
    $now = date("Y-m-d H:i:s");
    $unlock_datetime = date("Y-m-d H:i:s", strtotime($row['unlock_datetime']));
    $isUnlocked = strtotime($now) >= strtotime($unlock_datetime);

    echo "<div class='card'>";
    echo "<h3>{$row['title']}</h3>";
    echo "<p>{$row['description']}</p>";
    if ($isUnlocked && !empty($row['image_path'])) {
        echo "<img src='{$row['image_path']}' width='200'>";
    } else {
        echo "<p><em>Locked until {$row['unlock_datetime']}</em></p>";
    }
    echo "</div>";
}
?>

<?php include 'footer.php'; ?>
