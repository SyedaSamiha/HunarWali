<?php
require_once 'config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Orders in Database</h1>";

try {
    $query = "SELECT o.*, u1.username as buyer_username, u2.username as seller_username FROM orders o 
              JOIN users u1 ON o.buyer_id = u1.id 
              JOIN users u2 ON o.seller_id = u2.id";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Buyer</th>";
        echo "<th>Seller</th>";
        echo "<th>Gig ID</th>";
        echo "<th>Price</th>";
        echo "<th>Description</th>";
        echo "<th>Status</th>";
        echo "<th>Created At</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['buyer_username']) . " (ID: " . htmlspecialchars($row['buyer_id']) . ")</td>";
            echo "<td>" . htmlspecialchars($row['seller_username']) . " (ID: " . htmlspecialchars($row['seller_id']) . ")</td>";
            echo "<td>" . (is_null($row['gig_id']) ? 'NULL (Custom Order)' : htmlspecialchars($row['gig_id'])) . "</td>";
            echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No orders found in the database.</p>";
    }
} catch (Exception $e) {
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>