<?php
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Orders in Database:\n";

try {
    $query = "SELECT o.*, u1.username as buyer_username, u2.username as seller_username FROM orders o 
              JOIN users u1 ON o.buyer_id = u1.id 
              JOIN users u2 ON o.seller_id = u2.id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        echo "Found " . $result->num_rows . " orders:\n\n";
        
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . "\n";
            echo "Buyer: " . $row['buyer_username'] . " (ID: " . $row['buyer_id'] . ")\n";
            echo "Seller: " . $row['seller_username'] . " (ID: " . $row['seller_id'] . ")\n";
            echo "Gig ID: " . (is_null($row['gig_id']) ? 'NULL (Custom Order)' : $row['gig_id']) . "\n";
            echo "Price: $" . $row['price'] . "\n";
            echo "Description: " . $row['description'] . "\n";
            echo "Status: " . $row['status'] . "\n";
            echo "Created At: " . $row['created_at'] . "\n";
            echo "----------------------------------------\n";
        }
    } else {
        echo "No orders found in the database.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>