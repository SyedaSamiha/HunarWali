<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    die(json_encode(['error' => 'Invalid request']));
}

$user_id = intval($_GET['user_id']);
$current_user_id = $_SESSION['user_id'];

// Check if chat exists between these users
$query = "SELECT id FROM chats WHERE 
    (buyer_id = ? AND seller_id = ?) OR 
    (buyer_id = ? AND seller_id = ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param('iiii', $current_user_id, $user_id, $user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $chat = $result->fetch_assoc();
    echo json_encode(['chat_id' => $chat['id']]);
} else {
    // Create new chat
    $create_query = "INSERT INTO chats (buyer_id, seller_id, created_at) VALUES (?, ?, NOW())";
    $create_stmt = $conn->prepare($create_query);
    $create_stmt->bind_param('ii', $current_user_id, $user_id);
    
    if ($create_stmt->execute()) {
        echo json_encode(['chat_id' => $conn->insert_id]);
    } else {
        echo json_encode(['error' => 'Failed to create chat']);
    }
}
?> 