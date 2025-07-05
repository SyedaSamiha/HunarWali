<?php 

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


$stmt = $conn->prepare("SELECT * FROM `users`");
$stmt->exec();

// Bind result variables
$stmt->bind_result($id, $username, $email, $password, $role, $gender, $status);

// Fetch values
while ($stmt->fetch()) {
    echo "ID: $id, Username: $username, Email: $email, Role: $role, Gender: $gender, Status: $status<br>";
}

// Close the statement
$stmt->close();

// Close the connection
$conn->close();
?>