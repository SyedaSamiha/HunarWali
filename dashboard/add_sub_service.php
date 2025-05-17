<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = new mysqli("localhost", "root", "", "freelance_website");
    
    $service_id = (int)$_POST['service_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "INSERT INTO sub_services (service_id, name, description, status, created_at) 
            VALUES ($service_id, '$name', '$description', '$status', NOW())";
    
    if ($conn->query($sql)) {
        header("Location: sub_services.php?success=1");
    } else {
        header("Location: sub_services.php?error=1");
    }
    
    $conn->close();
} else {
    header("Location: sub_services.php");
}
?> 