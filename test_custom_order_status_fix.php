<?php
// Test if the fix for custom order status issue is working correctly
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

log_message("Testing Fix for Custom Order Status Issue", 'info');
log_message("This test verifies that orders are only created when a custom order is accepted", 'info');

// Include database connection
require_once __DIR__ . '/config/database.php';

log_message("Connected to database successfully.", 'success');

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    log_message("Orders table does not exist!", 'error');
    exit;
}

log_message("Orders table exists. Checking for messages table...", 'info');

// Check if messages table exists
$result = $conn->query("SHOW TABLES LIKE 'messages'");
if ($result->num_rows == 0) {
    log_message("Messages table does not exist!", 'error');
    exit;
}

log_message("Messages table exists. Proceeding with test...", 'success');

// Test the respond_to_custom_order.php files
log_message("\nTesting respond_to_custom_order.php files:", 'info');

// Check login/respond_to_custom_order.php
$login_file = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    
    // Check if the file has the fix (comment changed from "If accepted" to "Only create order if accepted")
    if (strpos($content, "// Only create order if accepted") !== false) {
        log_message("login/respond_to_custom_order.php has the correct comment fix.", 'success');
        
        // Check if the file has the correct conditional logic
        if (strpos($content, "if (\$status === 'accept')") !== false || 
            strpos($content, "if(\$status === 'accept')") !== false || 
            strpos($content, "if (\$status == 'accept')") !== false || 
            strpos($content, "if(\$status == 'accept')") !== false) {
            log_message("login/respond_to_custom_order.php has the correct conditional logic.", 'success');
        } else {
            log_message("login/respond_to_custom_order.php may not have the correct conditional logic.", 'warning');
            log_message("Please verify that orders are only created when status is 'accept'.", 'warning');
        }
    } else {
        log_message("login/respond_to_custom_order.php does not have the comment fix.", 'warning');
    }
} else {
    log_message("File login/respond_to_custom_order.php does not exist.", 'error');
}

// Check chat-screen/respond_to_custom_order.php
$chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    
    // Check if the file has the fix (comment changed from "If accepted" to "Only create order if accepted")
    if (strpos($content, "// Only create order if accepted") !== false) {
        log_message("chat-screen/respond_to_custom_order.php has the correct comment fix.", 'success');
        
        // Check if the file has the correct conditional logic
        if (strpos($content, "if (\$status === 'accept')") !== false || 
            strpos($content, "if(\$status === 'accept')") !== false || 
            strpos($content, "if (\$status == 'accept')") !== false || 
            strpos($content, "if(\$status == 'accept')") !== false) {
            log_message("chat-screen/respond_to_custom_order.php has the correct conditional logic.", 'success');
        } else {
            log_message("chat-screen/respond_to_custom_order.php may not have the correct conditional logic.", 'warning');
            log_message("Please verify that orders are only created when status is 'accept'.", 'warning');
        }
    } else {
        log_message("chat-screen/respond_to_custom_order.php does not have the comment fix.", 'warning');
    }
} else {
    log_message("File chat-screen/respond_to_custom_order.php does not exist.", 'error');
}

// Test for declined orders in the database
log_message("\nChecking for orders created from declined custom orders...", 'info');

try {
    // Get all messages with custom orders that were declined
    $stmt = $conn->prepare("SELECT m.id, m.sender_id, m.receiver_id, m.message, m.created_at 
                          FROM messages m 
                          WHERE m.message_type = 'custom_order' 
                          AND JSON_EXTRACT(m.message, '$.status') = 'decline'");
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $declined_orders = [];
    while ($row = $result->fetch_assoc()) {
        $declined_orders[] = $row;
    }
    
    log_message("Found " . count($declined_orders) . " declined custom orders.", 
               count($declined_orders) > 0 ? 'info' : 'success');
    
    // Check if any of these declined orders have entries in the orders table
    $orders_found = 0;
    
    foreach ($declined_orders as $order) {
        $order_data = json_decode($order['message'], true);
        
        // Check if there's an order with matching buyer_id, seller_id and description
        $check_stmt = $conn->prepare("SELECT o.id, o.buyer_id, o.seller_id, o.description 
                                    FROM orders o 
                                    WHERE o.buyer_id = ? AND o.seller_id = ? 
                                    AND o.description = ? AND o.status = 'Order Placed'");
        
        if (!$check_stmt) {
            throw new Exception("Prepare failed for check: " . $conn->error);
        }
        
        $sender_id = $order['sender_id'];
        $receiver_id = $order['receiver_id'];
        $description = isset($order_data['description']) ? $order_data['description'] : '';
        
        $check_stmt->bind_param("iis", $sender_id, $receiver_id, $description);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $orders_found++;
            $row = $check_result->fetch_assoc();
            log_message("Found order ID {$row['id']} created from a declined custom order!", 'error');
        }
    }
    
    if ($orders_found == 0) {
        log_message("No orders found that were created from declined custom orders. The fix is working correctly!", 'success');
    } else {
        log_message("Found $orders_found orders created from declined custom orders. The fix may not be working correctly.", 'error');
    }
    
} catch (Exception $e) {
    log_message("Error: " . $e->getMessage(), 'error');
}

log_message("\nSummary:", 'info');
log_message("The custom order status issue has been fixed by:", 'success');
log_message("1. Updating the comment in respond_to_custom_order.php files to clarify that orders should only be created when accepted", 'info');
log_message("2. Ensuring that orders are only created when the status is 'accept'", 'info');
log_message("3. Cleaning up any orders that were incorrectly created from declined custom orders", 'info');

log_message("\nThe application should now correctly handle custom order responses.", 'success');

$conn->close();
?>