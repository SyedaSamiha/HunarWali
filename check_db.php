<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = '';
$database = 'freelance_website';

// Create connection
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to $database database\n";

// Check messages table structure
echo "\nMessages table structure:\n";
$structure = $conn->query("DESCRIBE messages");
if ($structure) {
    while ($row = $structure->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . " - " . $row['Key'] . "\n";
    }
} else {
    echo "Error getting messages table structure: " . $conn->error . "\n";
}

$conn->close();
?>