<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    die('unauthorized');
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'];

try {
    $query = "SELECT m.*, u.username, u.profile_picture 
              FROM messages m 
              JOIN users u ON m.sender_id = u.id 
              WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                 OR (m.sender_id = ? AND m.receiver_id = ?) 
              ORDER BY m.created_at ASC";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($message = $result->fetch_assoc()) {
        $is_sent = $message['sender_id'] == $current_user_id;
        $message_class = $is_sent ? 'sent' : 'received';
        $timestamp = date('g:i A', strtotime($message['created_at']));
        
        echo '<div class="message ' . $message_class . '">';
        echo '<div class="message-content">';
        
        if ($message['message_type'] === 'offer') {
            // Parse the offer JSON
            $offerData = json_decode($message['message'], true);
            if ($offerData) {
                echo '<div class="offer-message">';
                echo '<h6 class="offer-title"><i class="fas fa-handshake"></i> Offer Details</h6>';
                echo '<div class="offer-details">';
                echo '<p><strong>Amount:</strong> PKR ' . number_format($offerData['amount'], 2) . '</p>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($offerData['description']) . '</p>';
                echo '<p><strong>Delivery Time:</strong> ' . $offerData['delivery_time'] . ' days</p>';
                
                // Show offer status
                $status = $offerData['status'] ?? 'pending';
                if ($status === 'pending' && !$is_sent) {
                    echo '<div class="offer-actions">';
                    echo '<button class="btn btn-success btn-sm me-2" onclick="respondToOffer(' . $message['id'] . ', \'accept\')">Accept</button>';
                    echo '<button class="btn btn-danger btn-sm" onclick="respondToOffer(' . $message['id'] . ', \'decline\')">Decline</button>';
                    echo '</div>';
                } else {
                    echo '<p class="offer-status"><strong>Status:</strong> ' . ucfirst($status) . '</p>';
                }
                
                echo '</div>'; // .offer-details
                echo '</div>'; // .offer-message
            }
        } else {
            // Display regular message text if not empty
            if (!empty($message['message'])) {
                echo htmlspecialchars($message['message']);
            }
            
            // Display attachment if present
            if (!empty($message['attachment_url'])) {
                $file_extension = strtolower(pathinfo($message['attachment_url'], PATHINFO_EXTENSION));
                $is_image = in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                
                echo '<div class="message-attachment">';
                if ($is_image) {
                    echo '<a href="../' . htmlspecialchars($message['attachment_url']) . '" target="_blank">';
                    echo '<img src="../' . htmlspecialchars($message['attachment_url']) . '" style="max-width: 200px; max-height: 200px; border-radius: 8px;">';
                    echo '</a>';
                } else {
                    echo '<a href="../' . htmlspecialchars($message['attachment_url']) . '" target="_blank">';
                    $icon_class = 'fa-file';
                    if ($file_extension === 'pdf') {
                        $icon_class = 'fa-file-pdf';
                    } elseif (in_array($file_extension, ['doc', 'docx'])) {
                        $icon_class = 'fa-file-word';
                    }
                    echo '<i class="fas ' . $icon_class . '"></i>';
                    echo '<span>' . htmlspecialchars(basename($message['attachment_url'])) . '</span>';
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        
        // Add timestamp
        echo '<div class="message-time">' . $timestamp . '</div>';
        
        echo '</div>'; // .message-content
        echo '</div>'; // .message
    }
    
} catch (Exception $e) {
    die('Error loading messages: ' . $e->getMessage());
}
?>

<script>
function respondToOffer(messageId, response) {
    const formData = new FormData();
    formData.append('message_id', messageId);
    formData.append('response', response);

    fetch('respond_to_offer.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadChat(selectedUserId);
        } else {
            alert('Failed to respond to offer: ' + data.message);
        }
    });
}
</script>