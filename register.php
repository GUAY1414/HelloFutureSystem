<?php
$message = "";
if (isset($_GET['error'])) {
    $message = $_GET['error'];
}
?>

<?php include 'header.php'; ?>

<h3>Register</h3>
<p style="color: red;"><?= htmlspecialchars($message) ?></p>
<form method="POST" action="register_submit.php">
    <input type="email" name="email" placeholder="Enter email" required><br>
    <input type="password" name="password" placeholder="Enter password" required><br>
    <button type="submit" name="register">Register</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a></p>

<?php include 'footer.php'; ?>
