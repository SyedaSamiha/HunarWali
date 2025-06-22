<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$new_status = isset($_POST['newStatus']) ? $_POST['newStatus'] : '';
$description = isset($_POST['statusDescription']) ? trim($_POST['statusDescription']) : '';

if (!$order_id || !$new_status || !$description) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Verify the user is the freelancer for this order
    $verify_query = "SELECT o.id, g.user_id as freelancer_id, o.status as current_status
                    FROM orders o
                    JOIN gigs g ON o.gig_id = g.id
                    WHERE o.id = ? AND g.user_id = ?";
    
    $verify_stmt = $conn->prepare($verify_query);
    $verify_stmt->bind_param("ii", $order_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $order_info = $verify_result->fetch_assoc();

    if (!$order_info) {
        throw new Exception('Order not found or you do not have permission to update it');
    }

    // Validate status transition
    $current_status = strtolower($order_info['current_status']);
    $new_status_lower = strtolower($new_status);
    
    $valid_transitions = [
        'pending' => ['in_progress', 'completed'],
        'in_progress' => ['completed']
    ];

    if (!isset($valid_transitions[$current_status]) || !in_array($new_status_lower, $valid_transitions[$current_status])) {
        throw new Exception('Invalid status transition');
    }

    // Update order status
    $update_order_query = "UPDATE orders SET status = ? WHERE id = ?";
    $update_order_stmt = $conn->prepare($update_order_query);
    $update_order_stmt->bind_param("si", $new_status, $order_id);
    
    if (!$update_order_stmt->execute()) {
        throw new Exception('Failed to update order status');
    }

    // Add tracking entry
    $tracking_query = "INSERT INTO order_tracking (order_id, status, description, updated_by) VALUES (?, ?, ?, ?)";
    $tracking_stmt = $conn->prepare($tracking_query);
    $tracking_stmt->bind_param("issi", $order_id, $new_status, $description, $user_id);
    
    if (!$tracking_stmt->execute()) {
        throw new Exception('Failed to add tracking entry');
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