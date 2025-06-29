<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config/database.php';

echo "<h2>Test Order Insert</h2>";

// Test database connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Orders table exists</p>";
} else {
    echo "<p style='color: red;'>✗ Orders table does not exist</p>";
    exit;
}

// Test inserting a sample order
try {
    // Start transaction
    $conn->begin_transaction();
    
    // Sample data
    $client_id = 1; // Assuming user ID 1 exists
    $freelancer_id = 2; // Assuming user ID 2 exists
    $amount = 100.50;
    $description = "Test order description";
    $delivery_time = 3; // 3 days
    
    // Prepare and execute the insert statement
    $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("iidsi", $client_id, $freelancer_id, $amount, $description, $delivery_time);
    
    if ($stmt->execute()) {
        $order_id = $conn->insert_id;
        echo "<p style='color: green;'>✓ Test order inserted successfully with ID: {$order_id}</p>";
        
        // Retrieve the inserted order
        $result = $conn->query("SELECT * FROM orders WHERE id = {$order_id}");
        $order = $result->fetch_assoc();
        
        echo "<h3>Inserted Order:</h3>";
        echo "<ul>";
        foreach ($order as $key => $value) {
            echo "<li>{$key}: {$value}</li>";
        }
        echo "</ul>";
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Rollback the transaction to avoid keeping test data
    $conn->rollback();
    echo "<p>Transaction rolled back - test data not saved to database.</p>";
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->ping()) {
        $conn->rollback();
    }
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Close the connection
$conn->close();
?>