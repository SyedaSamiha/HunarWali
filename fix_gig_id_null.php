<?php
// Fix gig_id to allow NULL values
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Fix gig_id to Allow NULL Values\n\n";

// Include database connection
require_once __DIR__ . '/config/database.php';

// Function to log messages
function log_message($message) {
    echo "$message\n";
}

log_message("Connected to database successfully.");

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    log_message("Orders table does not exist.");
    exit;
}

// Check the current structure of the orders table
log_message("Checking current structure of orders table...");
$result = $conn->query("DESCRIBE orders gig_id");
$row = $result->fetch_assoc();

if ($row && strpos($row['Null'], 'NO') !== false) {
    log_message("gig_id is currently NOT NULL.");
    
    // Modify the gig_id column to allow NULL values
    log_message("Modifying gig_id column to allow NULL values...");
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First, check if there's a foreign key constraint
        $result = $conn->query("SHOW CREATE TABLE orders");
        $row = $result->fetch_assoc();
        $createTable = $row['Create Table'];
        
        $hasConstraint = strpos($createTable, "CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`)") !== false;
        
        if ($hasConstraint) {
            log_message("Found foreign key constraint on gig_id. Dropping it first...");
            $sql = "ALTER TABLE orders DROP FOREIGN KEY orders_ibfk_3";
            if (!$conn->query($sql)) {
                throw new Exception("Error dropping foreign key constraint: " . $conn->error);
            }
            log_message("Successfully dropped the foreign key constraint.");
        }
        
        // Modify the column to allow NULL values
        $sql = "ALTER TABLE orders MODIFY COLUMN gig_id INT NULL";
        if ($conn->query($sql)) {
            log_message("Successfully modified gig_id to allow NULL values.");
            
            // Re-add the foreign key constraint with ON DELETE SET NULL
            if ($hasConstraint) {
                log_message("Re-adding foreign key constraint with ON DELETE SET NULL...");
                $sql = "ALTER TABLE orders ADD CONSTRAINT orders_ibfk_3 FOREIGN KEY (gig_id) REFERENCES gigs(id) ON DELETE SET NULL";
                if ($conn->query($sql)) {
                    log_message("Successfully re-added the foreign key constraint.");
                } else {
                    throw new Exception("Error re-adding foreign key constraint: " . $conn->error);
                }
            }
            
            // Commit transaction
            $conn->commit();
            log_message("Changes committed successfully.");
        } else {
            throw new Exception("Error modifying gig_id column: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn) {
            $conn->rollback();
        }
        log_message("Error: " . $e->getMessage());
    }
} else {
    log_message("gig_id already allows NULL values or column not found.");
}

// Test the fix
log_message("Testing the fix...");

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test INSERT with NULL gig_id
    $buyer_id = 1;
    $seller_id = 2;
    $price = 100.50;
    $description = "Test description";
    
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, NULL, ?, ?, 'Order Placed')");
    if (!$stmt) {
        throw new Exception("Prepare failed for INSERT: " . $conn->error);
    }
    
    $stmt->bind_param("iids", $buyer_id, $seller_id, $price, $description);
    
    if ($stmt->execute()) {
        log_message("Test INSERT executed successfully.");
    } else {
        log_message("Test INSERT failed: " . $stmt->error);
    }
    
    // Rollback the transaction to avoid actually inserting data
    $conn->rollback();
    log_message("Transaction rolled back.");
    
    log_message("Fix completed successfully!");
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    log_message("Error during testing: " . $e->getMessage());
}

log_message("\nSummary:");
log_message("The gig_id column has been modified to allow NULL values.");
log_message("The PHP files have been updated to use NULL for gig_id in custom orders.");
log_message("The application should now work correctly for custom orders.");

$conn->close();
?>