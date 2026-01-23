<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
body{
    font-family:Poppins;
    min-height:100vh;
    background:linear-gradient(135deg,#667eea,#764ba2);
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-card{
    width:380px;
    padding:30px;
    background:rgba(255,255,255,0.85);
    backdrop-filter:blur(20px);
    border-radius:20px;
    box-shadow:0 40px 80px rgba(0,0,0,0.3);
    animation:slideIn .7s ease;
}

@keyframes slideIn{
    from{opacity:0;transform:translateY(30px)}
    to{opacity:1;transform:none}
}

.btn-login{
    background:linear-gradient(135deg,#667eea,#764ba2);
    border:none;
    height:48px;
    transition:.3s;
}
.btn-login:hover{
    transform:translateY(-3px);
    box-shadow:0 15px 30px rgba(102,126,234,.5);
}
</style>
</head>

<body>

<div class="login-card">
<h4 class="text-center mb-4">🔐 Admin Login</h4>

<?php session_start(); if(isset($_SESSION['login_error'])){ ?>
<div class="alert alert-danger text-center">
<?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
</div>
<?php } ?>

<form method="POST" action="admin_auth.php">
<input class="form-control mb-3" name="username" placeholder="Username" required>
<input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
<button class="btn btn-login text-white w-100">Login</button>
</form>
</div>

</body>
</html>
