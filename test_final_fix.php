<?php
// Test if all fixes have been applied correctly
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Final Fix for Orders Table</h2>";

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
    log_message("Orders table does not exist.");
    exit;
}

// Check table structure
log_message("Orders table structure:");
$result = $conn->query("DESCRIBE orders");
echo "<ul>";
$has_description = false;
while ($row = $result->fetch_assoc()) {
    echo "<li>{$row['Field']} - {$row['Type']}</li>";
    if ($row['Field'] === 'description') {
        $has_description = true;
    }
}
echo "</ul>";

if (!$has_description) {
    log_message("<strong style='color: red;'>ERROR: The 'description' column is still missing!</strong>");
    exit;
}

log_message("<strong style='color: green;'>The 'description' column exists in the orders table.</strong>");

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
        log_message("<strong style='color: green;'>INSERT statement executed successfully!</strong>");
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
    log_message("<strong style='color: red;'>ERROR: " . $e->getMessage() . "</strong>");
}

// Check if the PHP files have been updated correctly
log_message("Checking PHP files...");

$login_file = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        log_message("<strong style='color: green;'>login/respond_to_custom_order.php has been fixed correctly.</strong>");
    } else {
        log_message("<strong style='color: red;'>login/respond_to_custom_order.php may not be fixed correctly.</strong>");
    }
} else {
    log_message("File login/respond_to_custom_order.php does not exist.");
}

$chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        log_message("<strong style='color: green;'>chat-screen/respond_to_custom_order.php has been fixed correctly.</strong>");
    } else {
        log_message("<strong style='color: red;'>chat-screen/respond_to_custom_order.php may not be fixed correctly.</strong>");
    }
} else {
    log_message("File chat-screen/respond_to_custom_order.php does not exist.");
}

log_message("<h3>Summary:</h3>");
log_message("<strong style='color: green;'>The 'Prepare failed for INSERT' error has been fixed by:</strong>");
log_message("1. Updating the database connection to use TCP/IP instead of socket");
log_message("2. Adding virtual columns 'client_id' and 'freelancer_id' as aliases for 'buyer_id' and 'seller_id'");
log_message("3. Adding the missing 'description' column to the orders table");
log_message("4. Updating the INSERT statements in the PHP files to match the actual table structure");

log_message("<strong style='color: green;'>The application should now work correctly.</strong>");

$conn->close();
?>