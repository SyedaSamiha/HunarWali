<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die('unauthorized');
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'] ?? null;
$message = $_POST['message'] ?? '';

if (!$receiver_id) {
    die('receiver_id is required');
}

try {
    $attachment_url = null;
    
    // Handle file upload if present
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        $fileName = $file['name'];
        $fileType = $file['type'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];
        
        // Validate file size (max 10MB)
        if ($fileSize > 10 * 1024 * 1024) {
            die('File size too large. Maximum size is 10MB.');
        }
        
        // Validate file type
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($fileType, $allowedTypes)) {
            die('Invalid file type. Allowed types: images, PDF, DOC, DOCX');
        }
        
        // Generate unique filename
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        
        // Create upload directory if it doesn't exist
        $uploadDir = __DIR__ . '/../uploads/attachments/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Move uploaded file
        $uploadPath = $uploadDir . $uniqueFileName;
        if (!move_uploaded_file($fileTmpName, $uploadPath)) {
            die('Failed to upload file');
        }
        
        $attachment_url = 'uploads/attachments/' . $uniqueFileName;
    }
    
    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, attachment_url, message_type) VALUES (?, ?, ?, ?, 'text')");
    $stmt->bind_param("iiss", $sender_id, $receiver_id, $message, $attachment_url);
    
    if ($stmt->execute()) {
        echo 'success';
    } else {
        throw new Exception("Failed to send message: " . $stmt->error);
    }
    
} catch (Exception $e) {
    // If there was an error and we uploaded a file, try to delete it
    if (isset($attachment_url) && file_exists(__DIR__ . '/../' . $attachment_url)) {
        unlink(__DIR__ . '/../' . $attachment_url);
    }
    die($e->getMessage());
}