<?php
session_start();
// Check if the user is logged in as an admin
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

// Redirect to services page since Sub Services functionality has been removed
header("Location: services.php");
exit();
?> 