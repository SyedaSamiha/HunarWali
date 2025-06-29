<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Get the current user ID
$sender_id = 1; // Admin or test seller ID
$receiver_id = $_SESSION['user_id']; // Current logged-in user

// Create the offer message
$message = json_encode([
    'type' => 'offer',
    'amount' => 200,
    'description' => 'asdas',
    'delivery_time' => 2,
    'status' => 'pending'
]);

try {
    // Insert the offer message into the database
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type, created_at) VALUES (?, ?, ?, 'offer', NOW())");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Test offer sent successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}