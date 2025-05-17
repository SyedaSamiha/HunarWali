<?php
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "freelance_website";  // Database name

$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);  // If there is a DB connection issue
}
?>
