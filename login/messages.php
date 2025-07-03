<?php
//session_start();
require_once '../config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$currentUserId = $_SESSION['user_id'];

// Function to initiate chat with seller
function initiateChat($conn, $buyer_id, $seller_id, $gig_id = null) {
    // Check if messages already exist between these users
    $check_query = "SELECT 1 FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) LIMIT 1";
    $check_stmt = $conn->prepare($check_query);
    if (!$check_stmt) {
        // Log the error for debugging
        error_log("Prepare failed for message check: (" . $conn->errno . ") " . $conn->error);
        return false;
    }
    $check_stmt->bind_param('iiii', $buyer_id, $seller_id, $seller_id, $buyer_id);
    
    if (!$check_stmt->execute()) {
        error_log("Execute failed for message check: (" . $check_stmt->errno . ") " . $check_stmt->error);
        return false;
    }
    $result = $check_stmt->get_result();
    
    // If no messages exist, we'll create the first one
    // We'll return true to indicate success, and the actual message will be created in the calling code
    if ($result->num_rows == 0) {
        return true;
    } else {
        // Messages already exist between these users
        return true;
    }
}

// Handle chat initiation
if (isset($_POST['start_chat']) && isset($_POST['seller_id'])) {
    $seller_id = $_POST['seller_id'];
    $gig_id = isset($_POST['gig_id']) ? $_POST['gig_id'] : null;
    
    // Check if initiateChat is successful (meaning we can proceed with sending a message)
    if (initiateChat($conn, $currentUserId, $seller_id, $gig_id)) {
        // Add initial message
        $message = "Hey, I'm interested in your service";
        if ($gig_id) {
            $message .= " (Gig ID: $gig_id)";
        }
        
        $message_query = "INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
        $message_stmt = $conn->prepare($message_query);
        
        if (!$message_stmt) {
            error_log("Prepare failed for message insert: (" . $conn->errno . ") " . $conn->error);
            echo "<div class='alert alert-danger'>Failed to send message. Please try again later.</div>";
        } else {
            $message_stmt->bind_param('iis', $currentUserId, $seller_id, $message);
            if ($message_stmt->execute()) {
                // Message sent successfully
                echo "<div class='alert alert-success'>Message sent successfully!</div>";
            } else {
                error_log("Execute failed for message insert: (" . $message_stmt->errno . ") " . $message_stmt->error);
                echo "<div class='alert alert-danger'>Failed to send message. Please try again later.</div>";
            }
        }
    } else {
        error_log("Failed to initiate chat between user $currentUserId and seller $seller_id");
        echo "<div class='alert alert-danger'>Failed to initiate chat. Please try again later.</div>";
    }
}

try {
    // Modified query to use mysqli and include profile_picture
    $query = "SELECT DISTINCT u.id, u.username, u.profile_picture 
              FROM users u 
              WHERE u.id IN (
                  SELECT sender_id FROM messages WHERE receiver_id = ?
                  UNION
                  SELECT receiver_id FROM messages WHERE sender_id = ?
              )
              AND u.id != ?
              ORDER BY u.username ASC";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("iii", $currentUserId, $currentUserId, $currentUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);

} catch(Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Define base URL for attachments (adjusted to point to web root)
$base_url = 'http://localhost/'; // Adjusted to match the correct root path
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Client Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .content {
            padding: 20px;
        }
        .chat-container {
            background-color: #f8f9fa;
            height: calc(100vh - 100px);
        }
        .user-list {
            background-color: white;
            border-right: 1px solid #dee2e6;
            height: 100%;
            overflow-y: auto;
        }
        .user-item {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .user-item:hover {
            background-color: #f8f9fa;
        }
        .user-item.active {
            background-color: #e9ecef;
        }
        .chat-area {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 15px;
            background-color: white;
            border-bottom: 1px solid #dee2e6;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f8f9fa;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            max-width: 70%;
        }
        .message.sent {
            align-self: flex-end;
            margin-left: auto;
            justify-content: flex-end;
        }
        .message.received {
            align-self: flex-start;
            margin-right: auto;
            justify-content: flex-start;
        }
        .message-content {
            display: inline-block;
            padding: 12px 18px;
            border-radius: 18px;
            background-color: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.07);
            word-break: break-word;
            max-width: 100%;
            font-size: 1rem;
            position: relative;
        }
        .message.sent .message-content {
            background-color: #007bff;
            color: white;
            border-bottom-right-radius: 5px;
            border-bottom-left-radius: 18px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }
        .message.received .message-content {
            background-color: #f1f0f0;
            color: #222;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 18px;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }
        .chat-input {
            padding: 15px;
            background-color: white;
            border-top: 1px solid #dee2e6;
        }
        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }
        .file-input-wrapper input[type="file"] {
            opacity: 0;
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        .file-input-wrapper .btn {
            cursor: pointer;
        }
        .profile-picture {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        .chat-header .profile-picture {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="chat-container">
            <div class="row h-100">
                <!-- User List Section -->
                <div class="col-md-4 col-lg-3 user-list">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Chats</h4>
                        <a href="../index.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-home"></i> Main Site
                        </a>
                    </div>
                    <?php foreach($users as $user): ?>
                    <div class="user-item" data-user-id="<?php echo htmlspecialchars($user['id']); ?>">
                        <div class="d-flex align-items-center">
                            <?php
                            $profilePicSrc = '';
                            if (!empty($user['profile_picture'])) {
                                // Check if the path starts with 'uploads/'
                                if (strpos($user['profile_picture'], 'uploads/') === 0) {
                                    $profilePicSrc = '../' . $user['profile_picture'];
                                } else {
                                    $profilePicSrc = $user['profile_picture'];
                                }
                                
                                // Check if file exists
                                $absolutePath = __DIR__ . '/../' . $user['profile_picture'];
                                if (!file_exists($absolutePath)) {
                                    $profilePicSrc = "https://ui-avatars.com/api/?name=" . urlencode($user['username']) . "&background=random";
                                }
                            } else {
                                $profilePicSrc = "https://ui-avatars.com/api/?name=" . urlencode($user['username']) . "&background=random";
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($profilePicSrc); ?>"
                                 class="rounded-circle me-2 profile-picture" alt="<?php echo htmlspecialchars($user['username']); ?>">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($user['username']); ?></h6>
                                <small class="text-muted">User</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Chat Area Section -->
                <div class="col-md-8 col-lg-9 chat-area">
                    <div class="chat-header">
                        <div class="d-flex align-items-center">
                            <img src="https://via.placeholder.com/40" class="rounded-circle me-2 profile-picture" alt="User">
                            <div>
                                <h5 class="mb-0">Select a user to start chatting</h5>
                                <small class="text-muted">Click on a user from the list</small>
                            </div>
                        </div>
                    </div>

                    <div class="chat-messages">
                        <!-- Messages will be loaded here -->
                    </div>

                    <div class="chat-input">
                        <form class="d-flex align-items-center" id="chatForm" autocomplete="off" enctype="multipart/form-data">
                            <input type="hidden" id="receiver_id" name="receiver_id" value="">
                            <div class="file-input-wrapper me-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm attachment-btn">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <input type="file" id="attachment" name="attachment">
                            </div>
                            <input type="text" class="form-control me-2" id="messageInput" placeholder="Type your message..." autocomplete="off">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let selectedUserId = null;
    const baseUrl = '<?php echo $base_url; ?>'; // Base URL for attachments

    document.addEventListener('DOMContentLoaded', function() {
        // User click handler
        document.querySelectorAll('.user-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                selectedUserId = this.getAttribute('data-user-id');
                document.getElementById('receiver_id').value = selectedUserId;
                loadChat(selectedUserId);
            });
        });

        // Trigger file input when attachment button is clicked
        document.querySelector('.attachment-btn').addEventListener('click', function() {
            document.getElementById('attachment').click();
        });

        // Send message handler
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('messageInput').value.trim();
            const receiverId = document.getElementById('receiver_id').value;
            const attachment = document.getElementById('attachment').files[0];

            // Validate receiverId and ensure there's either a message or an attachment
            if (!receiverId) {
                alert('Please select a user to chat with.');
                return;
            }
            if (!message && !attachment) {
                alert('Please enter a message or select a file to send.');
                return;
            }

            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message', message);
            if (attachment) {
                formData.append('attachment', attachment);
            }

            fetch('../chat-screen/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                result = result.trim(); // Trim whitespace from response
                if (result === 'success') {
                    loadChat(receiverId);
                    document.getElementById('messageInput').value = '';
                    document.getElementById('attachment').value = ''; // Clear file input
                } else {
                    alert('Failed to send message: ' + result);
                }
            })
            .catch(error => {
                alert('Error sending message: ' + error.message);
            });
        });

        // Function to load chat messages
        function loadChat(userId) {
            fetch('../chat-screen/load_chat.php?user_id=' + userId)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('.chat-messages').innerHTML = html;
                    const username = document.querySelector(`.user-item[data-user-id="${userId}"] h6`).textContent;
                    document.querySelector('.chat-header h5').textContent = username;
                    document.querySelector('.chat-header small').textContent = 'Chatting now';
                    document.querySelector('.chat-header img').src = document.querySelector(`.user-item[data-user-id="${userId}"] img`).src;

                    // Ensure attachments are displayed correctly
                    document.querySelectorAll('.attachment-link').forEach(link => {
                        const attachmentUrl = link.getAttribute('data-attachment');
                        if (attachmentUrl) {
                            link.href = baseUrl + attachmentUrl;
                            // Optionally display images inline
                            if (attachmentUrl.match(/\.(jpg|jpeg|png|gif)$/i)) {
                                const img = document.createElement('img');
                                img.src = baseUrl + attachmentUrl;
                                img.style.maxWidth = '200px';
                                img.style.marginTop = '10px';
                                link.appendChild(img);
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading chat:', error);
                });
        }
    });

    function respondToCustomOrder(messageId, response) {
        if (confirm('Are you sure you want to ' + response + ' this order?')) {
            // Show loading indicator
            const loadingSpinner = document.getElementById('loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.style.display = 'flex';
            }
            
            // Determine which endpoint to use based on the message type
            const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
            const messageType = messageElement ? messageElement.getAttribute('data-message-type') : 'custom_order';
            
            const endpoint = messageType === 'offer' ? '../chat-screen/respond_to_offer.php' : '../chat-screen/respond_to_custom_order.php';
            
            const formData = new FormData();
            formData.append('message_id', messageId);
            formData.append('response', response);

            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Use the global selectedUserId variable
                    if (typeof loadChat === 'function' && selectedUserId) {
                        loadChat(selectedUserId);
                    } else {
                        location.reload();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
                // Hide loading spinner
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                // Hide loading spinner
                if (loadingSpinner) {
                    loadingSpinner.style.display = 'none';
                }
            });
        }
    }
    </script>
    <!-- Loading Spinner -->
    <div id="loading-spinner" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</body>
</html>