<?php
session_start();
include "db.php";

/* ---------- LOGIN CHECK ---------- */
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

/* ---------- APPROVE / REJECT / PENDING ACTION ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Status update
    if (isset($_POST['action'], $_POST['id'])) {
        $id = (int)$_POST['id'];
        $status = $_POST['action'];

        if (in_array($status, ['Approved','Rejected','Pending'])) {
            $stmt = $conn->prepare("UPDATE job_applications 
                SET status=?, updated_by_staff_id=NULL, updated_by_staff_name='Admin', updated_at=NOW()
                WHERE id=?");
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Delete candidate
    if (isset($_POST['delete_id'])) {
        $del_id = (int)$_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM job_applications WHERE id=?");
        $stmt->bind_param("i", $del_id);
        $stmt->execute();
        $stmt->close();
        header("Location: admin_dashboard.php");
        exit;
    }
}

/* ---------- DASHBOARD DATA ---------- */
$total    = $conn->query("SELECT COUNT(*) c FROM job_applications")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) c FROM job_applications WHERE status='Approved'")->fetch_assoc()['c'];
$rejected = $conn->query("SELECT COUNT(*) c FROM job_applications WHERE status='Rejected'")->fetch_assoc()['c'];
$pending  = $conn->query("SELECT COUNT(*) c FROM job_applications WHERE status='Pending'")->fetch_assoc()['c'];

$result = $conn->query("SELECT * FROM job_applications ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ====== GLOBAL THEME ====== */
body{
    margin:0;
    min-height:100vh;
    font-family: "Poppins", Arial, sans-serif;
    background: linear-gradient(135deg, #b0c4ff, #071018);
    color:#111827;
}

/* ====== NAVBAR ====== */
.navbar{
    background:#ffffff;
    border-bottom:1px solid #e5e7eb;
    box-shadow:0 6px 20px rgba(0,0,0,.06);
}
.navbar-brand{
    font-weight:800;
    letter-spacing:.3px;
    color:#2563eb !important;
}
.nav-btn{
    border-radius:12px;
    font-weight:600;
    padding:8px 14px;
}

/* ====== CARDS ====== */
.stat{
    padding:18px;
    border-radius:18px;
    font-weight:700;
    font-size:16px;
    text-align:left;
    box-shadow:0 8px 25px rgba(0,0,0,.06);
    transition:.25s;
    background:#fff;
    border:1px solid #e5e7eb;
}
.stat:hover{ transform:translateY(-5px); }
.stat small{ font-weight:600; color:#6b7280; display:block; }
.stat h3{ margin:0; font-weight:900; }

.total{ border-left:6px solid #2563eb; }
.approved{ border-left:6px solid #16a34a; }
.rejected{ border-left:6px solid #dc2626; }
.pending{ border-left:6px solid #f59e0b; }

/* ====== SEARCH ====== */
#search{
    border-radius:14px;
    padding:12px 14px;
    border:1px solid #e5e7eb80;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
}
#search:focus{
    outline:none;
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
}

/* ====== TABLE ====== */
.table-wrap{
    background:#fff;
    border-radius:18px;
    padding:12px;
    border:1px solid #e5e7eb;
    box-shadow:0 10px 30px rgba(0,0,0,.06);
}
.table{
    margin:0;
}
.table thead th{
    background:#f9fafb;
    color:#111827;
    border-bottom:1px solid #80a7f7 !important;
    font-weight:800;
    padding:14px 10px;
}
.table tbody td{
    padding:14px 10px;
    border-bottom:1px solid #f1f5f9;
    vertical-align:middle;
}
.table tbody tr:hover{
    background:#f3f6ff;
    transition:.2s;
}

/* ====== BADGES ====== */
.badge{
    border-radius:999px;
    padding:7px 12px;
    font-weight:700;
    font-size:12px;
}
.badge.bg-success{ background:#16a34a !important; }
.badge.bg-danger{ background:#dc2626 !important; }
.badge.bg-warning{ background:#f59e0b !important; color:#111827; }

/* ====== BUTTONS ====== */
.btn-sm{
    border-radius:12px;
    font-weight:700;
    padding:7px 12px;
}
.btn-delete{
    background:#ef4444;
    border:none;
    color:#fff;
}
.btn-delete:hover{
    background:#dc2626;
}

/* ====== DROPDOWN ====== */
.form-select{
    border-radius:12px;
    font-weight:600;
    border:1px solid #e5e7eb;
}
.form-select:focus{
    border-color:#2563eb;
    box-shadow:0 0 0 4px rgba(37,99,235,.15);
}

/* ====== MODAL ====== */
.modal-content{
    border-radius:18px;
    border:none;
    box-shadow:0 20px 60px rgba(0,0,0,.2);
}
.modal-header{
    background:#2563eb;
    color:#fff;
    border:none;
}
.modal-body p{
    margin-bottom:10px;
    font-size:15px;
}
.modal-body b{
    color:#111827;
}
.modal-body{
    background:#ffffff;
}
</style>
</head>

<body>

<nav class="navbar px-4 py-3 d-flex justify-content-between align-items-center">
    <span class="navbar-brand fs-5">🛠 Admin Dashboard</span>

    <div class="d-flex gap-2">
        <a href="staff.php" class="btn btn-primary nav-btn btn-sm">👥 Staff</a>
        <a href="logout.php" class="btn btn-dark nav-btn btn-sm">Logout</a>
    </div>
</nav>

<div class="container my-4">

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat total">
                <small>Total Applications</small>
                <h3><?= $total ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat approved">
                <small>Approved</small>
                <h3><?= $approved ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat rejected">
                <small>Rejected</small>
                <h3><?= $rejected ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat pending">
                <small>Pending</small>
                <h3><?= $pending ?></h3>
            </div>
        </div>
    </div>

    <input class="form-control mb-3" id="search" placeholder="🔍 Search by name, email, status...">

    <div class="table-wrap">
        <table class="table table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Resume</th>
                    <th>Changed By</th>
                    <th>Changed Time</th>
                    <th style="width:220px;">Action</th>
                </tr>
            </thead>

            <tbody id="table">
            <?php while($r = $result->fetch_assoc()){ ?>
                <tr>
                    <td>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#view<?= $r['id'] ?>"
                           class="fw-bold text-decoration-none" style="color:#2563eb;">
                            <?= htmlspecialchars($r['full_name']) ?>
                        </a>
                    </td>

                    <td><?= htmlspecialchars($r['email']) ?></td>

                    <td>
                        <span class="badge <?= $r['status']=='Approved'?'bg-success':($r['status']=='Rejected'?'bg-danger':'bg-warning') ?>">
                            <?= htmlspecialchars($r['status']) ?>
                        </span>
                    </td>

                    <td>
                        <?php if(!empty($r['resume_path'])){ ?>
                            <a href="<?= htmlspecialchars($r['resume_path']) ?>" target="_blank" class="fw-semibold text-decoration-none" style="color:#0ea5e9;">
                                View
                            </a>
                        <?php } else { ?>
                            <span class="text-muted">No Resume</span>
                        <?php } ?>
                    </td>

                    <td><?= htmlspecialchars($r['updated_by_staff_name'] ?? "Admin") ?></td>
                    <td><?= !empty($r['updated_at']) ? htmlspecialchars($r['updated_at']) : "-" ?></td>

                    <td>
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <!-- STATUS DROPDOWN -->
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Pending" <?= $r['status']=='Pending'?'selected':'' ?>>Pending</option>
                                    <option value="Approved" <?= $r['status']=='Approved'?'selected':'' ?>>Approved</option>
                                    <option value="Rejected" <?= $r['status']=='Rejected'?'selected':'' ?>>Rejected</option>
                                </select>
                            </form>

                            <!-- DELETE BUTTON -->
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this candidate?')">
                                <input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>">
                                <button class="btn btn-delete btn-sm">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<!-- MODALS -->
<?php
$result2 = $conn->query("SELECT * FROM job_applications ORDER BY id DESC");
while($r = $result2->fetch_assoc()){
?>
<div class="modal fade" id="view<?= $r['id'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Candidate Details</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><b>Name:</b> <?= htmlspecialchars($r['full_name']) ?></p>
                <p><b>Email:</b> <?= htmlspecialchars($r['email']) ?></p>
                <p><b>Mobile:</b> <?= htmlspecialchars($r['mobile'] ?? "-") ?></p>
                <p><b>Qualification:</b> <?= htmlspecialchars($r['qualification'] ?? "-") ?></p>
                <p><b>Skills:</b> <?= htmlspecialchars($r['skills'] ?? "-") ?></p>

                <?php if(!empty($r['resume_path'])){ ?>
                    <p><b>Resume:</b>
                        <a href="<?= htmlspecialchars($r['resume_path']) ?>" target="_blank" style="color:#2563eb; font-weight:700;">
                            View Resume
                        </a>
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

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
