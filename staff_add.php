<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare(
        "INSERT INTO staff (full_name, username, email, password, role, status)
         VALUES (?,?,?,?,?,?)"
    );
    $stmt->bind_param("ssssss", $name, $username, $email, $password, $role, $status);
    $stmt->execute();

    header("Location: staff.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Staff</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">➕ Add Staff</span>
    <a href="staff.php" class="btn btn-outline-light btn-sm">⬅ Back</a>
</nav>

<div class="container my-5">
<div class="card shadow p-4 mx-auto" style="max-width:500px;">

<form method="POST">
<input class="form-control mb-3" name="full_name" placeholder="Full Name" required>
<input class="form-control mb-3" name="username" placeholder="Username" required>
<input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>

<select class="form-control mb-3" name="role" required>
<option value="">Select Role</option>
<option value="HR">HR</option>
<option value="Recruiter">Recruiter</option>
<option value="Manager">Manager</option>
</select>

<select class="form-control mb-4" name="status">
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>

<button class="btn btn-success w-100">Add Staff</button>
</form>

</div>
</div>

</body>
</html>
