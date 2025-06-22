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
            padding: 15px;
            background-color: white;
            border-top: 1px solid #dee2e6;
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
                <form class="d-flex" id="chatForm" autocomplete="off">
                    <input type="hidden" id="receiver_id" name="receiver_id" value="">
                    <input type="text" class="form-control me-2" id="messageInput" placeholder="Type your message...">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedUserId = null;

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.user-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.user-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            selectedUserId = this.dataset.userId;
            document.getElementById('receiver_id').value = selectedUserId;

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
