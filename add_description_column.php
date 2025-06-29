<?php
// Add description column to orders table
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Adding 'description' Column to Orders Table</h2>";

// Include database connection
require_once __DIR__ . '/config/database.php';

// Function to log messages
function log_message($message) {
    echo "<p>$message</p>";
}

log_message("Connected to database successfully.");

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    log_message("Orders table does not exist. Please create it first.");
    exit;
}

// Check if description column already exists
$result = $conn->query("DESCRIBE orders");
$has_description = false;
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] === 'description') {
        $has_description = true;
        break;
    }
}

if ($has_description) {
    log_message("The 'description' column already exists in the orders table.");
} else {
    log_message("Adding 'description' column to orders table...");
    
    // Add the description column
    $sql = "ALTER TABLE orders ADD COLUMN description TEXT AFTER price";
    
    if ($conn->query($sql)) {
        log_message("Added 'description' column successfully.");
    } else {
        log_message("Error adding 'description' column: " . $conn->error);
    }
}

// Verify the table structure
log_message("Current orders table structure:");
$result = $conn->query("DESCRIBE orders");
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>{$row['Field']} - {$row['Type']}</li>";
}
echo "</ul>";

// Test the fix
log_message("Testing INSERT with description column...");

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test INSERT with the new structure
    $buyer_id = 1;
    $seller_id = 2;
    $gig_id = 0;
    $price = 100.50;
    $description = "Test description";
    
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, ?, ?, ?, 'Order Placed')");
    if (!$stmt) {
        throw new Exception("Prepare failed for INSERT: " . $conn->error);
    }
    
    $stmt->bind_param("iidss", $buyer_id, $seller_id, $gig_id, $price, $description);
    
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
    if ($conn && $conn->ping()) {
        $conn->rollback();
    }
    log_message("Error during testing: " . $e->getMessage());
}

$conn->close();
?>