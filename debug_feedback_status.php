<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

echo "<h2>Debug Feedback Status</h2>";
echo "<p>User ID: " . $user_id . "</p>";

// Check if freelancer has given feedback
$feedback_query = "SELECT ff.*, o.id as order_id, g.gig_title, u.username as client_name
                  FROM freelancer_feedback ff
                  JOIN orders o ON ff.order_id = o.id
                  JOIN gigs g ON o.gig_id = g.id
                  JOIN users u ON ff.client_id = u.id
                  WHERE ff.freelancer_id = ?";

$stmt = $conn->prepare($feedback_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$feedback_result = $stmt->get_result();

echo "<h3>Freelancer Feedback Submitted:</h3>";
if ($feedback_result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Order ID</th><th>Gig Title</th><th>Client</th><th>Overall Rating</th><th>Date</th></tr>";
    
    while ($row = $feedback_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['order_id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['gig_title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
        echo "<td>" . $row['overall_rating'] . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No freelancer feedback found.</p>";
}

// Test the orders query with feedback status
echo "<h3>Orders with Feedback Status:</h3>";
$query = "SELECT o.*, u.username as buyer_name, g.gig_title as gig_title,
          CASE 
            WHEN ff.id IS NOT NULL THEN 'freelancer_feedback'
            WHEN r.id IS NOT NULL THEN 'client_feedback'
            ELSE 'no_feedback'
          END as feedback_status,
          ff.id as freelancer_feedback_id,
          r.id as client_review_id
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

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Order ID</th><th>Status</th><th>Feedback Status</th><th>FF ID</th><th>CR ID</th><th>Should Show Button</th></tr>";

while ($row = $result->fetch_assoc()) {
    $should_show = (strtolower($row['status']) === 'completed' && 
                   ($row['feedback_status'] === 'no_feedback' || $row['feedback_status'] === 'client_feedback'));
    
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td><strong>" . $row['status'] . "</strong></td>";
    echo "<td>" . $row['feedback_status'] . "</td>";
    echo "<td>" . ($row['freelancer_feedback_id'] ?: 'NULL') . "</td>";
    echo "<td>" . ($row['client_review_id'] ?: 'NULL') . "</td>";
    echo "<td>" . ($should_show ? "✅ YES" : "❌ NO") . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Summary:</h3>";
echo "<p>If you see 'freelancer_feedback' in the Feedback Status column, the Review Client button should NOT appear.</p>";
echo "<p><a href='login/dashboard.php?page=orders'>Go to Orders Page</a></p>";
?> 