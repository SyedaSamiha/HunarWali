<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config/database.php';

// Function to log messages with timestamp
function log_message($message, $type = 'info') {
    $prefix = "";
    switch ($type) {
        case 'success':
            $prefix = "[SUCCESS] ";
            break;
        case 'error':
            $prefix = "[ERROR] ";
            break;
        case 'warning':
            $prefix = "[WARNING] ";
            break;
        default:
            $prefix = "[INFO] ";
    }
    echo $prefix . "[" . date('Y-m-d H:i:s') . "] $message\n";
}

log_message("Starting initialization of order_tracking table", 'info');

// Check if order_tracking table exists, create if not
try {
    $create_table_query = "CREATE TABLE IF NOT EXISTS order_tracking (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        status VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        updated_by INT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (updated_by) REFERENCES users(id)
    )";

    if (!$conn->query($create_table_query)) {
        throw new Exception("Error creating order_tracking table: " . $conn->error);
    }

    // Check and create indexes
    $index_queries = [
        'idx_order_tracking_order_id' => "CREATE INDEX idx_order_tracking_order_id ON order_tracking(order_id)",
        'idx_order_tracking_updated_at' => "CREATE INDEX idx_order_tracking_updated_at ON order_tracking(updated_at)"
    ];

    foreach ($index_queries as $index_name => $index_query) {
        $check_index_sql = "SHOW INDEX FROM order_tracking WHERE Key_name = '$index_name'";
        $result = $conn->query($check_index_sql);
        if ($result && $result->num_rows == 0) {
            if (!$conn->query($index_query)) {
                log_message("Warning: Could not create index $index_name: " . $conn->error, 'warning');
            } else {
                log_message("Created index $index_name", 'success');
            }
        }
    }

    log_message("Order tracking table and indexes are ready", 'success');

    // Get all orders that don't have tracking entries
    $query = "SELECT o.*, 
              CASE WHEN g.user_id IS NULL THEN o.seller_id ELSE g.user_id END as freelancer_id 
              FROM orders o 
              LEFT JOIN gigs g ON o.gig_id = g.id 
              WHERE NOT EXISTS (
                  SELECT 1 FROM order_tracking ot WHERE ot.order_id = o.id
              )";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Error fetching orders: " . $conn->error);
    }
    
    $total_orders = $result->num_rows;
    log_message("Found $total_orders orders without tracking entries", 'info');
    
    if ($total_orders > 0) {
        // Prepare insert statement
        $insert_query = "INSERT INTO order_tracking (order_id, status, description, updated_by) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        
        if (!$insert_stmt) {
            throw new Exception("Error preparing insert statement: " . $conn->error);
        }
        
        $count = 0;
        while ($order = $result->fetch_assoc()) {
            $description = "Order was placed";
            $insert_stmt->bind_param("issi", $order['id'], $order['status'], $description, $order['freelancer_id']);
            
            if ($insert_stmt->execute()) {
                $count++;
            } else {
                log_message("Error adding tracking for order {$order['id']}: " . $insert_stmt->error, 'error');
            }
        }
        
        log_message("Successfully added tracking entries for $count orders", 'success');
    }
    
} catch (Exception $e) {
    log_message("Error: " . $e->getMessage(), 'error');
}

log_message("Initialization complete", 'info');
?>