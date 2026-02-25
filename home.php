<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Portal</title>

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

    /* animated gradient */
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

/* glowing bubbles */
.glow{
    position:absolute;
    border-radius:50%;
    filter:blur(50px);
    opacity:0.6;
    animation:float 6s ease-in-out infinite;
    z-index:0;
}

.glow.one{
    width:250px;height:250px;
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

/* main card */
.card-box{
    background:rgba(255,255,255,0.92);
    backdrop-filter:blur(18px);
    padding:45px;
    border-radius:22px;
    width:440px;
    text-align:center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.35);
    position:relative;
    z-index:2;

    animation:cardIn 0.9s ease;
}

@keyframes cardIn{
    from{opacity:0;transform:translateY(30px) scale(.96);}
    to{opacity:1;transform:translateY(0px) scale(1);}
}

h2{
    font-weight:600;
    font-size:26px;
    margin-bottom:10px;
}

p{
    font-size:15px;
}

/* button style */
.btn-main{
    height:58px;
    font-size:18px;
    border-radius:16px;
    font-weight:600;
    transition:all 0.35s ease;
    position:relative;
    overflow:hidden;
}

/* hover effects */
.btn-main:hover{
    transform:translateY(-4px);
    box-shadow:0 12px 30px rgba(0,0,0,0.25);
}

/* ripple effect */
.btn-main::after{
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

.btn-main:active::after{
    width:250px;
    height:250px;
    opacity:0;
}

/* auto glow border */
.card-box::before{
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

/* small fade-in for buttons */
.btn-main{
    animation:btnFade 1.1s ease;
}

@keyframes btnFade{
    from{opacity:0;transform:translateY(15px);}
    to{opacity:1;transform:translateY(0px);}
}
</style>
</head>

<body>

<!-- Background glow effects -->
<div class="glow one"></div>
<div class="glow two"></div>
<div class="glow three"></div>

<div class="card-box">
    <h2>💼 Job Application Portal</h2>
    <p class="text-muted mb-4">Choose how you want to continue</p>

    <a href="index.php" class="btn btn-primary btn-main w-100 mb-3">
        👤 Apply for Job
    </a>

    <a href="admin_login.php" class="btn btn-dark btn-main w-100 mb-3">
        🔐 Admin Login
    </a>

    <a href="staff_login.php" class="btn btn-success btn-main w-100">
        🧑‍💼 Staff Login
    </a>
</div>

</body>
</html>
