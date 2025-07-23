<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT * FROM memories WHERE user_id=$user_id ORDER BY created_at DESC");

date_default_timezone_set('Asia/Manila'); 
$now = date("Y-m-d H:i:s");
?>

<?php include 'header.php'; ?>

<h2>Welcome to your Time Capsule</h2>
<a href="create.php" class="btn">➕ Add New Memory</a>
<a href="logout.php" class="btn">Exit</a>
<br><br>

<?php
if ($res->num_rows == 0) {
    echo "<p>No memories yet.</p>";
}

while ($row = $res->fetch_assoc()) {
    $unlock_datetime = date("Y-m-d H:i:s", strtotime($row['unlock_datetime']));
    $isUnlocked = strtotime($now) >= strtotime($unlock_datetime);

    
    $createdAt = strtotime($row['created_at']);
    $canEdit = (time() - $createdAt) <= 3600;

    echo "<div class='card'>";
    echo "<h3>" . htmlspecialchars($row['title']) . "</h3>";
    echo "<p>" . htmlspecialchars($row['description']) . "</p>";

    if ($isUnlocked && !empty($row['image_path'])) {
        echo "<img src='" . htmlspecialchars($row['image_path']) . "' width='200'>";
    } else {
        echo "<p><em>Locked until {$row['unlock_datetime']}</em></p>";
    }

    echo "<p>";
    if ($canEdit) {
        echo "<a href='edit_memory.php?id={$row['id']}'>✏ Edit</a> | ";
    } else {
        echo "<em>⏳ Edit expired</em> | ";
    }
    echo "<a href='delete_memory.php?id={$row['id']}' onclick=\"return confirm('Are you sure you want to delete this memory?');\">🗑 Delete</a>";
    echo "</p>";
    echo "</div><hr>";
}

?>

<?php include 'footer.php'; ?>
