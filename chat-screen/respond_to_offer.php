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

$message_id = $_POST['message_id'];
$response = $_POST['response']; // 'accept' or 'decline'

if (!$message_id || !in_array($response, ['accept', 'decline'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Get the original offer message
    $stmt = $conn->prepare("SELECT * FROM messages WHERE id = ? AND message_type = 'offer'");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $message = $result->fetch_assoc();

    if (!$message) {
        throw new Exception('Offer not found');
    }

    $offer_data = json_decode($message['message'], true);
    $offer_data['status'] = $response;

    // Update the offer message with the response
    $updated_message = json_encode($offer_data);
    $stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ?");
    $stmt->bind_param("si", $updated_message, $message_id);
    $stmt->execute();

    // If accepted, create the order
    if ($response === 'accept') {
        $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iidsi", 
            $message['sender_id'], 
            $message['receiver_id'], 
            $offer_data['amount'],
            $offer_data['description'],
            $offer_data['delivery_time']
        );
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} 