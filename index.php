<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin.php' : 'dashboard.php'));
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hello Future - Welcome</title>
</head>
<body>
    <h1 style="text-align:center;">🚀 Welcome to Hello Future!</h1>

    <p style="text-align:center; font-size: 18px;">
        Where the future begins — today!
    </p>

    <p style="text-align:center; max-width: 600px; margin: 0 auto;">
        <strong>Hello Future</strong> is a simple and secure login and registration system made to guide users and admins into the future of streamlined access.
        Whether you're managing behind the scenes or just getting started, we’ve built a space for you.
    </p>

    <p style="text-align:center; font-weight: bold; font-size: 16px; margin-top: 30px;">
        🔐 Ready to begin?
    </p>

    <p style="text-align:center;">
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    </p>

    <p style="text-align:center; margin-top: 40px; font-size: 14px; color: gray;">
        &copy; <?= date("Y") ?> Hello Future. All rights reserved.
    </p>
</body>
</html>
