<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    echo '<p class="text-danger">Invalid request</p>';
    exit();
}

$current_user_id = intval($_SESSION['user_id']);
$other_user_id = intval($_GET['user_id']);

try {
    $query = "SELECT message, attachment_url, sender_id, created_at 
              FROM messages 
              WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
              ORDER BY created_at ASC";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($messages as $message) {
        $is_sent = $message['sender_id'] == $current_user_id;
        $message_class = $is_sent ? 'sent' : 'received';
        echo '<div class="message ' . htmlspecialchars($message_class) . '">';
        echo '<div class="message-content">';
        if (!empty($message['message'])) {
            echo htmlspecialchars($message['message']);
        }
        if (!empty($message['attachment_url'])) {
            echo '<div><a href="#" class="attachment-link" data-attachment="' . htmlspecialchars($message['attachment_url']) . '" target="_blank">' . htmlspecialchars(basename($message['attachment_url'])) . '</a></div>';
        }
        echo '</div>';
        echo '</div>';
    }
} catch (Exception $e) {
    echo '<p class="text-danger">Error loading messages: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>