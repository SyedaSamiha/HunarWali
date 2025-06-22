<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

header('Content-Type: application/json');

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

try {
    // Start transaction
    $conn->begin_transaction();

    // Verify that the order belongs to the user's gig
    $query = "SELECT o.id, o.status as current_status FROM orders o 
              JOIN gigs g ON o.gig_id = g.id 
              WHERE o.id = ? AND g.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_info = $result->fetch_assoc();

    if (!$order_info) {
        throw new Exception('Order not found or unauthorized');
    }

    // Update the order status
    $update_query = "UPDATE orders SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $status, $order_id);

    if (!$update_stmt->execute()) {
        throw new Exception('Error updating order status');
    }

    // Generate description based on status change
    $description = '';
    switch ($status) {
        case 'in_progress':
            $description = 'Order work has started';
            break;
        case 'completed':
            $description = 'Order has been completed successfully';
            break;
        case 'cancelled':
            $description = 'Order has been cancelled';
            break;
        default:
            $description = 'Order status updated to ' . ucfirst(str_replace('_', ' ', $status));
    }

    // Add tracking entry
    $tracking_query = "INSERT INTO order_tracking (order_id, status, description, updated_by) VALUES (?, ?, ?, ?)";
    $tracking_stmt = $conn->prepare($tracking_query);
    $tracking_stmt->bind_param("issi", $order_id, $status, $description, $user_id);
    
    if (!$tracking_stmt->execute()) {
        throw new Exception('Error adding tracking entry');
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?> 