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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image_path = $memory['image_path']; // Keep current image by default

    // Check if a new image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target_file = $target_dir . $image_name;

        // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Delete old image if exists
            if (!empty($memory['image_path']) && file_exists($memory['image_path'])) {
                unlink($memory['image_path']);
            }
            $image_path = $target_file; // Update with new path
        }
    }

    // Update memory with new data
    $update = $conn->prepare("UPDATE memories SET title=?, description=?, image_path=? WHERE id=? AND user_id=?");
    $update->bind_param("sssii", $title, $description, $image_path, $id, $user_id);
    $update->execute();

    header("Location: dashboard.php?msg=Memory updated");
    exit();
}
?>

<h2>Edit Memory</h2>
<form method="POST" enctype="multipart/form-data">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($memory['title']) ?>" required><br><br>
    Description:<br>
    <textarea name="description" rows="5" cols="40" required><?= htmlspecialchars($memory['description']) ?></textarea><br><br>

    <?php if (!empty($memory['image_path'])): ?>
        <p>Current Image:</p>
        <img src="<?= htmlspecialchars($memory['image_path']) ?>" width="150"><br><br>
    <?php endif; ?>

    <p>Upload New Image (optional):</p>
    <input type="file" name="image" accept="image/*"><br><br>

    <button type="submit">Update Memory</button>
    <a href="dashboard.php" style="margin-left: 10px;">
        <button type="button">Cancel</button>
    </a>
</form>
