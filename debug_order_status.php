<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

echo "<h2>Debug Order Status</h2>";
echo "<p>User ID: " . $user_id . "</p>";

// Get all orders for this freelancer with their status
$query = "SELECT o.id, o.status, g.gig_title, u.username as buyer_name
          FROM orders o 
          JOIN users u ON o.buyer_id = u.id 
          JOIN gigs g ON o.gig_id = g.id 
          WHERE g.user_id = ? 
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>All Orders:</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Order ID</th><th>Status</th><th>Gig Title</th><th>Buyer</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td><strong>" . $row['status'] . "</strong></td>";
    echo "<td>" . htmlspecialchars($row['gig_title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['buyer_name']) . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check unique status values
echo "<h3>Unique Status Values:</h3>";
$status_query = "SELECT DISTINCT status FROM orders";
$status_result = $conn->query($status_query);

echo "<ul>";
while ($row = $status_result->fetch_assoc()) {
    echo "<li><strong>" . $row['status'] . "</strong></li>";
}
echo "</ul>";

// Check completed orders specifically
echo "<h3>Orders that should show feedback button:</h3>";
$completed_query = "SELECT o.id, o.status, g.gig_title, u.username as buyer_name
                   FROM orders o 
                   JOIN users u ON o.buyer_id = u.id 
                   JOIN gigs g ON o.gig_id = g.id 
                   WHERE g.user_id = ? AND o.status = 'completed'";

$stmt = $conn->prepare($completed_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$completed_result = $stmt->get_result();

if ($completed_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Order ID</th><th>Status</th><th>Gig Title</th><th>Buyer</th></tr>";
    
    while ($row = $completed_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['status'] . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['gig_title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['buyer_name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders with status 'completed' found.</p>";
}

// Check for similar status values
echo "<h3>Checking for similar status values:</h3>";
$similar_query = "SELECT o.id, o.status, g.gig_title, u.username as buyer_name
                 FROM orders o 
                 JOIN users u ON o.buyer_id = u.id 
                 JOIN gigs g ON o.gig_id = g.id 
                 WHERE g.user_id = ? AND (o.status LIKE '%complete%' OR o.status LIKE '%done%' OR o.status LIKE '%finish%')";

$stmt = $conn->prepare($similar_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$similar_result = $stmt->get_result();

if ($similar_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Order ID</th><th>Status</th><th>Gig Title</th><th>Buyer</th></tr>";
    
    while ($row = $similar_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td><strong>" . $row['status'] . "</strong></td>";
        echo "<td>" . htmlspecialchars($row['gig_title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['buyer_name']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders with similar status values found.</p>";
}
?> 