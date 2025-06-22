<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "freelance_website");
    
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "INSERT INTO services (name, description, status, created_at) VALUES ('$name', '$description', '$status', NOW())";
    
    if ($conn->query($sql)) {
        header("Location: services.php?success=1");
    } else {
        header("Location: services.php?error=1");
    }
    
    $conn->close();
} else {
    header("Location: services.php");
}
?> 