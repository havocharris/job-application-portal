<?php
session_start();
include "db.php";

/* ---------- LOGIN CHECK ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

/* ---------- APPROVE / REJECT ACTION ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['action'];

    if ($status === 'Approved' || $status === 'Rejected') {
        $stmt = $conn->prepare("UPDATE job_applications SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
    }
}

/* ---------- DASHBOARD DATA ---------- */
$total = $conn->query("SELECT COUNT(*) c FROM job_applications")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) c FROM job_applications WHERE status='Approved'")->fetch_assoc()['c'];
$rejected = $conn->query("SELECT COUNT(*) c FROM job_applications WHERE status='Rejected'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) c FROM job_applications WHERE status='Pending'")->fetch_assoc()['c'];

$result = $conn->query("SELECT * FROM job_applications ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ----------------- GLOBAL ----------------- */
body {
    margin: 0;
    min-height: 100vh;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f4f7fb; /* very light blue-gray */
    color: #1e293b; /* dark slate text */
}

/* ----------------- NAVBAR ----------------- */
.navbar {
    background: linear-gradient(135deg, #1e40af, #3b82f6); /* deep-to-light blue */
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: #fff;
    font-weight: 600;
}

/* ----------------- STATS CARDS ----------------- */
.stat {
    padding: 25px;
    border-radius: 18px;
    color: #fff;
    font-weight: 600;
    font-size: 18px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: default;
}

.stat:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.total {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
}
.approved {
    background: linear-gradient(135deg, #0ea5e9, #3b82f6);
}
.rejected {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}
.pending {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

/* ----------------- SEARCH INPUT ----------------- */
#search {
    border-radius: 12px;
    border: 1px solid #cfd8e3;
    padding: 0.5rem 1rem;
    font-size: 15px;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

#search:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 8px rgba(59,130,246,0.2);
}

/* ----------------- TABLE ----------------- */
.table {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.table thead th {
    border: none;
    font-weight: 600;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff;
}

.table tbody td {
    vertical-align: middle;
}

/* ----------------- BADGES ----------------- */
.badge {
    font-weight: 600;
    padding: 0.5em 0.75em;
    border-radius: 12px;
    font-size: 0.9rem;
}

.badge.bg-success { background-color: #0ea5e9; }
.badge.bg-danger { background-color: #ef4444; }
.badge.bg-warning { background-color: #f59e0b; }

/* ----------------- ACTION BUTTONS ----------------- */
.btn-sm {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-sm:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.12);
}

.btn-success {
    background: linear-gradient(135deg, #0ea5e9, #3b82f6);
    color: #fff;
}
.btn-danger {
    background: linear-gradient(135deg, #ef4444, #f87171);
    color: #fff;
}

/* ----------------- MODAL ----------------- */
.modal-content {
    border-radius: 16px;
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    overflow: hidden;
}

.modal-header {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff;
    font-weight: 600;
}

.modal-body p {
    margin-bottom: 0.5rem;
}

/* ----------------- RESPONSIVE ----------------- */
@media (max-width: 576px) {
    .stat {
        font-size: 16px;
        padding: 18px;
    }
}

</style>
</head>

<body>

<nav class="navbar navbar-dark bg-dark px-4">
<span class="navbar-brand">🛠 Admin Dashboard</span>
<a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container my-4">

<!-- STATS -->
<div class="row g-3 mb-4">
<div class="col-md-3"><div class="stat total">📄 Total<br><?= $total ?></div></div>
<div class="col-md-3"><div class="stat approved">✅ Approved<br><?= $approved ?></div></div>
<div class="col-md-3"><div class="stat rejected">❌ Rejected<br><?= $rejected ?></div></div>
<div class="col-md-3"><div class="stat pending">⏳ Pending<br><?= $pending ?></div></div>
</div>

<input class="form-control mb-3" id="search" placeholder="🔍 Search candidates">

<table class="table table-hover text-center align-middle">
<thead class="table-dark">
<tr>
<th>Name</th>
<th>Email</th>
<th>Status</th>
<th>Resume</th>
<th>Action</th>
</tr>
</thead>

<tbody id="table">
<?php while($r = $result->fetch_assoc()) { ?>
<tr>
<td>
<a href="#" data-bs-toggle="modal" data-bs-target="#view<?= $r['id'] ?>" class="fw-semibold text-decoration-none">
<?= $r['full_name'] ?>
</a>
</td>

<td><?= $r['email'] ?></td>

<td>
<span class="badge 
<?= $r['status']=='Approved'?'bg-success':($r['status']=='Rejected'?'bg-danger':'bg-warning') ?>">
<?= $r['status'] ?>
</span>
</td>

<td>
<a href="<?= $r['resume_path'] ?>" target="_blank">View</a>
</td>

<td>
<form method="POST" class="d-flex gap-1 justify-content-center">
<input type="hidden" name="id" value="<?= $r['id'] ?>">
<button name="action" value="Approved" class="btn btn-success btn-sm">Approve</button>
<button name="action" value="Rejected" class="btn btn-danger btn-sm">Reject</button>
</form>
</td>
</tr>

<!-- VIEW DETAILS MODAL -->
<div class="modal fade" id="view<?= $r['id'] ?>" tabindex="-1">
<div class="modal-dialog modal-lg modal-dialog-centered">
<div class="modal-content">

<div class="modal-header bg-dark text-white">
<h5 class="modal-title">Candidate Details</h5>
<button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<p><strong>Name:</strong> <?= $r['full_name'] ?></p>
<p><strong>Email:</strong> <?= $r['email'] ?></p>
<p><strong>Mobile:</strong> <?= $r['mobile'] ?></p>
<p><strong>Gender:</strong> <?= $r['gender'] ?></p>
<p><strong>Qualification:</strong> <?= $r['qualification'] ?></p>
<p><strong>Skills:</strong> <?= $r['skills'] ?></p>
<p><strong>Status:</strong> <?= $r['status'] ?></p>
</div>

<div class="modal-footer">
<a href="<?= $r['resume_path'] ?>" target="_blank" class="btn btn-primary">View Resume</a>
<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

</div>
</div>
</div>
<?php } ?>
</tbody>
</table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("search").onkeyup = function(){
    let v = this.value.toLowerCase();
    document.querySelectorAll("#table tr").forEach(r=>{
        r.style.display = r.innerText.toLowerCase().includes(v) ? "" : "none";
    });
};
</script>

</body>
</html>
