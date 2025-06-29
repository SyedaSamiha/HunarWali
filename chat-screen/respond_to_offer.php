<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$message_id = isset($_POST['message_id']) ? intval($_POST['message_id']) : 0;
$response = isset($_POST['response']) ? $_POST['response'] : ''; // 'accept' or 'decline'

if (!$message_id || !in_array($response, ['accept', 'decline'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

try {
    // Check if orders table exists
    $result = $conn->query("SHOW TABLES LIKE 'orders'");
    if ($result->num_rows == 0) {
        throw new Exception('Orders table does not exist. Please run create_orders_table.php first.');
    }
    
    // Start transaction
    $conn->begin_transaction();

    // Get the original offer message
    $stmt = $conn->prepare("SELECT * FROM messages WHERE id = ? AND message_type = 'offer'");
    if (!$stmt) {
        throw new Exception("Prepare failed for SELECT: " . $conn->error);
    }
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if (!$message) {
        throw new Exception('Offer not found');
    }

    $order_data = json_decode($message['message'], true);
    $order_data['status'] = $response;

    // Update the order message with the response
    $updated_message = json_encode($order_data);
    $stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed for UPDATE: " . $conn->error);
    }
    $stmt->bind_param("si", $updated_message, $message_id);
    $stmt->execute();

    // If accepted, create the order
    if ($response === 'accept') {
        // Validate data before insertion
        if (!isset($message['sender_id']) || !isset($message['receiver_id']) || 
            !isset($order_data['amount']) || !isset($order_data['description']) || 
            !isset($order_data['delivery_time'])) {
            throw new Exception('Missing required order data');
        }
        
        $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        if (!$stmt) {
            throw new Exception("Prepare failed for INSERT: " . $conn->error);
        }
        
        // Convert types to ensure proper binding
        $sender_id = intval($message['sender_id']);
        $receiver_id = intval($message['receiver_id']);
        $amount = floatval($order_data['amount']);
        $description = $order_data['description'];
        $delivery_time = intval($order_data['delivery_time']);
        
        $stmt->bind_param("iidsi", 
            $sender_id, 
            $receiver_id, 
            $amount,
            $description,
            $delivery_time
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for INSERT: " . $stmt->error);
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->ping()) {
        $conn->rollback();
    }
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}