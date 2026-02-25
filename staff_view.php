<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM staff WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

if (!$staff) {
    die("Staff not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Details</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    background: linear-gradient(135deg, #a9b8ff, #000000);
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding-top: 50px;
}

/* Navbar */
.navbar {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    box-shadow: 0 4px 15px rgba(0,0,0,0.25);
}
.navbar .btn-outline-light {
    transition: all 0.3s ease;
}
.navbar .btn-outline-light:hover {
    background: #fff;
    color: #3b82f6;
    font-weight: 600;
}

/* Card */
.card {
    background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(240, 248, 255,0.85));
    border-radius: 20px;
    padding: 30px 40px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    width: 100%;
    max-width: 500px;
    animation: fadeIn 0.8s ease;
}

/* Badge Styles */
.badge {
    font-weight: 600;
    padding: 0.5em 0.75em;
    border-radius: 12px;
    font-size: 0.9rem;
}
.badge.bg-success { background: linear-gradient(135deg, #0ea5e9, #3b82f6); color: #fff; }
.badge.bg-secondary { background: #6c757d; color: #fff; }

/* Text Styling */
p {
    font-size: 1rem;
    margin-bottom: 0.8rem;
}
strong {
    color: #1e3a8a;
}

/* Animation */
@keyframes fadeIn{
    from {opacity:0; transform: translateY(20px);}
    to {opacity:1; transform: none;}
}
</style>
</head>

<body>

<nav class="navbar navbar-dark w-100 px-4 fixed-top">
    <span class="navbar-brand fw-bold">👁 Staff Details</span>
    <a href="staff.php" class="btn btn-outline-light btn-sm">⬅ Back</a>
</nav>

<div class="container my-5">
    <div class="card shadow mx-auto">
        <p><strong>Name:</strong> <?= htmlspecialchars($staff['full_name']) ?></p>
        <p><strong>Username:</strong> <?= htmlspecialchars($staff['username']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($staff['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($staff['role']) ?></p>
        <p><strong>Status:</strong>
            <span class="badge <?= $staff['status']=='Active'?'bg-success':'bg-secondary' ?>">
                <?= $staff['status'] ?>
            </span>
        </p>
        <p><strong>Created:</strong> <?= $staff['created_at'] ?></p>
    </div>
</div>

</body>
</html>
