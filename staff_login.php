<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:Poppins, sans-serif;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    position:relative;

    /* animated background */
    background:linear-gradient(-45deg, #92a5f8, #764ba2, #4facfe, #00f2fe);
    background-size:400% 400%;
    animation:bgMove 10s ease infinite;
}

/* background animation */
@keyframes bgMove{
    0%{background-position:0% 50%;}
    50%{background-position:100% 50%;}
    100%{background-position:0% 50%;}
}

/* glow bubbles */
.glow{
    position:absolute;
    border-radius:50%;
    filter:blur(50px);
    opacity:0.6;
    animation:float 6s ease-in-out infinite;
    z-index:0;
}

.glow.one{
    width:240px;height:240px;
    background:#ff6ec4;
    top:10%;left:10%;
}

.glow.two{
    width:300px;height:300px;
    background:#6a11cb;
    bottom:10%;right:10%;
    animation-delay:1s;
}

.glow.three{
    width:200px;height:200px;
    background:#00f2fe;
    top:60%;left:45%;
    animation-delay:2s;
}

@keyframes float{
    0%,100%{transform:translateY(0px) scale(1);}
    50%{transform:translateY(-25px) scale(1.05);}
}

/* login card */
.login-card{
    width:400px;
    padding:35px;
    background:rgba(255,255,255,0.92);
    backdrop-filter:blur(18px);
    border-radius:22px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.35);
    text-align:center;
    position:relative;
    z-index:2;

    animation:cardIn 0.9s ease;
}

@keyframes cardIn{
    from{opacity:0;transform:translateY(30px) scale(.96);}
    to{opacity:1;transform:translateY(0px) scale(1);}
}

/* glowing border */
.login-card::before{
    content:"";
    position:absolute;
    inset:-2px;
    border-radius:24px;
    background:linear-gradient(90deg,#00f2fe,#764ba2,#ff6ec4);
    z-index:-1;
    filter:blur(12px);
    opacity:0.65;
    animation:borderGlow 3s linear infinite;
}

@keyframes borderGlow{
    0%{filter:blur(12px) hue-rotate(0deg);}
    100%{filter:blur(12px) hue-rotate(360deg);}
}

/* inputs */
.form-control{
    height:50px;
    border-radius:14px;
    border:1px solid rgba(0,0,0,0.12);
    transition:0.3s ease;
}

.form-control:focus{
    border-color:#667eea;
    box-shadow:0 0 0 4px rgba(102,126,234,0.25);
}

/* login button */
.btn-login{
    height:52px;
    border-radius:16px;
    font-size:18px;
    font-weight:600;
    border:none;
    background:linear-gradient(135deg,#667eea,#764ba2);
    transition:all 0.35s ease;
    position:relative;
    overflow:hidden;
}

/* hover effect */
.btn-login:hover{
    transform:translateY(-4px);
    box-shadow:0 15px 35px rgba(102,126,234,0.5);
}

/* ripple effect */
.btn-login::after{
    content:"";
    position:absolute;
    top:50%;
    left:50%;
    width:0;
    height:0;
    background:rgba(255,255,255,0.4);
    border-radius:50%;
    transform:translate(-50%,-50%);
    transition:0.6s;
}

.btn-login:active::after{
    width:250px;
    height:250px;
    opacity:0;
}

/* title animation */
h4{
    font-weight:600;
    font-size:24px;
    animation:fadeText 1s ease;
}

@keyframes fadeText{
    from{opacity:0;transform:translateY(-10px);}
    to{opacity:1;transform:translateY(0px);}
}
</style>
</head>

<body>

<!-- background glow effects -->
<div class="glow one"></div>
<div class="glow two"></div>
<div class="glow three"></div>

<div class="login-card">
    <h4 class="mb-4">🔐 Staff Login</h4>

    <?php if(isset($_SESSION['staff_error'])){ ?>
        <div class="alert alert-danger text-center">
            <?= $_SESSION['staff_error']; unset($_SESSION['staff_error']); ?>
        </div>
    <?php } ?>

    <form method="POST" action="staff_auth.php">
        <input class="form-control mb-3" name="username" placeholder="Username" required>
        <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>

        <button class="btn btn-login text-white w-100">Login</button>
    </form>
</div>

</body>
</html>
