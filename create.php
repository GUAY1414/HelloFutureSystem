<?php
session_start();
include 'config/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') header("Location: index.php");
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $unlock = $_POST['unlock_datetime'];
    $user_id = $_SESSION['user_id'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'uploads/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $target = $targetDir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            if (isset($conn)) {
                $stmt = $conn->prepare("INSERT INTO memories (user_id, title, description, unlock_datetime, image_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $user_id, $title, $desc, $unlock, $target);
                $stmt->execute();
                $stmt->close();
                $msg = "Memory added!";
            } else {
                $msg = "Database connection error.";
            }
        } else {
            $msg = "Failed to upload image.";
        }
    } else if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $msg = "Image upload error: " . $_FILES['image']['error'];
    }
}
?>
<html>
<head>
<title>Create Memory</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Create a Memory</h2>
<p><?= $msg ?></p>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Title" required><br>
    <textarea name="description" placeholder="Description" required></textarea><br>
    <label>Unlock Date & Time:</label><br>
    <input type="datetime-local" name="unlock_datetime" required><br>
    <label>Image:</label><br>
    <input type="file" name="image" accept="image/*" required><br>
    <button type="submit">Post</button>
</form>
<a href="dashboard.php">🏠 Dashboard</a>
</body>
</html>