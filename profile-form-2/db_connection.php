<?php
// Database connection details
$host = "localhost";  // Database host (usually 'localhost' for local development)
$dbuser = "root";     // Database username
$dbpass = "";         // Database password (empty for XAMPP's default)
$dbname = "freelance_website";  // The name of your database

// Create a connection to the database
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // Display error if the connection fails
}
?>
