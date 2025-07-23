<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Fetch memory
$stmt = $conn->prepare("SELECT * FROM memories WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$memory = $stmt->get_result()->fetch_assoc();

if (!$memory) {
    echo "Memory not found.";
    exit();
}

// Time-based restriction: Allow editing only within 60 minutes of creation
date_default_timezone_set('Asia/Manila');
$createdAt = strtotime($memory['created_at']);
$now = time();
$timeElapsed = $now - $createdAt;

if ($timeElapsed > 3600) {
    header("Location: dashboard.php?msg=Edit+time+expired");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_path = $memory['image_path'];

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            if (!empty($memory['image_path']) && file_exists($memory['image_path'])) {
                unlink($memory['image_path']);
            }
            $image_path = $target_file;
        }
    }

    $update = $conn->prepare("UPDATE memories SET title=?, description=?, image_path=? WHERE id=? AND user_id=?");
    $update->bind_param("sssii", $title, $description, $image_path, $id, $user_id);
    $update->execute();

    header("Location: dashboard.php?msg=Memory+updated");
    exit();
}

// Calculate time left
$timeLeft = max(0, 3600 - $timeElapsed);
$minutesLeft = floor($timeLeft / 60);
$secondsLeft = $timeLeft % 60;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Memory</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>

<div class="edit-container">
    <h2>Edit Memory</h2>
    <div id="countdown">⏳ Time left: <?= $minutesLeft ?>m <?= str_pad($secondsLeft, 2, '0', STR_PAD_LEFT) ?>s</div>
    
    <form method="POST" enctype="multipart/form-data">
        <label>Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($memory['title']) ?>" required><br><br>

        <label>Description:</label><br>
        <textarea name="description" rows="5" cols="40" required><?= htmlspecialchars($memory['description']) ?></textarea><br><br>

        <?php if (!empty($memory['image_path'])): ?>
            <p>Current Image:</p>
            <img src="<?= htmlspecialchars($memory['image_path']) ?>" width="150"><br><br>
        <?php endif; ?>

        <p>Upload New Image (optional):</p>
        <input type="file" name="image" accept="image/*"><br><br>

        <button type="submit">Update Memory</button>
        <a href="dashboard.php"><button type="button">Cancel</button></a>
    </form>
</div>

</body>
</html>
