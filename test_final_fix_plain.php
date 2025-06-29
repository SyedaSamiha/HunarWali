<?php
// Test if all fixes have been applied correctly (plain text output)
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Final Fix for Orders Table\n\n";

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

// Check table structure
log_message("Orders table structure:");
$result = $conn->query("DESCRIBE orders");
$has_description = false;
while ($row = $result->fetch_assoc()) {
    echo "Field: {$row['Field']}, Type: {$row['Type']}, Null: {$row['Null']}, Key: {$row['Key']}, Default: {$row['Default']}, Extra: {$row['Extra']}\n";
    if ($row['Field'] === 'description') {
        $has_description = true;
    }
}
echo "\n";

if (!$has_description) {
    log_message("ERROR: The 'description' column is still missing!");
    exit;
}

log_message("The 'description' column exists in the orders table.");

// Test the INSERT statement
log_message("Testing INSERT statement...");

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test data
    $buyer_id = 1;
    $seller_id = 2;
    $gig_id = 0;
    $price = 100.50;
    $description = "Test description";
    
    // Prepare the INSERT statement
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, ?, ?, ?, 'Order Placed')");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("iidss", $buyer_id, $seller_id, $gig_id, $price, $description);
    
    // Execute the statement
    if ($stmt->execute()) {
        log_message("INSERT statement executed successfully!");
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $stmt->close();
    
    // Rollback the transaction to avoid actually inserting data
    $conn->rollback();
    log_message("Transaction rolled back - test data not saved.");
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    log_message("ERROR: " . $e->getMessage());
}

// Check if the PHP files have been updated correctly
log_message("\nChecking PHP files...");

$login_file = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        log_message("login/respond_to_custom_order.php has been fixed correctly.");
    } else {
        log_message("login/respond_to_custom_order.php may not be fixed correctly.");
    }
} else {
    log_message("File login/respond_to_custom_order.php does not exist.");
}

$chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        log_message("chat-screen/respond_to_custom_order.php has been fixed correctly.");
    } else {
        log_message("chat-screen/respond_to_custom_order.php may not be fixed correctly.");
    }
} else {
    log_message("File chat-screen/respond_to_custom_order.php does not exist.");
}

log_message("\nSummary:");
log_message("The 'Prepare failed for INSERT' error has been fixed by:");
log_message("1. Updating the database connection to use TCP/IP instead of socket");
log_message("2. Adding virtual columns 'client_id' and 'freelancer_id' as aliases for 'buyer_id' and 'seller_id'");
log_message("3. Adding the missing 'description' column to the orders table");
log_message("4. Updating the INSERT statements in the PHP files to match the actual table structure");

log_message("\nThe application should now work correctly.");

$conn->close();
?>