<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT * FROM memories WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$memory = $stmt->get_result()->fetch_assoc();

if (!$memory) {
    echo "Memory not found.";
    exit();
}


date_default_timezone_set('Asia/Manila');
$createdAt = strtotime($memory['created_at']);
$now = time();
$timeElapsed = $now - $createdAt;

if ($timeElapsed > 3600) {
    header("Location: dashboard.php?msg=Edit+time+expired");
    exit();
}


$timeLeft = max(0, 3600 - $timeElapsed);

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
    <div id="countdown"></div> 
    
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

<script>
    let timeLeft = <?= $timeLeft ?>; 

    const countdownEl = document.getElementById("countdown");

    const countdown = setInterval(() => {
        if (timeLeft <= 0) {
            countdownEl.innerText = "⏳ Edit time expired.";
            const form = document.querySelector("form");
            if (form) form.style.display = "none";

            clearInterval(countdown);
            setTimeout(() => {
                window.location.href = "dashboard.php?msg=Edit+time+expired";
            }, 3000);
            return;
        }

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        countdownEl.innerText = `⏳ Time left: ${minutes}m ${seconds < 10 ? '0' : ''}${seconds}s`;
        timeLeft--;
    }, 1000);
</script>

</body>
</html>
