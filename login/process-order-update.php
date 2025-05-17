<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit();
}

// Check if required parameters are present
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];
$user_id = $_SESSION['user_id'];

// Validate status
$allowed_statuses = ['pending', 'in_progress', 'completed', 'cancelled'];
if (!in_array($status, $allowed_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

// Verify that the order belongs to the user's gig
$query = "SELECT o.id FROM orders o 
          JOIN gigs g ON o.gig_id = g.id 
          WHERE o.id = ? AND g.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found or unauthorized']);
    exit();
}

// Update the order status
$update_query = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $status, $order_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating order status']);
}

$update_stmt->close();
$conn->close();
?> 