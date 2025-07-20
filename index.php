<?php
session_start();
include 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: dashboard.php");
    }
    exit();
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (isset($_POST['register'])) {
        // Determine role
        $role = (substr_compare($email, '@admin.com', -strlen('@admin.com')) === 0) ? 'admin' : 'user';

        // Check if user already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            // Hash the password before storing
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $hashed_password, $role);
            $stmt->execute();
            // Redirect to login page after registration (do not set session here)
            header("Location: index.php?registered=1");
            exit();
            // Option 2: Auto-login after registration (uncomment below and comment out above to use)
            /*
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['role'] = $role;
            if ($role === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
            */
        }
    }

    if (isset($_POST['login'])) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            if ($user['role'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $message = "Invalid credentials.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login / Register - Hello Future</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Hello, Future!</h2>
    <p style="color: red;"><?= $message ?></p>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter email" required><br>
        <input type="password" name="password" placeholder="Enter password" required><br>
        <button type="submit" name="login">Login</button>
        <button type="submit" name="register">Register</button>
    </form>
</body>
</html>