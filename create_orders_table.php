<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config/database.php';

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>Error: The users table does not exist. Cannot create orders table with foreign key constraints.</p>";
    exit;
}

// SQL to create orders table without foreign key constraints initially
$sql = "CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  freelancer_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  description TEXT NOT NULL,
  delivery_time INT NOT NULL,
  status VARCHAR(50) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Execute the SQL
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>Orders table created successfully!</p>";
    
    // Check if the table exists and show its structure
    $result = $conn->query("DESCRIBE orders");
    
    if ($result) {
        echo "<h3>Orders Table Structure:</h3>";
        echo "<ul>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>{$row['Field']} - {$row['Type']}</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>Error creating orders table: " . $conn->error . "</p>";
    exit;
}

echo "<p>Orders table created successfully without foreign key constraints.</p>";

// Close the connection
$conn->close();
?>