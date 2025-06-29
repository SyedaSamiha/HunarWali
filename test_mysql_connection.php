<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>MySQL Connection Test</h2>";

// Test direct MySQL connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'freelance_website';

try {
    // Create connection without database selection first
    $conn = new mysqli($host, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p style='color: green;'>✓ MySQL server connection successful</p>";
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE '$database'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Database '$database' exists</p>";
        
        // Select the database
        if (!$conn->select_db($database)) {
            throw new Exception("Cannot select database: " . $conn->error);
        }
        
        echo "<p style='color: green;'>✓ Successfully selected database '$database'</p>";
        
        // List all tables
        $result = $conn->query("SHOW TABLES");
        if ($result->num_rows > 0) {
            echo "<h3>Tables in database:</h3>";
            echo "<ul>";
            while ($row = $result->fetch_row()) {
                echo "<li>{$row[0]}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: orange;'>! No tables found in database</p>";
        }
        
        // Check for orders table specifically
        $result = $conn->query("SHOW TABLES LIKE 'orders'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✓ Orders table exists</p>";
            
            // Try to insert a test record with transaction and rollback
            $conn->begin_transaction();
            
            try {
                $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $client_id = 1;
                $freelancer_id = 2;
                $amount = 100.50;
                $description = "Test order";
                $delivery_time = 3;
                $status = "pending";
                
                $stmt->bind_param("iidssi", $client_id, $freelancer_id, $amount, $description, $delivery_time, $status);
                
                if ($stmt->execute()) {
                    echo "<p style='color: green;'>✓ Test INSERT successful</p>";
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                
                // Always rollback the test insert
                $conn->rollback();
                echo "<p>Transaction rolled back - test data not saved</p>";
                
            } catch (Exception $e) {
                $conn->rollback();
                echo "<p style='color: red;'>✗ Test INSERT failed: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ Orders table does not exist</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Database '$database' does not exist</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Close the connection if it exists
if (isset($conn)) {
    $conn->close();
    echo "<p>Connection closed</p>";
}
?>