<?php
include "db.php";

$username = "admin";
$password = password_hash("Admin@123", PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

echo "Admin created successfully";
