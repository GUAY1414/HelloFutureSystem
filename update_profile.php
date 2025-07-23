<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_POST['id'];
$email = $_POST['email'];
$password = $_POST['password'];

if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET email=?, password=? WHERE id=?");
    $stmt->bind_param("ssi", $email, $hashed, $id);
} else {
    $stmt = $conn->prepare("UPDATE users SET email=? WHERE id=?");
    $stmt->bind_param("si", $email, $id);
}

$stmt->execute();
header("Location: profile.php?msg=Profile updated");
exit();
?>
