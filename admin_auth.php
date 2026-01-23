<?php
session_start();
include "db.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$stmt = $conn->prepare("SELECT password FROM admin_users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit;
    }
}

$_SESSION['login_error'] = "❌ Invalid Username or Password";
header("Location: admin_login.php");
exit;
