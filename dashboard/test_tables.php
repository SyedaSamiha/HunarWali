<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once '../config/database.php';

echo "<h2>Database Table Structure Test</h2>";

// Test database connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Test users table
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Users table exists</p>";
    
    // Check users table structure
    $result = $conn->query("DESCRIBE users");
    echo "<h3>Users Table Structure:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Users table does not exist</p>";
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

// Test messages table
$result = $conn->query("SHOW TABLES LIKE 'messages'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Messages table exists</p>";
    
    // Check messages table structure
    $result = $conn->query("DESCRIBE messages");
    echo "<h3>Messages Table Structure:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Messages table does not exist</p>";
}

// Test gigs table
$result = $conn->query("SHOW TABLES LIKE 'gigs'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Gigs table exists</p>";
    
    // Check gigs table structure
    $result = $conn->query("DESCRIBE gigs");
    echo "<h3>Gigs Table Structure:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Gigs table does not exist</p>";
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

// Test reviews table
$result = $conn->query("SHOW TABLES LIKE 'reviews'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Reviews table exists</p>";
    
    // Check reviews table structure
    $result = $conn->query("DESCRIBE reviews");
    echo "<h3>Reviews Table Structure:</h3>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['Field']} - {$row['Type']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ Reviews table does not exist</p>";
}

// Test if there are any users
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
echo "<p>Number of users in database: {$row['count']}</p>";

// Test if there are any orders
$result = $conn->query("SELECT COUNT(*) as count FROM orders");
$row = $result->fetch_assoc();
echo "<p>Number of orders in database: {$row['count']}</p>";

// Test if there are any messages
$result = $conn->query("SELECT COUNT(*) as count FROM messages");
$row = $result->fetch_assoc();
echo "<p>Number of messages in database: {$row['count']}</p>";

// Test if there are any gigs
$result = $conn->query("SELECT COUNT(*) as count FROM gigs");
$row = $result->fetch_assoc();
echo "<p>Number of gigs in database: {$row['count']}</p>";

echo "<hr>";
echo "<p><a href='admin.php'>Back to Admin Dashboard</a></p>";
?> 