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
            background-color: #f8f9fa;
        }
        .chat-container {
            background-color: #fff;
            height: calc(100vh - 100px);
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .user-list {
            background-color: #fff;
            border-right: 1px solid #eaeaea;
            height: 100%;
            overflow-y: auto;
        }
        .user-list::-webkit-scrollbar {
            width: 6px;
        }
        .user-list::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 3px;
        }
        .user-item {
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .user-item:hover {
            background-color: #f8f9fa;
        }
        .user-item.active {
            background-color: #e7f1ff;
            border-left: 3px solid #0d6efd;
        }
        .user-item .d-flex {
            gap: 12px;
        }
        .chat-area {
            height: 100%;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }
        .chat-header {
            padding: 20px;
            background-color: #fff;
            border-bottom: 1px solid #eaeaea;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .chat-header .d-flex {
            gap: 15px;
        }
        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f8f9fa;
            background-image: url('data:image/svg+xml,%3Csvg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Cpath d="M0 0h20L0 20z"/%3E%3C/g%3E%3C/svg%3E');
        }
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        .chat-messages::-webkit-scrollbar-thumb {
            background-color: #ccc;
            border-radius: 3px;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
            max-width: 80%;
            position: relative;
        }
        .message.sent {
            margin-left: auto;
            justify-content: flex-end;
        }
        .message.received {
            margin-right: auto;
            justify-content: flex-start;
        }
        .message-content {
            padding: 12px 18px;
            border-radius: 18px;
            background-color: #fff;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            font-size: 0.95rem;
            line-height: 1.4;
            position: relative;
        }
        .message.sent .message-content {
            background-color: #0d6efd;
            color: white;
            border-bottom-right-radius: 5px;
        }
        .message.received .message-content {
            background-color: #fff;
            color: #2b2b2b;
            border-bottom-left-radius: 5px;
        }
        .chat-input {
            padding: 20px;
            background-color: #fff;
            border-top: 1px solid #eaeaea;
        }
        .chat-input form {
            display: flex;
            gap: 10px;
        }
        .chat-input .input-group {
            position: relative;
            flex: 1;
        }
        .chat-input .form-control {
            border-radius: 25px;
            padding: 12px 20px;
            border: 1px solid #eaeaea;
            transition: all 0.2s ease;
        }
        .chat-input .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .chat-input .input-group-text {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }
        .chat-input .btn {
            border-radius: 50%;
            width: 42px;
            height: 42px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .chat-input .btn-success {
            background-color: #28a745;
        }
        .chat-input .btn-success:hover {
            background-color: #218838;
        }
        .profile-picture {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .message-attachment {
            margin-top: 10px;
            padding: 10px;
            background-color: rgba(0,0,0,0.03);
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .message-attachment:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .message-attachment a {
            color: inherit;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        .message-attachment i {
            font-size: 1.2em;
            opacity: 0.8;
        }
        .message.sent .message-attachment {
            background-color: rgba(255,255,255,0.1);
        }
        .message.sent .message-attachment:hover {
            background-color: rgba(255,255,255,0.2);
        }
        .message.sent .message-attachment a {
            color: white;
        }
        .user-status {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 2px;
        }
        .chat-header .user-status {
            color: #28a745;
            font-weight: 500;
        }
        .message-time {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 5px;
            text-align: right;
        }
        .message.sent .message-time {
            color: rgba(255,255,255,0.8);
        }
        .offer-message {
            background-color: rgba(0,0,0,0.03);
            border-radius: 12px;
            padding: 15px;
            margin: -12px -18px;
        }
        .message.sent .offer-message {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .offer-title {
            font-size: 1rem;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: inherit;
        }
        .offer-details {
            font-size: 0.9rem;
        }
        .offer-details p {
            margin-bottom: 8px;
        }
        .offer-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        .offer-status {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid rgba(0,0,0,0.1);
            font-size: 0.9rem;
        }
        .message.sent .offer-status {
            border-top-color: rgba(255,255,255,0.2);
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 15px;
        }
        .chat-input .btn-offer {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            height: 42px;
        }
        .chat-input .btn-offer:hover {
            background-color: #218838;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chat-input .btn-offer i {
            font-size: 1.1rem;
        }
        /* Modal Styles */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eaeaea;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }
        .modal-header .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2b2b2b;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .modal-header .modal-title i {
            color: #28a745;
        }
        .modal-body {
            padding: 20px;
        }
        .modal-footer {
            background-color: #f8f9fa;
            border-top: 1px solid #eaeaea;
            border-radius: 0 0 15px 15px;
            padding: 20px;
        }
        .modal .form-label {
            font-weight: 500;
            color: #2b2b2b;
            margin-bottom: 8px;
        }
        .modal .form-control {
            border: 1px solid #eaeaea;
            border-radius: 10px;
            padding: 12px;
            transition: all 0.2s ease;
        }
        .modal .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .modal textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        .modal .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
        }
        .modal .btn-primary {
            background-color: #28a745;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
        }
        .modal .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .modal .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #eaeaea;
            border-radius: 10px 0 0 10px;
            padding: 0 15px;
            color: #6c757d;
        }
        .modal .input-group .form-control {
            border-radius: 0 10px 10px 0;
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
                                    <?php
                                    $profilePicSrc = '';
                                    if (!empty($user['profile_picture'])) {
                                        if (strpos($user['profile_picture'], 'uploads/') === 0) {
                                            $profilePicSrc = '../' . $user['profile_picture'];
                                        } else {
                                            $profilePicSrc = $user['profile_picture'];
                                        }
                                        
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
                                        <div class="user-status">Last seen recently</div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Chat Area Section -->
                        <div class="col-md-8 col-lg-9 chat-area">
                            <div class="chat-header">
                                <div class="d-flex align-items-center">
                                    <img src="https://via.placeholder.com/40" class="profile-picture" alt="User">
                                    <div>
                                        <h5 class="mb-0">Select a user to start chatting</h5>
                                        <div class="user-status">Click on a user from the list</div>
                                    </div>
                                </div>
                            </div>

                            <div class="chat-messages">
                                <!-- Messages will be loaded here -->
                            </div>

                            <div class="chat-input">
                                <form class="d-flex align-items-center" id="chatForm" autocomplete="off" enctype="multipart/form-data">
                                    <input type="hidden" id="receiver_id" name="receiver_id" value="">
                                    <div class="input-group me-2">
                                        <input type="text" class="form-control" id="messageInput" placeholder="Type your message..." autocomplete="off">
                                        <label class="input-group-text btn btn-secondary" for="fileInput">
                                            <i class="fas fa-paperclip"></i>
                                        </label>
                                        <input type="file" class="d-none" id="fileInput" name="attachment" accept="image/*,.pdf,.doc,.docx">
                                    </div>
                                    <button type="button" class="btn btn-offer me-2" id="sendOfferBtn">
                                        <i class="fas fa-handshake"></i>
                                        <span>Send Offer</span>
                                    </button>
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

    <!-- Offer Modal -->
    <div class="modal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="offerModalLabel">
                        <i class="fas fa-handshake"></i>
                        Send Offer
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="offerForm">
                        <input type="hidden" id="offer_receiver_id" name="receiver_id">
                        <div class="mb-3">
                            <label for="offerAmount" class="form-label">Offer Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">PKR</span>
                                <input type="number" class="form-control" id="offerAmount" name="amount" required min="1" step="0.01" placeholder="Enter amount">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="offerDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="offerDescription" name="description" rows="3" required placeholder="Describe your offer details"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="deliveryTime" class="form-label">Delivery Time</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="deliveryTime" name="delivery_time" required min="1" placeholder="Enter number of days">
                                <span class="input-group-text">Days</span>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitOffer">Send Offer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let selectedUserId = null;
    const offerModal = new bootstrap.Modal(document.getElementById('offerModal'));

    document.addEventListener('DOMContentLoaded', function() {
        // User click handler
        document.querySelectorAll('.user-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                selectedUserId = this.getAttribute('data-user-id');
                document.getElementById('receiver_id').value = selectedUserId;
                document.getElementById('offer_receiver_id').value = selectedUserId;
                loadChat(selectedUserId);
            });
        });

        // Send Offer button click handler
        document.getElementById('sendOfferBtn').addEventListener('click', function() {
            if (!selectedUserId) {
                alert('Please select a user first');
                return;
            }
            document.getElementById('offer_receiver_id').value = selectedUserId;
            offerModal.show();
        });

        // Submit Offer handler
        document.getElementById('submitOffer').addEventListener('click', function() {
            const form = document.getElementById('offerForm');
            const formData = new FormData(form);

            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Disable submit button to prevent double submission
            const submitButton = document.getElementById('submitOffer');
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';

            fetch('../chat-screen/send_offer.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    offerModal.hide();
                    form.reset();
                    loadChat(selectedUserId);
                } else {
                    alert('Failed to send offer: ' + (data.message || 'Unknown error occurred'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the offer. Please try again.');
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.innerHTML = 'Send Offer';
            });
        });

        // Send message handler
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('messageInput').value.trim();
            const receiverId = document.getElementById('receiver_id').value;
            const fileInput = document.getElementById('fileInput');

            if (!receiverId || (!message && !fileInput.files[0])) return;

            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message', message);
            
            if (fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            // Disable form elements during upload
            const submitButton = this.querySelector('button[type="submit"]');
            const messageInput = document.getElementById('messageInput');
            const fileInputLabel = document.querySelector('label[for="fileInput"]');
            
            submitButton.disabled = true;
            messageInput.disabled = true;
            fileInputLabel.style.pointerEvents = 'none';
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            fetch('../chat-screen/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.trim() === 'success') {
                    loadChat(receiverId);
                    document.getElementById('messageInput').value = '';
                    document.getElementById('fileInput').value = '';
                    // Reset the attachment button style
                    const label = document.querySelector('label[for="fileInput"]');
                    label.innerHTML = '<i class="fas fa-paperclip"></i>';
                    label.classList.remove('btn-success');
                    label.classList.add('btn-secondary');
                } else {
                    throw new Error('Failed to send message: ' + result);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            })
            .finally(() => {
                // Re-enable form elements
                submitButton.disabled = false;
                messageInput.disabled = false;
                fileInputLabel.style.pointerEvents = 'auto';
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
            });
        });

        // Add file input change handler to show selected filename
        document.getElementById('fileInput').addEventListener('change', function() {
            const label = document.querySelector('label[for="fileInput"]');
            if (this.files[0]) {
                label.innerHTML = '<i class="fas fa-check"></i>';
                label.classList.remove('btn-secondary');
                label.classList.add('btn-success');
            } else {
                label.innerHTML = '<i class="fas fa-paperclip"></i>';
                label.classList.remove('btn-success');
                label.classList.add('btn-secondary');
            }
        });

        // Function to load chat messages
        function loadChat(userId) {
            fetch('../chat-screen/load_chat.php?user_id=' + userId)
                .then(response => response.text())
                .then(html => {
                    document.querySelector('.chat-messages').innerHTML = html;
                    const userItem = document.querySelector(`.user-item[data-user-id="${userId}"]`);
                    const username = userItem.querySelector('h6').textContent;
                    const userImage = userItem.querySelector('img').src;
                    document.querySelector('.chat-header h5').textContent = username;
                    document.querySelector('.chat-header .user-status').textContent = 'Online';
                    document.querySelector('.chat-header img').src = userImage;
                    
                    // Scroll to bottom of chat
                    const chatMessages = document.querySelector('.chat-messages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }

        // Add auto-scroll after sending message
        document.getElementById('chatForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const message = document.getElementById('messageInput').value.trim();
            const receiverId = document.getElementById('receiver_id').value;
            const fileInput = document.getElementById('fileInput');

            if (!receiverId || (!message && !fileInput.files[0])) return;

            const formData = new FormData();
            formData.append('receiver_id', receiverId);
            formData.append('message', message);
            
            if (fileInput.files[0]) {
                formData.append('attachment', fileInput.files[0]);
            }

            // Disable form elements during upload
            const submitButton = this.querySelector('button[type="submit"]');
            const messageInput = document.getElementById('messageInput');
            const fileInputLabel = document.querySelector('label[for="fileInput"]');
            
            submitButton.disabled = true;
            messageInput.disabled = true;
            fileInputLabel.style.pointerEvents = 'none';
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

            fetch('../chat-screen/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.trim() === 'success') {
                    loadChat(receiverId);
                    document.getElementById('messageInput').value = '';
                    document.getElementById('fileInput').value = '';
                    // Reset the attachment button style
                    const label = document.querySelector('label[for="fileInput"]');
                    label.innerHTML = '<i class="fas fa-paperclip"></i>';
                    label.classList.remove('btn-success');
                    label.classList.add('btn-secondary');
                } else {
                    throw new Error('Failed to send message: ' + result);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message);
            })
            .finally(() => {
                // Re-enable form elements
                submitButton.disabled = false;
                messageInput.disabled = false;
                fileInputLabel.style.pointerEvents = 'auto';
                submitButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
            });
        });

        // Add file input change handler to show selected filename
        document.getElementById('fileInput').addEventListener('change', function() {
            const label = document.querySelector('label[for="fileInput"]');
            if (this.files[0]) {
                label.innerHTML = '<i class="fas fa-check"></i>';
                label.classList.remove('btn-secondary');
                label.classList.add('btn-success');
            } else {
                label.innerHTML = '<i class="fas fa-paperclip"></i>';
                label.classList.remove('btn-success');
                label.classList.add('btn-secondary');
            }
        });

        // Add offer response handler
        function respondToOffer(messageId, response) {
            fetch('../chat-screen/respond_to_offer.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'message_id=' + messageId + '&response=' + response
            })
            .then(response => response.text())
            .then(result => {
                if (result === 'success') {
                    loadChat(document.getElementById('receiver_id').value);
                } else {
                    alert('Failed to respond to offer: ' + result);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while responding to the offer');
            });
        }
    });
    </script>
</body>
</html> 