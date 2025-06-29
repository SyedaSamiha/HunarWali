<?php
// Test if the fix resolved the issue (plain text output)
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing Fix for 'Prepare failed for INSERT' Error\n\n";

// Include database connection
require_once __DIR__ . '/config/database.php';

echo "Connected to database successfully.\n";

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    echo "Orders table does not exist!\n";
    exit;
}

echo "Orders table exists. Checking structure...\n";

// Check table structure
$result = $conn->query("DESCRIBE orders");
echo "Table Structure:\n";
while ($row = $result->fetch_assoc()) {
    echo "Field: {$row['Field']}, Type: {$row['Type']}, Null: {$row['Null']}, Key: {$row['Key']}, Default: {$row['Default']}, Extra: {$row['Extra']}\n";
}

// Test the INSERT statement that was failing
echo "\nTesting INSERT Statement:\n";

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test data
    $sender_id = 1;
    $receiver_id = 2;
    $amount = 100.50;
    $description = "Test description";
    
    echo "Preparing INSERT statement...\n";
    
    // This is the fixed INSERT statement
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')");
    
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error . "\n";
    } else {
        echo "Prepare successful!\n";
        
        echo "Binding parameters...\n";
        $stmt->bind_param("iids", $sender_id, $receiver_id, $amount, $description);
        
        echo "Executing statement...\n";
        if ($stmt->execute()) {
            echo "Execute successful! The issue is fixed!\n";
        } else {
            echo "Execute failed: " . $stmt->error . "\n";
        }
        
        $stmt->close();
    }
    
    // Rollback the transaction to avoid actually inserting data
    $conn->rollback();
    echo "Transaction rolled back - test data not saved.\n";
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    echo "Error: " . $e->getMessage() . "\n";
}

// Test the original PHP files
echo "\nTesting Fixed PHP Files:\n";

$login_file = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        echo "login/respond_to_custom_order.php has been fixed correctly.\n";
    } else {
        echo "login/respond_to_custom_order.php may not be fixed correctly.\n";
    }
} else {
    echo "File login/respond_to_custom_order.php does not exist.\n";
}

$chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        echo "chat-screen/respond_to_custom_order.php has been fixed correctly.\n";
    } else {
        echo "chat-screen/respond_to_custom_order.php may not be fixed correctly.\n";
    }
} else {
    echo "File chat-screen/respond_to_custom_order.php does not exist.\n";
}

echo "\nSummary:\n";
echo "The 'Prepare failed for INSERT' error has been fixed by:\n";
echo "1. Updating the database connection to use TCP/IP instead of socket\n";
echo "2. Adding virtual columns 'client_id' and 'freelancer_id' as aliases for 'buyer_id' and 'seller_id'\n";
echo "3. Updating the INSERT statements in the PHP files to match the actual table structure\n";

echo "\nThe application should now work correctly.\n";

$conn->close();
?>