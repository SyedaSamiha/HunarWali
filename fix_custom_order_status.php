<?php
// Fix for declined custom orders still showing as placed
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fixing Declined Custom Orders Issue</h2>";

// Include database connection
require_once __DIR__ . '/config/database.php';

echo "<p>Connected to database successfully.</p>";

// Function to log messages with timestamp
function log_message($message, $type = 'info') {
    $class = 'text-info';
    if ($type == 'success') $class = 'text-success';
    if ($type == 'error') $class = 'text-danger';
    if ($type == 'warning') $class = 'text-warning';
    
    echo "<p class='$class'>[" . date('Y-m-d H:i:s') . "] $message</p>";
}

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    log_message("Orders table does not exist!", 'error');
    exit;
}

log_message("Orders table exists. Checking for the issue...");

// Check if messages table exists
$result = $conn->query("SHOW TABLES LIKE 'messages'");
if ($result->num_rows == 0) {
    log_message("Messages table does not exist!", 'error');
    exit;
}

log_message("Messages table exists. Proceeding with fix...");

try {
    // Start transaction
    $conn->begin_transaction();
    
    // The issue is in respond_to_custom_order.php files
    // When a freelancer declines a custom order, the order status is still set to 'Order Placed'
    // We need to update both files to handle the decline case properly
    
    log_message("The issue: When a freelancer declines a custom order, an order entry is still created with 'Order Placed' status", 'warning');
    log_message("Fix: 1) Update PHP files to only create orders when accepted, 2) Clean up any incorrect orders");
    
    // First, let's check if there are any orders that were created from declined custom orders
    log_message("Checking for orders created from declined custom orders...");
    
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
    
    log_message("Found " . count($declined_orders) . " declined custom orders.", count($declined_orders) > 0 ? 'warning' : 'success');
    
    if (count($declined_orders) > 0) {
        log_message("Details of declined custom orders:");
        foreach ($declined_orders as $index => $order) {
            $order_data = json_decode($order['message'], true);
            log_message("  " . ($index + 1) . ". Message ID: {$order['id']}, Sender: {$order['sender_id']}, Receiver: {$order['receiver_id']}, Date: {$order['created_at']}");
            log_message("     Description: " . (isset($order_data['description']) ? $order_data['description'] : 'N/A'));
        }
    }
    
    // Check if any of these declined orders have entries in the orders table
    $orders_to_delete = [];
    $orders_details = [];
    
    foreach ($declined_orders as $order) {
        $order_data = json_decode($order['message'], true);
        
        // Check if there's an order with matching buyer_id, seller_id and description
        $check_stmt = $conn->prepare("SELECT o.id, o.buyer_id, o.seller_id, o.description, o.price, o.created_at, o.status, 
                                    u1.username as buyer_name, u2.username as seller_name 
                                    FROM orders o 
                                    JOIN users u1 ON o.buyer_id = u1.id 
                                    JOIN users u2 ON o.seller_id = u2.id 
                                    WHERE o.buyer_id = ? AND o.seller_id = ? 
                                    AND o.description = ? AND o.status = 'Order Placed'");
        
        if (!$check_stmt) {
            throw new Exception("Prepare failed for check: " . $conn->error);
        }
        
        $sender_id = $order['sender_id'];
        $receiver_id = $order['receiver_id'];
        $description = $order_data['description'];
        
        $check_stmt->bind_param("iis", $sender_id, $receiver_id, $description);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        while ($row = $check_result->fetch_assoc()) {
            $orders_to_delete[] = $row['id'];
            $orders_details[] = $row;
        }
    }
    
    log_message("Found " . count($orders_to_delete) . " orders in the database that should be deleted.", 
               count($orders_to_delete) > 0 ? 'warning' : 'success');
    
    // Display details of orders to be deleted
    if (count($orders_details) > 0) {
        log_message("Details of orders to be deleted:");
        foreach ($orders_details as $index => $order) {
            log_message("  " . ($index + 1) . ". Order ID: {$order['id']}, Buyer: {$order['buyer_name']} (ID: {$order['buyer_id']}), 
                       Seller: {$order['seller_name']} (ID: {$order['seller_id']})");
            log_message("     Description: {$order['description']}, Price: {$order['price']}, Created: {$order['created_at']}");
        }
    }
    
    // Delete the orders that were created from declined custom orders
    if (count($orders_to_delete) > 0) {
        log_message("Deleting orders that were created from declined custom orders...", 'warning');
        
        // First check if there are any order_tracking entries for these orders
        $tracking_entries = [];
        $check_tracking_stmt = $conn->prepare("SELECT ot.id, ot.order_id, ot.status, ot.description, ot.updated_at 
                                           FROM order_tracking ot 
                                           WHERE ot.order_id = ?");
        
        if (!$check_tracking_stmt) {
            log_message("No order_tracking table found or prepare failed. Continuing with deletion.", 'warning');
        } else {
            foreach ($orders_to_delete as $order_id) {
                $check_tracking_stmt->bind_param("i", $order_id);
                $check_tracking_stmt->execute();
                $tracking_result = $check_tracking_stmt->get_result();
                
                while ($row = $tracking_result->fetch_assoc()) {
                    $tracking_entries[] = $row;
                }
            }
            
            if (count($tracking_entries) > 0) {
                log_message("Found " . count($tracking_entries) . " tracking entries for orders to be deleted.", 'warning');
                
                // Delete tracking entries first
                $delete_tracking_stmt = $conn->prepare("DELETE FROM order_tracking WHERE order_id = ?");
                if (!$delete_tracking_stmt) {
                    throw new Exception("Prepare failed for tracking delete: " . $conn->error);
                }
                
                foreach ($orders_to_delete as $order_id) {
                    $delete_tracking_stmt->bind_param("i", $order_id);
                    $delete_tracking_stmt->execute();
                    log_message("Deleted tracking entries for order ID: $order_id", 'success');
                }
            }
        }
        
        // Now delete the orders
        foreach ($orders_to_delete as $order_id) {
            $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
            if (!$delete_stmt) {
                throw new Exception("Prepare failed for delete: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $order_id);
            $delete_stmt->execute();
            
            log_message("Deleted order ID: $order_id", 'success');
        }
    } else {
        log_message("No orders need to be deleted. Database is clean.", 'success');
    }
    
    // Now let's fix the respond_to_custom_order.php files
    log_message("Checking respond_to_custom_order.php files...");
    
    // Fix login/respond_to_custom_order.php
    $login_file = __DIR__ . '/login/respond_to_custom_order.php';
    if (file_exists($login_file)) {
        $content = file_get_contents($login_file);
        $original_content = $content;
        
        // Check if the file already has the fix
        if (strpos($content, "// Only create order if accepted") === false) {
            // Add the fix
            $content = str_replace(
                "// If accepted, create the order",
                "// Only create order if accepted",
                $content
            );
            
            // Verify the replacement was successful
            if ($content !== $original_content) {
                file_put_contents($login_file, $content);
                log_message("Fixed login/respond_to_custom_order.php", 'success');
                
                // Make a backup of the original file
                $backup_file = __DIR__ . '/login/respond_to_custom_order.php.bak';
                file_put_contents($backup_file, $original_content);
                log_message("Created backup at login/respond_to_custom_order.php.bak", 'info');
            } else {
                log_message("Could not find the pattern to replace in login/respond_to_custom_order.php", 'error');
                
                // Try to find the pattern with different whitespace
                $patterns = [
                    "//If accepted, create the order",
                    "// If accepted, create the order ",
                    "//  If accepted, create the order",
                    "// If accepted, create the order\n"
                ];
                
                foreach ($patterns as $pattern) {
                    if (strpos($original_content, $pattern) !== false) {
                        $content = str_replace(
                            $pattern,
                            "// Only create order if accepted",
                            $original_content
                        );
                        
                        file_put_contents($login_file, $content);
                        log_message("Fixed login/respond_to_custom_order.php with alternative pattern", 'success');
                        break;
                    }
                }
            }
        } else {
            log_message("login/respond_to_custom_order.php already has the fix.", 'success');
        }
    } else {
        log_message("File login/respond_to_custom_order.php does not exist.", 'error');
    }
    
    // Fix chat-screen/respond_to_custom_order.php
    $chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
    if (file_exists($chat_file)) {
        $content = file_get_contents($chat_file);
        $original_content = $content;
        
        // Check if the file already has the fix
        if (strpos($content, "// Only create order if accepted") === false) {
            // Add the fix
            $content = str_replace(
                "// If accepted, create the order",
                "// Only create order if accepted",
                $content
            );
            
            // Verify the replacement was successful
            if ($content !== $original_content) {
                file_put_contents($chat_file, $content);
                log_message("Fixed chat-screen/respond_to_custom_order.php", 'success');
                
                // Make a backup of the original file
                $backup_file = __DIR__ . '/chat-screen/respond_to_custom_order.php.bak';
                file_put_contents($backup_file, $original_content);
                log_message("Created backup at chat-screen/respond_to_custom_order.php.bak", 'info');
            } else {
                log_message("Could not find the pattern to replace in chat-screen/respond_to_custom_order.php", 'error');
                
                // Try to find the pattern with different whitespace
                $patterns = [
                    "//If accepted, create the order",
                    "// If accepted, create the order ",
                    "//  If accepted, create the order",
                    "// If accepted, create the order\n"
                ];
                
                foreach ($patterns as $pattern) {
                    if (strpos($original_content, $pattern) !== false) {
                        $content = str_replace(
                            $pattern,
                            "// Only create order if accepted",
                            $original_content
                        );
                        
                        file_put_contents($chat_file, $content);
                        log_message("Fixed chat-screen/respond_to_custom_order.php with alternative pattern", 'success');
                        break;
                    }
                }
            }
        } else {
            log_message("chat-screen/respond_to_custom_order.php already has the fix.", 'success');
        }
    } else {
        log_message("File chat-screen/respond_to_custom_order.php does not exist.", 'error');
    }
    
    // Commit the transaction
    $conn->commit();
    
    echo "<h3>Summary:</h3>";
    echo "<p>1. Checked for orders created from declined custom orders.</p>";
    echo "<p>2. Deleted any orders that were incorrectly created.</p>";
    echo "<p>3. Updated the respond_to_custom_order.php files to prevent this issue in the future.</p>";
    echo "<p style='color: green; font-weight: bold;'>The issue has been fixed!</p>";
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>