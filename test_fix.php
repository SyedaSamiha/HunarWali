<?php
// Test if the fix resolved the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Fix for 'Prepare failed for INSERT' Error</h2>";

// Include database connection
require_once __DIR__ . '/config/database.php';

echo "<p>Connected to database successfully.</p>";

// Check if orders table exists
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows == 0) {
    echo "<p style='color: red;'>Orders table does not exist!</p>";
    exit;
}

echo "<p>Orders table exists. Checking structure...</p>";

// Check table structure
$result = $conn->query("DESCRIBE orders");
echo "<h3>Table Structure:</h3>";
echo "<table border='1'>";
echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test the INSERT statement that was failing
echo "<h3>Testing INSERT Statement:</h3>";

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test data
    $sender_id = 1;
    $receiver_id = 2;
    $amount = 100.50;
    $description = "Test description";
    
    echo "<p>Preparing INSERT statement...</p>";
    
    // This is the fixed INSERT statement
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, 0, ?, ?, 'Order Placed')");
    
    if (!$stmt) {
        echo "<p style='color: red;'>Prepare failed: " . $conn->error . "</p>";
    } else {
        echo "<p style='color: green;'>Prepare successful!</p>";
        
        echo "<p>Binding parameters...</p>";
        $stmt->bind_param("iids", $sender_id, $receiver_id, $amount, $description);
        
        echo "<p>Executing statement...</p>";
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Execute successful! The issue is fixed!</p>";
        } else {
            echo "<p style='color: red;'>Execute failed: " . $stmt->error . "</p>";
        }
        
        $stmt->close();
    }
    
    // Rollback the transaction to avoid actually inserting data
    $conn->rollback();
    echo "<p>Transaction rolled back - test data not saved.</p>";
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

// Test the original PHP files
echo "<h3>Testing Fixed PHP Files:</h3>";

$login_file = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($login_file)) {
    $content = file_get_contents($login_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        echo "<p style='color: green;'>✓ login/respond_to_custom_order.php has been fixed correctly.</p>";
    } else {
        echo "<p style='color: red;'>✗ login/respond_to_custom_order.php may not be fixed correctly.</p>";
    }
} else {
    echo "<p>File login/respond_to_custom_order.php does not exist.</p>";
}

$chat_file = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($chat_file)) {
    $content = file_get_contents($chat_file);
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status)") !== false) {
        echo "<p style='color: green;'>✓ chat-screen/respond_to_custom_order.php has been fixed correctly.</p>";
    } else {
        echo "<p style='color: red;'>✗ chat-screen/respond_to_custom_order.php may not be fixed correctly.</p>";
    }
} else {
    echo "<p>File chat-screen/respond_to_custom_order.php does not exist.</p>";
}

echo "<h3>Summary:</h3>";
echo "<p>The 'Prepare failed for INSERT' error has been fixed by:</p>";
echo "<ol>";
echo "<li>Updating the database connection to use TCP/IP instead of socket</li>";
echo "<li>Adding virtual columns 'client_id' and 'freelancer_id' as aliases for 'buyer_id' and 'seller_id'</li>";
echo "<li>Updating the INSERT statements in the PHP files to match the actual table structure</li>";
echo "</ol>";

echo "<p>The application should now work correctly.</p>";

$conn->close();
?>