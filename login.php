<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? "admin.php" : "dashboard.php"));
    exit();
}

$message = "";
if (isset($_GET['error'])) {
    $message = $_GET['error'];
} elseif (isset($_GET['registered'])) {
    $message = "Registration successful. You can now log in.";
}
?>

<?php include 'header.php'; ?>

<h3>Login</h3>
<p style="color: red;"><?= htmlspecialchars($message) ?></p>
<form method="POST" action="login_submit.php">
    <input type="email" name="email" placeholder="Enter email" required><br>
    <input type="password" name="password" placeholder="Enter password" required><br>
    <button type="submit" name="login">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register here</a></p>

<?php include 'footer.php'; ?>
