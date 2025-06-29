<?php
// Database configuration
$host = '127.0.0.1'; // Use IP instead of 'localhost' to force TCP/IP
$port = 3306;        // Specify port explicitly
$username = 'root';
$password = '';
$database = 'freelance_website';

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");
?>