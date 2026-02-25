<?php
session_start();
require_once "db.php";

if(!isset($_SESSION['staff_id'])){
    header("Location: staff_login.php");
    exit;
}

$id = (int)$_SESSION['staff_id'];

/* ===== Fetch Staff Full Details ===== */
$stmt = $conn->prepare("SELECT * FROM staff WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$staff){
    header("Location: staff_logout.php");
    exit;
}

function badgeClass($status){
    return ($status === "Active") ? "badge-active" : "badge-inactive";
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

/* ===== Approve/Reject Candidate (UPDATE BOTH TABLES) ===== */
if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST['assign_id'], $_POST['candidate_id'])){
    $assign_id = (int)$_POST['assign_id'];
    $candidate_id = (int)$_POST['candidate_id'];
    $new_status = $_POST['assign_status'] ?? "Pending";
    $comment = trim($_POST['staff_comment'] ?? "");

    // Update staff assignment table
    $stmt = $conn->prepare("UPDATE staff_candidate_assign 
        SET status=?, staff_comment=? 
        WHERE id=? AND staff_id=?");
    $stmt->bind_param("ssii", $new_status, $comment, $assign_id, $id);
    $stmt->execute();
    $stmt->close();

    // Update main job_applications (Admin dashboard reads this)
    $staff_name = $staff['full_name'] ?? "Staff";

    $stmt2 = $conn->prepare("UPDATE job_applications 
        SET status=?, updated_by_staff_id=?, updated_by_staff_name=?, updated_at=NOW()
        WHERE id=?");
    $stmt2->bind_param("sisi", $new_status, $id, $staff_name, $candidate_id);
    $stmt2->execute();
    $stmt2->close();

    header("Location: staff_dashboard.php");
    exit;
}

/* ===== Fetch Assigned Candidates ===== */
$assigned = $conn->query("
    SELECT sca.id as assign_id, sca.status as assign_status, sca.staff_comment, sca.assigned_at, sca.updated_at,
           ja.*
    FROM staff_candidate_assign sca
    JOIN job_applications ja ON ja.id = sca.candidate_id
    WHERE sca.staff_id = $id
    ORDER BY sca.id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box}
body{
    font-family:Poppins,sans-serif;
    min-height:100vh;
    padding-top:80px;
    overflow-x:hidden;
    background:linear-gradient(135deg,#0f172a,#1d4ed8,#06b6d4);
    background-size:200% 200%;
    animation:bgMove 7s ease infinite;
}
@keyframes bgMove{
    0%{background-position:0% 50%}
    50%{background-position:100% 50%}
    100%{background-position:0% 50%}
}

.navbar{
    position:fixed; top:0; left:0; right:0;
    z-index:999;
    padding:14px 18px;
    background:rgba(15,23,42,.75);
    backdrop-filter:blur(16px);
    border-bottom:1px solid rgba(255,255,255,.15);
}


.navbar-brand{font-weight:700}
.navbar .btn{border-radius:12px;font-weight:600}

.page-wrap{max-width:1100px;margin:auto;padding:20px}

.glass{
    background:rgba(255,255,255,.14);
    border:1px solid rgba(255,255,255,.18);
    backdrop-filter:blur(16px);
    border-radius:22px;
    box-shadow:0 18px 45px rgba(0,0,0,.22);
    color:#fff;
}
.hero{padding:22px}
.hero h2{font-weight:700;margin-bottom:6px}
.hero p{margin:0;opacity:.9}

.card-glass{margin-top:18px;overflow:hidden}
.card-glass .card-header{
    background:rgba(0,0,0,.22); border:none; padding:16px 20px; font-weight:700; color:#fff;
}
.card-glass .card-body{padding:20px}

.info-box,.task-box{
    background:rgba(0,0,0,.22);
    border:1px solid rgba(255,255,255,.14);
    border-radius:18px;
    padding:16px;
    transition:.18s ease;
}
.info-box:hover,.task-box:hover{transform:translateY(-2px); box-shadow:0 14px 26px rgba(0,0,0,.18)}
.info-title{font-size:13px;opacity:.85;margin-bottom:6px}
.info-value{font-size:16px;font-weight:700;word-break:break-word}

.badge-active,.badge-inactive{
    padding:8px 12px;border-radius:999px;font-weight:700;display:inline-block
}
.badge-active{background:linear-gradient(135deg,#22c55e,#16a34a)}
.badge-inactive{background:linear-gradient(135deg,#94a3b8,#475569)}

.small-note{margin-top:18px;text-align:center;color:rgba(255,255,255,.9);font-size:13px;opacity:.9}

.tile-box{
    background:rgba(255,255,255,.12);
    border:1px solid rgba(255,255,255,.18);
    border-radius:18px;
    padding:16px;
    text-align:center;
    transition:.2s ease;
    box-shadow:0 12px 22px rgba(0,0,0,.18);
    cursor:pointer;
    user-select:none;
}
.tile-box:hover{
    transform:translateY(-3px);
    box-shadow:0 18px 30px rgba(0,0,0,.25);
}
.tile-icon{ font-size:28px; margin-bottom:8px; }
.tile-title{ font-weight:700; color:#fff; font-size:15px; }
.tile-sub{ font-size:12px; opacity:.9; margin-top:3px; color:rgba(255,255,255,.9); }

#tasksSection{ display:none; }

.view-link{
    display:inline-flex;
    align-items:center;
    gap:6px;
    padding:8px 12px;
    border-radius:14px;
    text-decoration:none;
    font-weight:700;
    background:rgba(255,255,255,.14);
    border:1px solid rgba(255,255,255,.18);
    color:#fff;
    cursor:pointer;
    transition:.2s ease;
}
.view-link:hover{
    transform:translateY(-2px);
    background:rgba(255,255,255,.2);
}

.details-box{
    margin-top:14px;
    padding:14px;
    border-radius:18px;
    background:rgba(0,0,0,.18);
    border:1px solid rgba(255,255,255,.12);
    display:none;
}
.details-title{
    font-weight:800;
    margin-bottom:10px;
}
.detail-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:10px;
}
.detail-item{
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.12);
    border-radius:16px;
    padding:12px;
}
.detail-item small{
    opacity:.85;
    display:block;
    margin-bottom:4px;
}
.resume-box{
    margin-top:12px;
    padding:12px;
    border-radius:16px;
    background:rgba(255,255,255,.10);
    border:1px solid rgba(255,255,255,.12);
}
</style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="d-flex align-items-center gap-2">
        <span class="navbar-brand mb-0">👤 Staff Dashboard</span>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-outline-light btn-sm" onclick="goBack()">⬅ Back</button>
        <a href="staff_logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>
</nav>

<div class="page-wrap">

    <div class="hero glass">
        <h2>Welcome, <?= htmlspecialchars($staff['full_name'] ?? "Staff") ?> 👋</h2>
        <p>Click <b>My Tasks</b> to view assigned candidates.</p>
    </div>

    <div class="card card-glass glass">
        <div class="card-header">📌 Your Profile Information</div>
        <div class="card-body">

            <div class="row g-3">
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-title">Full Name</div>
                        <div class="info-value"><?= htmlspecialchars($staff['full_name'] ?? "-") ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-title">Username</div>
                        <div class="info-value"><?= htmlspecialchars($staff['username'] ?? "-") ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-title">Email</div>
                        <div class="info-value"><?= htmlspecialchars($staff['email'] ?? "-") ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-title">Role</div>
                        <div class="info-value"><?= htmlspecialchars($staff['role'] ?? "Staff") ?></div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-title">Status</div>
                        <div class="info-value">
                            <span class="<?= badgeClass($staff['status'] ?? "Inactive") ?>">
                                <?= htmlspecialchars($staff['status'] ?? "Inactive") ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="info-box">
                        <div class="info-title">Created At</div>
                        <div class="info-value"><?= htmlspecialchars($staff['created_at'] ?? "-") ?></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="tile-box" onclick="toggleTasks()">
                            <div class="tile-icon">📋</div>
                            <div class="tile-title">My Tasks</div>
                            <div class="tile-sub">Click to show/hide</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="small-note">✅ Staff can view details + approve/reject candidates.</div>
        </div>
    </div>

    <div class="card card-glass glass mt-3" id="tasksSection">
        <div class="card-header">📌 Assigned Candidates</div>
        <div class="card-body">

            <?php if($assigned->num_rows == 0){ ?>
                <div class="task-box">
                    <p class="m-0">No candidates assigned yet ✅</p>
                </div>
            <?php } ?>

            <?php while($c = $assigned->fetch_assoc()){ ?>
                <div class="task-box mb-3">

                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                        <div>
                            <div style="font-weight:800;font-size:16px;">
                                👤 <?= htmlspecialchars($c['full_name'] ?? "Candidate") ?>
                            </div>
                            <div style="opacity:.9;font-size:13px;margin-top:4px;">
                                <b>Email:</b> <?= htmlspecialchars($c['email'] ?? "-") ?><br>
                                <b>Decision:</b> <?= htmlspecialchars($c['assign_status'] ?? "Pending") ?>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-2">
                            <span class="view-link" onclick="toggleDetails(<?= (int)$c['assign_id'] ?>)">
                                👁 View Details
                            </span>
                        </div>
                    </div>

                    <div class="details-box" id="details<?= (int)$c['assign_id'] ?>">

                        <div class="details-title">📌 Candidate Full Details</div>

                        <div class="detail-grid">
                            <div class="detail-item">
                                <small>Personal Info</small>
                                <b>Name:</b> <?= htmlspecialchars($c['full_name'] ?? "-") ?><br>
                                <b>Email:</b> <?= htmlspecialchars($c['email'] ?? "-") ?><br>
                                <b>Phone:</b> <?= htmlspecialchars($c['phone'] ?? "-") ?><br>
                                <b>Address:</b> <?= htmlspecialchars($c['address'] ?? "-") ?>
                            </div>

                            <div class="detail-item">
                                <small>Academic Info</small>
                                <b>Qualification:</b> <?= htmlspecialchars($c['qualification'] ?? "-") ?><br>
                                <b>College:</b> <?= htmlspecialchars($c['college'] ?? "-") ?><br>
                                <b>Year:</b> <?= htmlspecialchars($c['passing_year'] ?? "-") ?><br>
                                <b>Percentage:</b> <?= htmlspecialchars($c['percentage'] ?? "-") ?>
                            </div>

                            <div class="detail-item">
                                <small>Experience Info</small>
                                <b>Experience:</b> <?= htmlspecialchars($c['experience'] ?? "-") ?><br>
                                <b>Company:</b> <?= htmlspecialchars($c['company'] ?? "-") ?><br>
                                <b>Skills:</b> <?= htmlspecialchars($c['skills'] ?? "-") ?>
                            </div>

                            <div class="detail-item">
                                <small>Job Info</small>
                                <b>Applied Job:</b> <?= htmlspecialchars($c['job_title'] ?? "-") ?><br>
                                <b>Applied Date:</b> <?= htmlspecialchars($c['created_at'] ?? "-") ?>
                            </div>
                        </div>

                        <div class="resume-box">
                            <b>📄 Resume:</b><br>
                            <?php if(!empty($c['resume_path'])){ ?>
                                <a class="view-link mt-2" href="<?= htmlspecialchars($c['resume_path']) ?>" target="_blank">
                                    📄 View Resume
                                </a>
                            <?php } else { ?>
                                <div style="opacity:.9;margin-top:6px;">No resume uploaded ❌</div>
                            <?php } ?>
                        </div>

                        <form method="POST" class="mt-3">
                            <input type="hidden" name="assign_id" value="<?= (int)$c['assign_id'] ?>">
                            <input type="hidden" name="candidate_id" value="<?= (int)$c['id'] ?>">

                            <label class="form-label fw-semibold">Approve / Reject</label>
                            <select name="assign_status" class="form-select mb-2" required>
                                <option value="Pending"  <?= ($c['assign_status']=="Pending")?"selected":"" ?>>Pending</option>
                                <option value="Approved" <?= ($c['assign_status']=="Approved")?"selected":"" ?>>Approved</option>
                                <option value="Rejected" <?= ($c['assign_status']=="Rejected")?"selected":"" ?>>Rejected</option>
                            </select>

                            <label class="form-label fw-semibold">Staff Comment</label>
                            <textarea name="staff_comment" class="form-control mb-2" rows="2"
                            placeholder="Write your comment..."><?= htmlspecialchars($c['staff_comment'] ?? "") ?></textarea>

                            <button class="btn btn-success btn-sm">💾 Save Decision</button>
                        </form>

                    </div>

                </div>
            <?php } ?>

        </div>
    </div>

</div>

<script>
function goBack(){
    if (window.history.length > 1) window.history.back();
    else window.location.href = "staff_login.php";
}

function toggleTasks(){
    let tasks = document.getElementById("tasksSection");
    if(tasks.style.display === "none" || tasks.style.display === ""){
        tasks.style.display = "block";
        tasks.scrollIntoView({behavior:"smooth"});
    }else{
        tasks.style.display = "none";
    }
}

function toggleDetails(id){
    let box = document.getElementById("details"+id);
    if(!box) return;

    if(box.style.display === "none" || box.style.display === ""){
        box.style.display = "block";
        box.scrollIntoView({behavior:"smooth"});
    }else{
        box.style.display = "none";
    }
}
</script>

</body>
</html>
