<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$id = (int)$_GET['id'];

/* FETCH STAFF */
$stmt = $conn->prepare("SELECT * FROM staff WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

if (!$staff) {
    die("Staff not found");
}

/* UPDATE STAFF */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    $stmt = $conn->prepare(
        "UPDATE staff SET full_name=?, username=?, email=?, role=?, status=? WHERE id=?"
    );
    $stmt->bind_param("sssssi", $name, $username, $email, $role, $status, $id);
    $stmt->execute();

    header("Location: staff.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Staff</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">✏️ Edit Staff</span>
    <a href="staff.php" class="btn btn-outline-light btn-sm">⬅ Back</a>
</nav>

<div class="container my-5">
<div class="card shadow p-4 mx-auto" style="max-width:500px;">

<form method="POST">

<input class="form-control mb-3" name="full_name"
value="<?= htmlspecialchars($staff['full_name']) ?>" required>

<input class="form-control mb-3" name="username"
value="<?= htmlspecialchars($staff['username']) ?>" required>

<input class="form-control mb-3" type="email" name="email"
value="<?= htmlspecialchars($staff['email']) ?>" required>

<select class="form-control mb-3" name="role" required>
<option <?= $staff['role']=='HR'?'selected':'' ?>>HR</option>
<option <?= $staff['role']=='Recruiter'?'selected':'' ?>>Recruiter</option>
<option <?= $staff['role']=='Manager'?'selected':'' ?>>Manager</option>
</select>

<select class="form-control mb-4" name="status">
<option <?= $staff['status']=='Active'?'selected':'' ?>>Active</option>
<option <?= $staff['status']=='Inactive'?'selected':'' ?>>Inactive</option>
</select>

<button class="btn btn-warning w-100">Update Staff</button>

</form>

</div>
</div>

</body>
</html>
