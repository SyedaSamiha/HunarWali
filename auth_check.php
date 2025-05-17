<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include_once("../auth_check.php"); // Adjust the path if needed

// Database connection
$conn = new mysqli("localhost", "root", "", "freelance_website");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current user's id and username
$currentUserId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $currentUserId);
$stmt->execute();
$stmt->bind_result($currentUser);
$stmt->fetch();
$stmt->close();

$query = "
    SELECT u.id, u.username, u.role
    FROM users u
    WHERE u.id IN (
        SELECT DISTINCT 
            CASE 
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END AS chat_user_id
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
    ) AND u.id != ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiii", $currentUserId, $currentUserId, $currentUserId, $currentUserId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<div class="user-item" onclick="selectUser(\'' . htmlspecialchars($row['username']) . '\', ' . $row['id'] . ')">
            <div class="user-avatar">' . strtoupper(substr($row['username'], 0, 1)) . '</div>
            <div class="user-info">
                <div class="user-name">' . htmlspecialchars($row['username']) . '</div>
                <div class="user-role">' . htmlspecialchars($row['role']) . '</div>
                <div class="user-status"></div>
            </div>
          </div>';
}
$stmt->close();
?>