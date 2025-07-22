<?php
session_start();
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $role = (substr_compare($email, '@admin.com', -strlen('@admin.com')) === 0) ? 'admin' : 'user';

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();
    if ($result->num_rows > 0) {
        header("Location: register.php?error=Email already registered.");
        exit();
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashed_password, $role);
        $stmt->execute();
        header("Location: index.php?registered=1");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
