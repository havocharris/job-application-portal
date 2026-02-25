<?php
session_start();
require_once "db.php";

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$stmt = $conn->prepare("SELECT id, full_name, password, status FROM staff WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1){
    $staff = $result->fetch_assoc();

    if($staff['status'] !== 'Active'){
        $_SESSION['staff_error'] = "Your account is inactive!";
        header("Location: staff_login.php");
        exit;
    }

    if(password_verify($password, $staff['password'])){
        $_SESSION['staff_id'] = $staff['id'];
        $_SESSION['staff_name'] = $staff['full_name'];
        header("Location: staff_dashboard.php");
        exit;
    }
}

// Invalid login
$_SESSION['staff_error'] = "Invalid username or password";
header("Location: staff_login.php");
exit;
