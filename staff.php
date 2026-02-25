<?php
session_start();
require_once "db.php";

/* ---------- LOGIN CHECK ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

/* ---------- REMOVE STAFF ---------- */
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM staff WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: staff.php");
    exit;
}

/* ---------- CREATE ASSIGN TABLE (AUTO) ---------- */
$conn->query("CREATE TABLE IF NOT EXISTS staff_candidate_assign (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT NOT NULL,
    candidate_id INT NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    staff_comment TEXT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

/* ---------- ASSIGN CANDIDATE TO STAFF ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_staff_id'])) {
    $staff_id = (int)$_POST['assign_staff_id'];
    $candidate_id = (int)$_POST['candidate_id'];

    $stmt = $conn->prepare("INSERT INTO staff_candidate_assign (staff_id, candidate_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $staff_id, $candidate_id);
    $stmt->execute();

    header("Location: staff.php");
    exit;
}

/* ---------- FETCH STAFF ---------- */
$result = $conn->query("SELECT * FROM staff ORDER BY id DESC");

/* ---------- FETCH CANDIDATES ---------- */
$candidates = $conn->query("SELECT id, full_name, email FROM job_applications ORDER BY id DESC");

/* ---------- FETCH LATEST ASSIGNMENT PER STAFF ---------- */
$assignments = [];
$a = $conn->query("SELECT * FROM staff_candidate_assign ORDER BY id DESC");
while($row = $a->fetch_assoc()){
    if(!isset($assignments[$row['staff_id']])){
        $assignments[$row['staff_id']] = $row; // latest only
    }
}

/* ---------- FETCH CANDIDATE DETAILS FOR ASSIGNMENTS ---------- */
$candidateMap = [];
$cc = $conn->query("SELECT id, full_name, email FROM job_applications ORDER BY id DESC");
while($c = $cc->fetch_assoc()){
    $candidateMap[$c['id']] = $c;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}
body{
    font-family:'Poppins',sans-serif;
    min-height:100vh;
    background:
        radial-gradient(circle at top left, rgba(251,146,60,.35), transparent 55%),
        radial-gradient(circle at bottom right, rgba(168,85,247,.28), transparent 55%),
        radial-gradient(circle at center, rgba(252,211,77,.18), transparent 60%),
        linear-gradient(135deg,#111827,#1f2937,#27272a);
    background-size:200% 200%;
    animation:bgMove 12s ease infinite;
    color:#f5f5f5;
}

/* NAVBAR */
.navbar{
    background:rgba(17,24,39,.72);
    backdrop-filter:blur(18px);
    border-bottom:1px solid rgba(255,255,255,.16);
    box-shadow:0 10px 30px rgba(0,0,0,.25);
}
.navbar-brand{
    font-weight:800;
    letter-spacing:.3px;
    color:#fbbf24;
}

/* TABLE */
.table{
    background:rgba(0,0,0,.25);
    border-radius:15px;
    overflow:hidden;
}
.table thead th{
    background:linear-gradient(135deg,#f97316,#c2410c);
    color:#fff;
    font-weight:800;
    text-align:center;
}
.table tbody tr:hover{
    background:rgba(251,146,60,.15);
    transform:scale(1.01);
    transition:all 0.2s ease-in-out;
}

/* BUTTONS */
.btn-add{
    background:linear-gradient(135deg,#fbbf24,#f97316);
    border:none;
    color:#111827;
    font-weight:800;
    border-radius:16px;
}
.btn-assign{
    background:linear-gradient(135deg,#f97316,#c2410c);
    border:none;
    color:#fff;
    font-weight:700;
}
.btn-edit{
    background:linear-gradient(135deg,#a855f7,#9333ea);
    border:none;
    color:#fff;
    font-weight:700;
}
.btn-remove{
    background:linear-gradient(135deg,#ef4444,#b91c1c);
    border:none;
    color:#fff;
    font-weight:700;
}
.btn-sm{
    border-radius:10px;
    font-weight:600;
}

/* BADGES */
.badge-active{
    background:linear-gradient(135deg,#facc15,#f97316);
    color:#111827;
}
.badge-inactive{
    background:linear-gradient(135deg,#6b7280,#374151);
    color:#fff;
}

/* MODAL */
.modal-header{
    background:linear-gradient(135deg,#f97316,#9333ea);
    color:#fff;
    font-weight:900;
}
.modal-content{
    border-radius:15px;
    overflow:hidden;
}

/* INPUTS */
.form-select, .form-control{
    border-radius:14px;
    font-weight:600;
}

/* RESPONSIVE */
@media(max-width:768px){
    .table thead{display:none;}
    .table tbody td{display:block;text-align:left;}
    .table tbody td:before{content: attr(data-label); font-weight:600; display:inline-block; width:120px;}
}
</style>
</head>
<body>

<nav class="navbar navbar-dark px-4 py-3 d-flex justify-content-between">
    <span class="navbar-brand fs-5 fw-bold">👥 Staff Management</span>
    <a href="admin_dashboard.php" class="btn btn-add btn-sm">⬅ Back</a>
</nav>

<div class="container my-5">

    <div class="d-flex justify-content-end mb-4">
        <a href="staff_add.php" class="btn btn-add shadow-sm">➕ Add Staff</a>
    </div>

    <table class="table table-hover text-center align-middle">
        <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th width="350">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($s = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($s['full_name']) ?></td>
                <td><?= htmlspecialchars($s['username']) ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td>
                    <span class="badge <?= $s['status']==='Active'?'badge-active':'badge-inactive'?>">
                        <?= htmlspecialchars($s['status']) ?>
                    </span>
                </td>
                <td class="d-flex gap-1 justify-content-center flex-wrap">
                    <a href="#" class="btn btn-assign btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal<?= $s['id'] ?>">Assign Candidate</a>
                    <a href="staff_edit.php?id=<?= $s['id'] ?>" class="btn btn-edit btn-sm">Edit</a>
                    <a href="staff.php?remove=<?= $s['id'] ?>" class="btn btn-remove btn-sm" onclick="return confirm('Are you sure you want to remove this staff?')">Remove</a>
                </td>
            </tr>

            <!-- ASSIGN MODAL -->
            <div class="modal fade" id="assignModal<?= $s['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Assign Candidate to <?= htmlspecialchars($s['full_name']) ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <form method="POST">
                      <input type="hidden" name="assign_staff_id" value="<?= $s['id'] ?>">
                      <label class="form-label fw-bold">Select Candidate</label>
                      <select name="candidate_id" class="form-select mb-3" required>
                        <option value="">-- Choose Candidate --</option>
                        <?php $candidates->data_seek(0); while($c = $candidates->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['email']) ?>)</option>
                        <?php endwhile; ?>
                      </select>
                      <button class="btn btn-assign w-100">Assign</button>
                    </form>
                    <hr>
                    <?php if(isset($assignments[$s['id']])): 
                        $as = $assignments[$s['id']];
                        $cand = $candidateMap[$as['candidate_id']] ?? null;
                    ?>
                        <p class="mb-1"><b>Latest Assigned Candidate:</b></p>
                        <p class="mb-1"><?= $cand ? htmlspecialchars($cand['full_name'])." (".htmlspecialchars($cand['email']).")" : "Unknown" ?></p>
                        <p class="mb-1"><b>Status:</b> <?= htmlspecialchars($as['status']) ?></p>
                        <p class="mb-1"><b>Staff Comment:</b> <?= htmlspecialchars($as['staff_comment'] ?? "-") ?></p>
                        <p class="mb-0"><b>Updated:</b> <?= htmlspecialchars($as['updated_at']) ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0">No candidate assigned yet.</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-muted">No staff found</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
