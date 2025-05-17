<?php
session_start();
require_once '../config/database.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

$currentUserId = $_SESSION['user_id'];

try {
    // Modified query to use mysqli
    $query = "SELECT DISTINCT u.id, u.username 
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
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar a.active {
            background-color: #0d6efd;
        }
        .sidebar i {
            margin-right: 10px;
        }
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
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-white text-center mb-4">Client Panel</h3>
                <nav>
                    <a href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="ordered-services.php">
                        <i class="fas fa-list"></i> Ordered Services
                    </a>
                    <a href="messages.php" class="active">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                    <a href="logout.php" class="mt-5">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="chat-container">
                    <div class="row h-100">
                        <!-- User List Section -->
                        <div class="col-md-4 col-lg-3 user-list">
                            <div class="p-3 border-bottom">
                                <h4 class="mb-0">Chats</h4>
                            </div>
                            <?php foreach($users as $user): ?>
                            <div class="user-item" data-user-id="<?php echo htmlspecialchars($user['id']); ?>">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=random"
                                         class="rounded-circle me-2" alt="<?php echo htmlspecialchars($user['username']); ?>">
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
                                    <img src="https://via.placeholder.com/40" class="rounded-circle me-2" alt="User">
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
                                <form class="d-flex align-items-center" id="chatForm" autocomplete="off">
                                    <input type="hidden" id="receiver_id" name="receiver_id" value="">
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
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let selectedUserId = null;

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

        // Send message handler
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('messageInput').value.trim();
            const receiverId = document.getElementById('receiver_id').value;

            if (!receiverId || !message) return;

            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message', message);

            fetch('../chat-screen/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    loadChat(receiverId);
                    document.getElementById('messageInput').value = '';
                } else {
                    alert('Failed to send message: ' + result);
                }
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
                });
        }
    });
    </script>
</body>
</html> 