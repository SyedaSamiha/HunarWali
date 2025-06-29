<?php
// Fix Orders Table and Update PHP Files
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fix Orders Table</h2>";

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
    log_message("Orders table does not exist. Creating it now...");
    
    // Create orders table
    $sql = "CREATE TABLE orders (
        id INT(11) NOT NULL AUTO_INCREMENT,
        buyer_id INT(11) NOT NULL,
        seller_id INT(11) NOT NULL,
        gig_id INT(11) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        status ENUM('Order Placed', 'In Progress', 'Pending Review', 'Completed') NOT NULL DEFAULT 'Order Placed',
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY buyer_id (buyer_id),
        KEY seller_id (seller_id),
        KEY gig_id (gig_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    if ($conn->query($sql)) {
        log_message("Orders table created successfully.");
    } else {
        log_message("Error creating orders table: " . $conn->error);
    }
} else {
    log_message("Orders table already exists.");
    
    // Check table structure
    $result = $conn->query("DESCRIBE orders");
    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row;
    }
    
    log_message("Current table structure:");
    echo "<pre>" . print_r($columns, true) . "</pre>";
    
    // Check if we need to add client_id and freelancer_id columns
    $alterNeeded = false;
    
    if (!isset($columns['client_id']) && !isset($columns['freelancer_id'])) {
        log_message("Adding client_id and freelancer_id columns as aliases...");
        
        // Add the columns as aliases
        $sql = "ALTER TABLE orders 
                ADD COLUMN client_id INT(11) AS (buyer_id) VIRTUAL,
                ADD COLUMN freelancer_id INT(11) AS (seller_id) VIRTUAL";
        
        if ($conn->query($sql)) {
            log_message("Added virtual columns successfully.");
        } else {
            log_message("Error adding virtual columns: " . $conn->error);
            log_message("Trying alternative approach...");
            
            // Some MySQL versions don't support virtual columns, so let's try triggers
            $conn->query("DROP TRIGGER IF EXISTS orders_insert_trigger");
            $conn->query("DROP TRIGGER IF EXISTS orders_update_trigger");
            
            $createTriggerInsert = "CREATE TRIGGER orders_insert_trigger BEFORE INSERT ON orders
                                    FOR EACH ROW
                                    BEGIN
                                        IF NEW.client_id IS NOT NULL AND NEW.buyer_id IS NULL THEN
                                            SET NEW.buyer_id = NEW.client_id;
                                        END IF;
                                        IF NEW.freelancer_id IS NOT NULL AND NEW.seller_id IS NULL THEN
                                            SET NEW.seller_id = NEW.freelancer_id;
                                        END IF;
                                    END";
            
            $createTriggerUpdate = "CREATE TRIGGER orders_update_trigger BEFORE UPDATE ON orders
                                    FOR EACH ROW
                                    BEGIN
                                        IF NEW.client_id IS NOT NULL AND NEW.client_id != OLD.client_id THEN
                                            SET NEW.buyer_id = NEW.client_id;
                                        END IF;
                                        IF NEW.freelancer_id IS NOT NULL AND NEW.freelancer_id != OLD.freelancer_id THEN
                                            SET NEW.seller_id = NEW.freelancer_id;
                                        END IF;
                                    END";
            
            if ($conn->query($createTriggerInsert) && $conn->query($createTriggerUpdate)) {
                log_message("Created triggers to handle client_id and freelancer_id mapping.");
            } else {
                log_message("Error creating triggers: " . $conn->error);
            }
        }
    } else {
        log_message("client_id and freelancer_id columns already exist or are not needed.");
    }
}

// Now let's fix the PHP files
log_message("Fixing PHP files...");

// Fix login/respond_to_custom_order.php
$file_path = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    // Replace the problematic INSERT statement
    $old_insert = "INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    $new_insert = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')";
    
    // Replace the bind_param statement
    $old_bind = "\$stmt->bind_param(\"iidsi\", 
            \$sender_id, 
            \$receiver_id, 
            \$amount,
            \$description,
            \$delivery_time
        )";
    
    $new_bind = "\$stmt->bind_param(\"iids\", 
            \$sender_id, 
            \$receiver_id, 
            \$amount,
            \$description
        )";
    
    $content = str_replace($old_insert, $new_insert, $content);
    $content = str_replace($old_bind, $new_bind, $content);
    
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
    
    // Replace the problematic INSERT statement
    $old_insert = "INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())";
    $new_insert = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')";
    
    // Replace the bind_param statement
    $old_bind = "\$stmt->bind_param(\"iidsi\", 
            \$sender_id, 
            \$receiver_id, 
            \$amount,
            \$description,
            \$delivery_time
        )";
    
    $new_bind = "\$stmt->bind_param(\"iids\", 
            \$sender_id, 
            \$receiver_id, 
            \$amount,
            \$description
        )";
    
    $content = str_replace($old_insert, $new_insert, $content);
    $content = str_replace($old_bind, $new_bind, $content);
    
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
    
    // Test INSERT with the new structure
    $sender_id = 1;
    $receiver_id = 2;
    $amount = 100.50;
    $description = "Test description";
    
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')");
    if (!$stmt) {
        throw new Exception("Prepare failed for INSERT: " . $conn->error);
    }
    
    $stmt->bind_param("iids", $sender_id, $receiver_id, $amount, $description);
    
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
    if ($conn->ping()) {
        $conn->rollback();
    }
    log_message("Error during testing: " . $e->getMessage());
}

$conn->close();
?>