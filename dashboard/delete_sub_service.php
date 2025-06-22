<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

if (isset($_GET['id'])) {
    $conn = new mysqli("localhost", "root", "", "freelance_website");
    $id = (int)$_GET['id'];
    
    $sql = "DELETE FROM services WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: services.php?success=3");
    } else {
        header("Location: services.php?error=3");
    }
    
    $conn->close();
} else {
    header("Location: services.php");
}
?> 