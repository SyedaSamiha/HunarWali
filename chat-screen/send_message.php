<?php
session_start();
require_once '../config/database.php';

// Clear any output buffer to prevent unwanted whitespace
ob_start();

// Validate session and request
if (!isset($_SESSION['user_id']) || !isset($_POST['receiver_id'])) {
    die('Invalid request');
}

$current_user_id = intval($_SESSION['user_id']);
$receiver_id = intval($_POST['receiver_id']);
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$attachment_url = null;

// Validate receiver_id
if ($receiver_id <= 0) {
    die('Invalid receiver ID');
}

// Define allowed file types and max size (5MB)
$allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
$max_file_size = 5 * 1024 * 1024; // 5MB in bytes

// Handle file upload if present
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Check for upload errors
    if ($_FILES['attachment']['error'] !== UPLOAD_ERR_OK) {
        die('File upload error: ' . $_FILES['attachment']['error']);
    }

    $upload_dir = '../uploads/attachments/';
    
    // Ensure upload directory exists with proper permissions
    if (!file_exists($upload_dir)) {
        $old_umask = umask(0);
        if (!mkdir($upload_dir, 0777, true)) {
            umask($old_umask);
            die('Failed to create upload directory. Check parent directory permissions.');
        }
        umask($old_umask);
    }

    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        if (!chmod($upload_dir, 0777)) {
            die('Upload directory is not writable and could not be fixed. Please set permissions manually (e.g., chmod 777 ' . realpath($upload_dir) . ')');
        }
    }

    // Validate file type
    $file_extension = strtolower(pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_types)) {
        die('Invalid file type. Allowed types: ' . implode(', ', $allowed_types));
    }

    // Validate file size
    if ($_FILES['attachment']['size'] > $max_file_size) {
        die('File size exceeds limit of ' . ($max_file_size / (1024 * 1024)) . 'MB');
    }

    // Generate unique filename
    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $unique_filename;

    // Move uploaded file
    if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $target_path)) {
        die('Failed to move uploaded file. Check directory permissions or disk space.');
    }

    // Store relative path instead of just filename
    $attachment_url = 'uploads/attachments/' . $unique_filename;
}

// If both message and attachment are empty, return error
if (empty($message) && empty($attachment_url)) {
    die('Message or attachment is required');
}

// Insert the message with attachment
$query = "INSERT INTO messages (sender_id, receiver_id, message, attachment_url, created_at, is_read) 
          VALUES (?, ?, ?, ?, NOW(), 0)";
$stmt = $conn->prepare($query);
if (!$stmt) {
    if ($attachment_url && file_exists($upload_dir . basename($attachment_url))) {
        unlink($upload_dir . basename($attachment_url));
    }
    die('Failed to prepare statement: ' . $conn->error);
}

$stmt->bind_param('iiss', $current_user_id, $receiver_id, $message, $attachment_url);

if ($stmt->execute()) {
    // Clear output buffer and send exact response
    ob_end_clean();
    echo 'success';
} else {
    // If message insert fails and we uploaded a file, delete it
    if ($attachment_url && file_exists($upload_dir . basename($attachment_url))) {
        unlink($upload_dir . basename($attachment_url));
    }
    ob_end_clean();
    echo 'Failed to send message: ' . $conn->error;
}

$stmt->close();
?>