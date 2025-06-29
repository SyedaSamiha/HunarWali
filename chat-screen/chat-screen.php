<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelance_website";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Exclude current user from list
    $stmt = $conn->prepare("SELECT id, username, is_online, profile_picture FROM users WHERE id != :current_user ORDER BY username ASC");
    $stmt->execute([':current_user' => $_SESSION['user_id']]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Screen</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
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
        .chat-input .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .chat-input .btn-primary {
            background-color: #0d6efd;
            border: none;
        }
        .chat-input .btn-primary:hover {
            background-color: #0b5ed7;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            border-top: 1px solid #eaeaea;
            padding: 15px 20px;
        }
        .modal .form-label {
            font-weight: 500;
            color: #555;
            margin-bottom: 8px;
        }
        .modal .form-control {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ddd;
        }
        .modal .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .modal textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        .modal .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 4px;
            padding: 8px 20px;
        }
        .modal .btn-primary {
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            padding: 8px 20px;
        }
        .modal .btn-primary:hover {
            background-color: #218838;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .modal .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #555;
        }
        .modal .input-group .form-control {
            border-left: none;
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
        .online-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .online-status.online {
            background-color: #28a745;
        }
        .online-status.offline {
            background-color: #6c757d;
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
        .chat-input .btn-order {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            height: 42px;
        }
        .chat-input .btn-order:hover {
            background-color: #218838;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .chat-input .btn-order i {
            font-size: 1.2rem;
        }
        .order-message {
            background-color: #f0f8ff;
            border: 1px solid #d1e7ff;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }
        .order-title {
            color: #0056b3;
            margin-bottom: 10px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .order-details {
            font-size: 0.9rem;
        }
        .order-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
        .order-status {
            margin-top: 10px;
            font-weight: 500;
            color: #6c757d;
        }
    </style>
</head>
<body>
<?php include '../navbar/navbar.php'; ?>
<div class="container-fluid chat-container">
    <div class="row h-100">
        <!-- User List -->
        <div class="col-md-4 col-lg-3 user-list">
            <div class="p-3 border-bottom">
                <h4 class="mb-0">Chats</h4>
            </div>
            <?php foreach($users as $user): ?>
            <div class="user-item" data-user-id="<?php echo htmlspecialchars($user['id']); ?>">
                <div class="d-flex align-items-center">
                    <span class="online-status <?php echo $user['is_online'] ? 'online' : 'offline'; ?>"></span>
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
                        <small class="text-muted"><?php echo $user['is_online'] ? 'Online' : 'Offline'; ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Chat Area -->
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
                <!-- Messages will load here -->
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
                    <button type="button" class="btn btn-order me-2" id="sendOrderBtn">
                        <i class="fas fa-handshake"></i>
                        <span>Custom Order</span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Order Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">
                    <i class="fas fa-handshake"></i>
                    Custom Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="orderForm">
                    <input type="hidden" id="order_receiver_id" name="receiver_id">
                    <div class="mb-3">
                        <label for="orderAmount" class="form-label">Order Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">PKR</span>
                            <input type="number" class="form-control" id="orderAmount" name="amount" required min="1" step="0.01" placeholder="Enter amount">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="orderDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="orderDescription" name="description" rows="3" required placeholder="Describe your custom order details"></textarea>
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
                <button type="button" class="btn btn-primary" id="submitOrder">Submit Order</button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedUserId = null;
const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            selectedUserId = this.dataset.userId;
            document.getElementById('receiver_id').value = selectedUserId;
            document.getElementById('order_receiver_id').value = selectedUserId;

            fetch('load_chat.php?user_id=' + selectedUserId)
                .then(res => res.text())
                .then(html => {
                    document.querySelector('.chat-messages').innerHTML = html;
                    const username = this.querySelector('h6').textContent;
                    document.querySelector('.chat-header h5').textContent = username;
                    document.querySelector('.chat-header small').textContent = 'Chatting now';
                    document.querySelector('.chat-header img').src = this.querySelector('img').src;
                });
        });
    });
    
    // Custom Order button click handler
    document.getElementById('sendOrderBtn').addEventListener('click', function() {
        if (!selectedUserId) {
            alert('Please select a user first');
            return;
        }
        document.getElementById('order_receiver_id').value = selectedUserId;
        orderModal.show();
    });

    // Submit Custom Order handler
    document.getElementById('submitOrder').addEventListener('click', function() {
        const form = document.getElementById('orderForm');
        const formData = new FormData(form);

        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Disable submit button to prevent double submission
        const submitButton = document.getElementById('submitOrder');
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

        fetch('send_custom_order.php', {
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
                orderModal.hide();
                form.reset();
                fetch('load_chat.php?user_id=' + selectedUserId)
                    .then(res => res.text())
                    .then(html => {
                        document.querySelector('.chat-messages').innerHTML = html;
                    });
            } else {
                alert('Failed to submit custom order: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while submitting the custom order. Please try again.');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.innerHTML = 'Submit Order';
        });
    });

    document.getElementById('chatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const message = document.getElementById('messageInput').value.trim();
        const receiverId = document.getElementById('receiver_id').value;
        if (!receiverId || !message) return;

        fetch('send_message.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'receiver_id=' + encodeURIComponent(receiverId) + '&message=' + encodeURIComponent(message)
        })
        .then(res => res.text())
        .then(result => {
            if (result === 'success') {
                fetch('load_chat.php?user_id=' + receiverId)
                    .then(res => res.text())
                    .then(html => {
                        document.querySelector('.chat-messages').innerHTML = html;
                        document.getElementById('messageInput').value = '';
                    });
            } else {
                alert('Failed to send message: ' + result);
            }
        });
    });
});
</script>
</body>
</html>
