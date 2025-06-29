<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug INSERT Statement</h2>";

// Include database connection
require_once 'config/database.php';

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
    
    // Show table structure
    $result = $conn->query("DESCRIBE orders");
    echo "<h3>Orders Table Structure:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    
    // Test the exact INSERT statement from respond_to_offer.php
    echo "<h3>Testing INSERT Statement</h3>";
    
    // Start transaction to avoid permanent changes
    $conn->begin_transaction();
    
    try {
        // Test with different variations of the INSERT statement
        
        // Version 1: With created_at
        echo "<h4>Version 1: With created_at</h4>";
        $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        if (!$stmt) {
            echo "<p style='color: red;'>✗ Prepare failed: " . $conn->error . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Prepare successful</p>";
            
            // Sample data
            $client_id = 1;
            $freelancer_id = 2;
            $amount = 100.50;
            $description = "Test order description";
            $delivery_time = 3;
            
            echo "<p>Binding parameters: client_id=$client_id, freelancer_id=$freelancer_id, amount=$amount, description='$description', delivery_time=$delivery_time</p>";
            
            $stmt->bind_param("iidsi", $client_id, $freelancer_id, $amount, $description, $delivery_time);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Execute successful</p>";
            } else {
                echo "<p style='color: red;'>✗ Execute failed: " . $stmt->error . "</p>";
            }
            
            $stmt->close();
        }
        
        // Version 2: Without created_at
        echo "<h4>Version 2: Without created_at</h4>";
        $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        if (!$stmt) {
            echo "<p style='color: red;'>✗ Prepare failed: " . $conn->error . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Prepare successful</p>";
            
            // Sample data
            $client_id = 1;
            $freelancer_id = 2;
            $amount = 100.50;
            $description = "Test order description";
            $delivery_time = 3;
            
            echo "<p>Binding parameters: client_id=$client_id, freelancer_id=$freelancer_id, amount=$amount, description='$description', delivery_time=$delivery_time</p>";
            
            $stmt->bind_param("iidsi", $client_id, $freelancer_id, $amount, $description, $delivery_time);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Execute successful</p>";
            } else {
                echo "<p style='color: red;'>✗ Execute failed: " . $stmt->error . "</p>";
            }
            
            $stmt->close();
        }
        
        // Version 3: With explicit column list matching bind_param types
        echo "<h4>Version 3: With explicit column list matching bind_param types</h4>";
        $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            echo "<p style='color: red;'>✗ Prepare failed: " . $conn->error . "</p>";
        } else {
            echo "<p style='color: green;'>✓ Prepare successful</p>";
            
            // Sample data
            $client_id = 1;
            $freelancer_id = 2;
            $amount = 100.50;
            $description = "Test order description";
            $delivery_time = 3;
            
            echo "<p>Binding parameters: client_id=$client_id, freelancer_id=$freelancer_id, amount=$amount, description='$description', delivery_time=$delivery_time</p>";
            
            $stmt->bind_param("iidsi", $client_id, $freelancer_id, $amount, $description, $delivery_time);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Execute successful</p>";
            } else {
                echo "<p style='color: red;'>✗ Execute failed: " . $stmt->error . "</p>";
            }
            
            $stmt->close();
        }
        
        // Rollback to avoid saving test data
        $conn->rollback();
        echo "<p>Transaction rolled back - test data not saved</p>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Orders table does not exist</p>";
    
    // Show all tables in the database
    $result = $conn->query("SHOW TABLES");
    echo "<h3>Available Tables:</h3>";
    echo "<ul>";
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_row()) {
            echo "<li>{$row[0]}</li>";
        }
    } else {
        echo "<li>No tables found</li>";
    }
    echo "</ul>";
    
    echo "<p>Please run create_orders_table.php to create the orders table.</p>";
}

// Close the connection
$conn->close();
?>