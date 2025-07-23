<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<?php include 'header.php'; ?>
<h2>My Profile</h2>
<form action="update_profile.php" method="POST">
    <input type="hidden" name="id" value="<?= $id ?>">
    Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br><br>
    New Password: <input type="password" name="password" placeholder="Leave blank to keep current"><br><br>
    <button type="submit">Update Profile</button>
</form>
<?php include 'footer.php'; ?>
