<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}


if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);
    $conn->prepare("DELETE FROM users WHERE id=?")->bind_param("i", $uid)->execute();
    $conn->prepare("DELETE FROM memories WHERE user_id=?")->bind_param("i", $uid)->execute();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $uid = intval($_POST['user_id']);
    $email = $_POST['email'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['password'];


    $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($hashed);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($currentPassword, $hashed)) {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET email=?, password=? WHERE id=?");
        $stmt->bind_param("ssi", $email, $newHash, $uid);
        $stmt->execute();
        $stmt->close();

        header("Location: admin.php?updated=1");
        exit;
    } else {
        $error = "Incorrect current password.";
    }
}

$users = $conn->query("SELECT u.*, COUNT(m.id) as memory_count FROM users u LEFT JOIN memories m ON u.id = m.user_id GROUP BY u.id");
$editing = isset($_GET['edit']) ? intval($_GET['edit']) : null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="container">
    <h2>Admin Panel</h2>
    <a href="logout.php" class="btn-link">Exit</a>

    <?php if (isset($_GET['updated'])): ?>
        <p class="card" style="background:#0a0; color:#fff;">User updated successfully.</p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="card" style="background:#a00; color:#fff;"><?= $error ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Role</th>
            <th>Memories</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['role'] ?></td>
            <td><?= $row['memory_count'] ?></td>
            <td>
                <a href="?delete=<?= $row['id'] ?>" class="btn-link" onclick="return confirm('Delete user?');">Delete</a>
                <a href="?edit=<?= $row['id'] ?>" class="btn-link">Edit</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if ($editing): 
        $user = $conn->query("SELECT * FROM users WHERE id=$editing")->fetch_assoc();
    ?>
    <h3>Edit User #<?= $user['id'] ?></h3>
    <form method="post" class="card" style="max-width: 400px; margin: 2rem auto;">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label>New Password:</label>
        <input type="password" name="password" required>

        <label>Enter Current Password:</label>
        <input type="password" name="current_password" required>

        <div style="margin-top: 1rem;">
            <button type="submit" name="update_user" class="btn">Update</button>
            <a href="admin.php" class="btn" style="background:#a00;">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
