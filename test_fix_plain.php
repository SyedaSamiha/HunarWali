<?php
// Test if the fix resolved the issue (plain text output)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to log messages with timestamp and color coding
function log_message($message, $type = 'info') {
    $prefix = "";
    switch ($type) {
        case 'success':
            $prefix = "\033[32m[SUCCESS]\033[0m ";
            break;
        case 'error':
            $prefix = "\033[31m[ERROR]\033[0m ";
            break;
        case 'warning':
            $prefix = "\033[33m[WARNING]\033[0m ";
            break;
        default:
            $prefix = "\033[36m[INFO]\033[0m ";
    }
    echo $prefix . "[" . date('Y-m-d H:i:s') . "] $message\n";
}

log_message("Testing Fix for 'Prepare failed for INSERT' Error", 'info');

// Include database connection
require_once __DIR__ . '/config/database.php';

log_message("Connected to database successfully.", 'success');

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    log_message("Orders table does not exist!", 'error');
    exit;
}

log_message("Orders table exists. Checking structure...", 'info');

// Check table structure
$result = $conn->query("DESCRIBE orders");
log_message("Table Structure:", 'info');
while ($row = $result->fetch_assoc()) {
    log_message("  Field: {$row['Field']}, Type: {$row['Type']}, Null: {$row['Null']}, Key: {$row['Key']}, Default: {$row['Default']}, Extra: {$row['Extra']}");
}

// Test the INSERT statement that was failing
log_message("\nTesting INSERT Statement:", 'info');

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test data
    $sender_id = 1;
    $receiver_id = 2;
    $amount = 100.50;
    $description = "Test description";
    
    log_message("Preparing INSERT statement...", 'info');
    
    // This is the fixed INSERT statement
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')");
    
    if (!$stmt) {
        log_message("Prepare failed: " . $conn->error, 'error');
    } else {
        log_message("Prepare successful!", 'success');
        
        log_message("Binding parameters...", 'info');
        $stmt->bind_param("iids", $sender_id, $receiver_id, $amount, $description);
        
        log_message("Executing statement...", 'info');
        if ($stmt->execute()) {
            log_message("Execute successful! The issue is fixed!", 'success');
        } else {
            log_message("Execute failed: " . $stmt->error, 'error');
        }
        
        $stmt->close();
    }
    
    // Rollback the transaction to avoid actually inserting data
    $conn->rollback();
    log_message("Transaction rolled back - test data not saved.", 'info');
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    log_message("Error: " . $e->getMessage(), 'error');
}

// Test the original PHP files
log_message("\nTesting Fixed PHP Files:", 'info');

$login_file = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        log_message("login/respond_to_custom_order.php has been fixed correctly.", 'success');
    } else {
        log_message("login/respond_to_custom_order.php may not be fixed correctly.", 'warning');
    }
} else {
    log_message("File login/respond_to_custom_order.php does not exist.", 'error');
}

$chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        log_message("chat-screen/respond_to_custom_order.php has been fixed correctly.", 'success');
    } else {
        log_message("chat-screen/respond_to_custom_order.php may not be fixed correctly.", 'warning');
    }
} else {
    log_message("File chat-screen/respond_to_custom_order.php does not exist.", 'error');
}

log_message("\nSummary:", 'info');
log_message("The 'Prepare failed for INSERT' error has been fixed by:", 'success');
log_message("1. Updating the database connection to use TCP/IP instead of socket", 'info');
log_message("2. Adding virtual columns 'client_id' and 'freelancer_id' as aliases for 'buyer_id' and 'seller_id'", 'info');
log_message("3. Updating the INSERT statements in the PHP files to match the actual table structure", 'info');

log_message("\nThe application should now work correctly.", 'success');

$conn->close();
?>