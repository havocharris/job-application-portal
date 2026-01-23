<?php
include "db.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

/* ================= SANITIZE INPUT ================= */
function clean($data) {
    return htmlspecialchars(trim($data));
}

/* ================= COLLECT DATA ================= */
$full_name   = clean($_POST['full_name'] ?? '');
$email       = clean($_POST['email'] ?? '');
$mobile      = clean($_POST['mobile'] ?? '');
$dob         = $_POST['dob'] ?? '';
$gender      = $_POST['gender'] ?? '';
$address     = clean($_POST['address'] ?? '');
$nationality = clean($_POST['nationality'] ?? '');
$relocate    = $_POST['relocate'] ?? '';

$qualification    = clean($_POST['qualification'] ?? '');
$institution      = clean($_POST['institution'] ?? '');
$graduation_year  = (int) ($_POST['graduation_year'] ?? 0);
$cgpa             = clean($_POST['cgpa'] ?? '');
$skills           = implode(",", $_POST['skills'] ?? []);
$certifications   = clean($_POST['certifications'] ?? '');

$employmentStatus = clean($_POST['employment_status'] ?? '');
$summary = clean($_POST['skills_summary'] ?? '');
$previous_org     = clean($_POST['previous_org'] ?? '');
$job_title        = clean($_POST['job_title'] ?? '');
$summary          = clean($_POST['summary'] ?? '');

$status = "Pending";

/* ================= BASIC VALIDATION ================= */
if (!$full_name || !$email || !$mobile) {
    die("Required fields missing");
}

/* ================= DUPLICATE EMAIL CHECK ================= */
$check = $conn->prepare("SELECT id FROM job_applications WHERE email=?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    die("You have already applied using this email.");
}

/* ================= RESUME UPLOAD ================= */
if (!isset($_FILES['resume']) || $_FILES['resume']['error'] !== 0) {
    die("Resume is required");
}

$resume = $_FILES['resume'];
$allowed = ['pdf', 'doc', 'docx'];
$ext = strtolower(pathinfo($resume['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    die("Only PDF, DOC, DOCX allowed");
}

if ($resume['size'] > 5 * 1024 * 1024) {
    die("Resume must be under 5MB");
}

$uploadDir = "uploads/resumes/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$safeName = uniqid("resume_", true) . "." . $ext;
$target = $uploadDir . $safeName;

move_uploaded_file($resume['tmp_name'], $target);

/* ================= INSERT INTO DB ================= */
$stmt = $conn->prepare("
INSERT INTO job_applications 
(full_name,email,mobile,dob,gender,address,nationality,relocate,
qualification,institution,graduation_year,cgpa,skills,certifications,
experience,employment_status,previous_org,job_title,summary,resume_path,status)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->bind_param(
    "ssssssssssissssssssss",
    $full_name,
    $email,
    $mobile,
    $dob,
    $gender,
    $address,
    $nationality,
    $relocate,
    $qualification,
    $institution,
    $graduation_year,
    $cgpa,
    $skills,
    $certifications,
    $experience,
    $employmentStatus,
    $previous_org,
    $job_title,
    $summary,
    $target,
    $status
);

$stmt->execute();

/* ================= SEND EMAIL ================= */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'propermail017@gmail.com';
    $mail->Password = 'wodo jgie qjva xtlm';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('propermail017@gmail.com', 'HR Team');
    $mail->addAddress($email, $full_name);

    $mail->isHTML(true);
    $mail->Subject = 'Application Received';

    $mail->Body = "
        <h3>Thank you, $full_name 👋</h3>
        <p>Your job application has been <b>successfully submitted</b>.</p>
        <p>Our HR team will review your profile and contact you if shortlisted.</p>
        <br>
        <small>HR Team</small>
    ";

    $mail->AltBody = "Your job application has been submitted successfully.";
    $mail->send();

} catch (Exception $e) {
    // silently fail
}

/* ================= REDIRECT ================= */
header("Location: success.php");
exit;
