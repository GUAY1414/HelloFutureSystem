<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hello, Future!</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 class="typewriter">HELLO, FUTURE!</h1>
        <div class="terminal-effect">
            <p>> This is your digital time capsule. <br>
            > Save memories. Unlock them in the future. <br>
            > A retro-futuristic vault just for you.
            </p>
        </div>

        <form action="login_submit.php" method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>

            <button type="submit" class="btn">Login</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register Here</a></p>
    </div>

    <div class="footer">
        <p>⏳ Hello, Future! | Retro-Tech Edition</p>
    </div>
</body>
</html>
