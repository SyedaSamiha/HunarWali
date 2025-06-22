<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

echo "<h2>Testing Status Fix</h2>";
echo "<p>User ID: " . $user_id . "</p>";

// Test the updated query
$query = "SELECT o.*, u.username as buyer_name, g.gig_title as gig_title,
          CASE 
            WHEN r.id IS NOT NULL THEN 'client_feedback'
            WHEN ff.id IS NOT NULL THEN 'freelancer_feedback'
            ELSE 'no_feedback'
          END as feedback_status
          FROM orders o 
          JOIN users u ON o.buyer_id = u.id 
          JOIN gigs g ON o.gig_id = g.id 
          LEFT JOIN reviews r ON o.id = r.order_id AND r.user_id = o.buyer_id
          LEFT JOIN freelancer_feedback ff ON o.id = ff.order_id AND ff.freelancer_id = g.user_id
          WHERE g.user_id = ? 
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h3>Orders with Feedback Status:</h3>";
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Order ID</th><th>Status</th><th>Feedback Status</th><th>Should Show Button</th></tr>";

while ($row = $result->fetch_assoc()) {
    $should_show = (strtolower($row['status']) === 'completed' && 
                   ($row['feedback_status'] === 'no_feedback' || $row['feedback_status'] === 'client_feedback'));
    
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td><strong>" . $row['status'] . "</strong></td>";
    echo "<td>" . $row['feedback_status'] . "</td>";
    echo "<td>" . ($should_show ? "✅ YES" : "❌ NO") . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Summary:</h3>";
echo "<p>The feedback button should now appear for orders with status 'completed' or 'Completed' that have no feedback or only client feedback.</p>";
echo "<p><a href='login/dashboard.php?page=orders'>Go to Orders Page</a></p>";
?> 