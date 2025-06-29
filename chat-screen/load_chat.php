<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['user_id'])) {
    die('unauthorized');
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'];

// No need to mark messages as read anymore

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
        
        echo '<div class="message ' . $message_class . '" data-message-id="' . $message['id'] . '" data-message-type="' . $message['message_type'] . '">';
        echo '<div class="message-content">';
        
        if ($message['message_type'] === 'custom_order' || $message['message_type'] === 'offer') {
            // Parse the JSON message
            $orderData = json_decode($message['message'], true);
            if ($orderData) {
                echo '<div class="order-message">';
                
                // Set title based on message type
                if ($message['message_type'] === 'offer') {
                    echo '<h6 class="order-title"><i class="fas fa-tag"></i> Offer Details</h6>';
                } else {
                    echo '<h6 class="order-title"><i class="fas fa-handshake"></i> Custom Order Details</h6>';
                }
                
                echo '<div class="order-details">';
                echo '<p><strong>Amount:</strong> PKR ' . number_format($orderData['amount'], 2) . '</p>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($orderData['description']) . '</p>';
                echo '<p><strong>Delivery Time:</strong> ' . $orderData['delivery_time'] . ' days</p>';
                
                // Show order status
                $status = $orderData['status'] ?? 'pending';
                if ($status === 'pending' && !$is_sent) {
                    echo '<div class="order-actions">';
                    // Replace lines 57-58 with:
                    echo '<button class="btn btn-success btn-sm me-2" onclick="window.respondToOrder ? respondToOrder(' . $message['id'] . ', \'accept\') : respondToCustomOrder(' . $message['id'] . ', \'accept\')">Accept</button>';
                    echo '<button class="btn btn-danger btn-sm" onclick="window.respondToOrder ? respondToOrder(' . $message['id'] . ', \'decline\') : respondToCustomOrder(' . $message['id'] . ', \'decline\')">Decline</button>';
                    echo '</div>';
                } else {
                    echo '<p class="order-status"><strong>Status:</strong> ' . ucfirst($status) . '</p>';
                }
                
                echo '</div>'; // .order-details
                echo '</div>'; // .order-message
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
// Make the function available in the global scope
window.respondToOrder = function(messageId, response) {
    if (confirm('Are you sure you want to ' + response + ' this order?')) {
        // Check if loading spinner exists before trying to show it
        const loadingSpinner = document.getElementById('loading-spinner');
        if (loadingSpinner) {
            loadingSpinner.style.display = 'flex';
        }
        
        // Determine which endpoint to use based on the message type
        const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
        const messageType = messageElement ? messageElement.getAttribute('data-message-type') : 'custom_order';
        
        const endpoint = messageType === 'offer' ? 'respond_to_offer.php' : 'respond_to_custom_order.php';
        
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message_id=' + messageId + '&response=' + response
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the chat to show the updated order status
                if (typeof loadChat === 'function') {
                    // Try different variables that might contain the user ID
                    if (typeof currentReceiverId !== 'undefined') {
                        loadChat(currentReceiverId);
                    } else if (typeof selectedUserId !== 'undefined') {
                        loadChat(selectedUserId);
                    } else if (typeof receiverId !== 'undefined') {
                        loadChat(receiverId);
                    } else {
                        // If no chat loading function is available, reload the page
                        window.location.reload();
                    }
                } else {
                    // If no chat loading function is available, reload the page
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.message);
            }
            // Hide loading spinner if it exists
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            // Hide loading spinner if it exists
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }
        });
    }
}
</script>