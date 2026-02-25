<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit;
}

$staff_id = (int)($_GET['staff_id'] ?? 0);

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $staff_id = (int)$_POST['staff_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO staff_tasks (staff_id,title,description) VALUES (?,?,?)");
    $stmt->bind_param("iss",$staff_id,$title,$desc);
    $stmt->execute();

    header("Location: staff.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Assign Task</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="card shadow p-4 mx-auto" style="max-width:600px;">
<h4 class="mb-3">➕ Assign Task</h4>

<form method="POST">
<input type="hidden" name="staff_id" value="<?= $staff_id ?>">

<input class="form-control mb-3" name="title" placeholder="Task Title" required>

<textarea class="form-control mb-3" name="description" placeholder="Task Description" rows="4"></textarea>

<button class="btn btn-primary w-100">Assign Task</button>
<a href="staff.php" class="btn btn-secondary w-100 mt-2">Back</a>
</form>

</div>
</div>

</body>
</html>
