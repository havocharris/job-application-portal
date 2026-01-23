<?php
$conn = new mysqli("localhost", "root", "", "job_portal", 3307);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>