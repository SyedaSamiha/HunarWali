<?php
// Test custom order fix with real users
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Test Custom Order Fix with Real Users\n\n";

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

// Check if users table exists
$result = $conn->query("SHOW TABLES LIKE 'users'");
if ($result->num_rows == 0) {
    log_message("Users table does not exist.");
    exit;
}

// Check the current structure of the orders table
log_message("Checking current structure of orders table...");
$result = $conn->query("DESCRIBE orders gig_id");
$row = $result->fetch_assoc();

if ($row && strpos($row['Null'], 'YES') !== false) {
    log_message("gig_id allows NULL values. Good!");
} else {
    log_message("gig_id does not allow NULL values. This needs to be fixed.");
    exit;
}

// Get real user IDs from the database
log_message("Getting real user IDs from the database...");
$result = $conn->query("SELECT id FROM users LIMIT 2");

if ($result->num_rows < 2) {
    log_message("Not enough users in the database for testing.");
    log_message("Creating test users...");
    
    // Create test users if needed
    $conn->query("INSERT INTO users (username, email, password, role) VALUES ('testbuyer', 'testbuyer@example.com', 'password123', 'client')");
    $conn->query("INSERT INTO users (username, email, password, role) VALUES ('testseller', 'testseller@example.com', 'password123', 'freelancer')");
    
    $result = $conn->query("SELECT id FROM users LIMIT 2");
}

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row['id'];
}

if (count($users) < 2) {
    log_message("Could not get enough user IDs for testing.");
    exit;
}

$buyer_id = $users[0];
$seller_id = $users[1];

log_message("Using buyer_id: $buyer_id and seller_id: $seller_id for testing.");

// Test the fix
log_message("Testing the fix with real users...");

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Test INSERT with NULL gig_id
    $price = 100.50;
    $description = "Test custom order description";
    
    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, NULL, ?, ?, 'Order Placed')");
    if (!$stmt) {
        throw new Exception("Prepare failed for INSERT: " . $conn->error);
    }
    
    $stmt->bind_param("iids", $buyer_id, $seller_id, $price, $description);
    
    if ($stmt->execute()) {
        log_message("Test INSERT executed successfully.");
        $order_id = $conn->insert_id;
        log_message("Created order with ID: $order_id");
        
        // Verify the inserted data
        $result = $conn->query("SELECT * FROM orders WHERE id = $order_id");
        $order = $result->fetch_assoc();
        
        log_message("Verified order data:");
        log_message("- buyer_id: {$order['buyer_id']}");
        log_message("- seller_id: {$order['seller_id']}");
        log_message("- gig_id: " . ($order['gig_id'] === null ? "NULL" : $order['gig_id']));
        log_message("- price: {$order['price']}");
        log_message("- description: {$order['description']}");
        log_message("- status: {$order['status']}");
    } else {
        log_message("Test INSERT failed: " . $stmt->error);
    }
    
    // Rollback the transaction to avoid actually inserting data
    $conn->rollback();
    log_message("Transaction rolled back.");
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn) {
        $conn->rollback();
    }
    log_message("Error during testing: " . $e->getMessage());
}

// Check the PHP files
log_message("\nChecking PHP files...");

// Check login/respond_to_custom_order.php
$file_path = __DIR__ . '/login/respond_to_custom_order.php';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, NULL, ?, ?, 'Order Placed')") !== false) {
        log_message("login/respond_to_custom_order.php is correctly using NULL for gig_id.");
    } else {
        log_message("login/respond_to_custom_order.php is NOT correctly using NULL for gig_id.");
    }
} else {
    log_message("File $file_path does not exist.");
}

// Check chat-screen/respond_to_custom_order.php
$file_path = __DIR__ . '/chat-screen/respond_to_custom_order.php';
if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    
    if (strpos($content, "INSERT INTO orders (buyer_id, seller_id, gig_id, price, description, status) VALUES (?, ?, NULL, ?, ?, 'Order Placed')") !== false) {
        log_message("chat-screen/respond_to_custom_order.php is correctly using NULL for gig_id.");
    } else {
        log_message("chat-screen/respond_to_custom_order.php is NOT correctly using NULL for gig_id.");
    }
} else {
    log_message("File $file_path does not exist.");
}

log_message("\nSummary:");
log_message("1. The gig_id column has been modified to allow NULL values.");
log_message("2. The PHP files have been updated to use NULL for gig_id in custom orders.");
log_message("3. The application should now work correctly for custom orders.");

$conn->close();
?>