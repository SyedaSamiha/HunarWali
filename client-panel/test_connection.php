<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/database.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Test orders table
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Orders table exists</p>";
    
    // Check orders table structure
    $result = $conn->query("DESCRIBE orders");
    echo "<h3>Orders Table Structure:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Orders table does not exist</p>";
}

// Test feedback table
$result = $conn->query("SHOW TABLES LIKE 'feedback'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Feedback table exists</p>";
    
    // Check feedback table structure
    $result = $conn->query("DESCRIBE feedback");
    echo "<h3>Feedback Table Structure:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Feedback table does not exist</p>";
}

// Test if there are any orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$row = $result->fetch_assoc();
echo "<p>Number of orders in database: {$row['count']}</p>";

// Test if there are any feedback entries
$result = $conn->query("SELECT COUNT(*) as count FROM feedback");
$row = $result->fetch_assoc();
echo "<p>Number of feedback entries in database: {$row['count']}</p>";
?> 