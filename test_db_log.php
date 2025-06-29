<?php
// Database connection test with log file output
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set up log file
$log_file = __DIR__ . '/db_connection_test.log';
function log_message($message) {
    global $log_file;
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
}

// Clear previous log
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Starting database connection test\n");

// Database configuration
$host = '127.0.0.1'; // Use IP instead of 'localhost' to force TCP/IP
$port = 3306;        // Specify port explicitly
$username = 'root';
$password = '';
$database = 'freelance_website';

log_message("Database configuration: host=$host, port=$port, username=$username, database=$database");

try {
    // Try to connect without selecting a database first
    log_message("Attempting to connect to MySQL server...");
    $conn = new mysqli($host, $username, $password, "", $port);
    
    if ($conn->connect_error) {
        log_message("Connection failed: " . $conn->connect_error);
        echo "Connection failed. See log file for details.";
        exit;
    }
    
    log_message("Connected to MySQL server successfully!");
    
    // Check if database exists
    log_message("Checking if database '$database' exists...");
    $result = $conn->query("SHOW DATABASES LIKE '$database'");
    if ($result->num_rows > 0) {
        log_message("Database '$database' exists.");
        
        // Now try to select the database
        log_message("Attempting to select database '$database'...");
        if ($conn->select_db($database)) {
            log_message("Successfully selected database '$database'.");
            
            // Check if orders table exists
            log_message("Checking if table 'orders' exists...");
            $result = $conn->query("SHOW TABLES LIKE 'orders'");
            if ($result->num_rows > 0) {
                log_message("Table 'orders' exists.");
                
                // Show table structure
                log_message("Retrieving table structure...");
                $result = $conn->query("DESCRIBE orders");
                log_message("Table Structure:");
                while ($row = $result->fetch_assoc()) {
                    log_message("Field: {$row['Field']}, Type: {$row['Type']}, Null: {$row['Null']}, Key: {$row['Key']}, Default: {$row['Default']}, Extra: {$row['Extra']}");
                }
                
                // Test a simple prepared statement
                log_message("Testing a simple prepared statement...");
                $stmt = $conn->prepare("SELECT 1");
                if ($stmt) {
                    log_message("Simple prepared statement created successfully.");
                    $stmt->execute();
                    $stmt->close();
                } else {
                    log_message("Failed to create simple prepared statement. Error: " . $conn->error);
                }
                
                // Test a prepared statement with the orders table
                log_message("Testing a prepared statement with the orders table...");
                $stmt = $conn->prepare("SELECT * FROM orders LIMIT 1");
                if ($stmt) {
                    log_message("Prepared statement with orders table created successfully.");
                    $stmt->execute();
                    $stmt->close();
                } else {
                    log_message("Failed to create prepared statement with orders table. Error: " . $conn->error);
                }
                
                // Test the problematic INSERT statement
                log_message("Testing the problematic INSERT statement...");
                $conn->begin_transaction();
                
                $sender_id = 1;
                $receiver_id = 2;
                $amount = 100.50;
                $description = "Test description";
                $delivery_time = 5;
                
                log_message("Preparing INSERT statement...");
                $stmt = $conn->prepare("INSERT INTO orders (sender_id, receiver_id, amount, description, delivery_time) VALUES (?, ?, ?, ?, ?)");
                
                if ($stmt) {
                    log_message("INSERT statement prepared successfully.");
                    log_message("Binding parameters...");
                    $stmt->bind_param("iidsi", $sender_id, $receiver_id, $amount, $description, $delivery_time);
                    
                    log_message("Executing statement...");
                    if ($stmt->execute()) {
                        log_message("INSERT executed successfully.");
                    } else {
                        log_message("Failed to execute INSERT. Error: " . $stmt->error);
                    }
                    
                    $stmt->close();
                } else {
                    log_message("Failed to prepare INSERT statement. Error: " . $conn->error);
                }
                
                // Rollback the transaction to avoid actually inserting data
                log_message("Rolling back transaction...");
                $conn->rollback();
                
            } else {
                log_message("Table 'orders' does not exist.");
            }
        } else {
            log_message("Failed to select database '$database'. Error: " . $conn->error);
        }
    } else {
        log_message("Database '$database' does not exist.");
    }
    
    $conn->close();
    log_message("Connection closed.");
    
} catch (Exception $e) {
    log_message("Error: " . $e->getMessage());
}

echo "Database connection test completed. See log file for details.";
?>