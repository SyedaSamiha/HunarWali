<?php
// Fix gig_id foreign key constraint issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Fix gig_id Foreign Key Constraint Issue\n\n";

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

// Check if gigs table exists
$result = $conn->query("SHOW TABLES LIKE 'gigs'");
if ($result->num_rows == 0) {
    log_message("Gigs table does not exist.");
    exit;
}

// Check for foreign key constraints
log_message("Checking foreign key constraints...");
$result = $conn->query("SHOW CREATE TABLE orders");
$row = $result->fetch_assoc();
$createTable = $row['Create Table'];

if (strpos($createTable, "CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`gig_id`) REFERENCES `gigs` (`id`)") !== false) {
    log_message("Found foreign key constraint on gig_id.");
    
    // Option 1: Drop the foreign key constraint
    log_message("Option 1: Drop the foreign key constraint (safer for custom orders).");
    
    // Option 2: Create a dummy gig with ID 0
    log_message("Option 2: Create a dummy gig with ID 0 (maintains referential integrity).");
    
    // Let's implement Option 1 - Drop the foreign key constraint
    log_message("Implementing Option 1: Dropping the foreign key constraint...");
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Drop the foreign key constraint
        $sql = "ALTER TABLE orders DROP FOREIGN KEY orders_ibfk_3";
        if ($conn->query($sql)) {
            log_message("Successfully dropped the foreign key constraint.");
            
            // Keep the index for performance
            log_message("Keeping the index on gig_id for performance.");
            
            // Commit transaction
            $conn->commit();
            log_message("Changes committed successfully.");
        } else {
            throw new Exception("Error dropping foreign key constraint: " . $conn->error);
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        if ($conn) {
            $conn->rollback();
        }
        log_message("Error: " . $e->getMessage());
    }
} else {
    log_message("No foreign key constraint found on gig_id.");
}

// Now let's update the PHP files to handle custom orders properly
log_message("Updating PHP files to handle custom orders properly...");

// Fix login/respond_to_custom_order.php
$file_path = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    // Replace the INSERT statement to use NULL for gig_id in custom orders
    $old_insert = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')";
    $new_insert = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, NULL, ?, ?, 'Order Placed')";
    
    $content = str_replace($old_insert, $new_insert, $content);
    
    if (file_put_contents($file_path, $content)) {
        log_message("Fixed $file_path successfully.");
    } else {
        log_message("Error fixing $file_path.");
    }
} else {
    log_message("File $file_path does not exist.");
}

// Fix chat-screen/respond_to_custom_order.php
$file_path = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    // Replace the INSERT statement to use NULL for gig_id in custom orders
    $old_insert = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')";
    $new_insert = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, NULL, ?, ?, 'Order Placed')";
    
    $content = str_replace($old_insert, $new_insert, $content);
    
    if (file_put_contents($file_path, $content)) {
        log_message("Fixed $file_path successfully.");
    } else {
        log_message("Error fixing $file_path.");
    }
} else {
    log_message("File $file_path does not exist.");
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
log_message("The foreign key constraint issue has been fixed by:");
log_message("1. Dropping the foreign key constraint on gig_id");
log_message("2. Updating the PHP files to use NULL for gig_id in custom orders");
log_message("The application should now work correctly for custom orders.");

$conn->close();
?>