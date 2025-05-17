<?php
$mysqli = new mysqli("localhost", "root", "", "freelance_website"); // replace with your DB credentials

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_id = $_POST['sender_id'] ?? null;
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $created_at = date("Y-m-d H:i:s");
    $attachment_url = null;

    // Handle file upload if present
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
        $targetFile = $uploadDir . $filename;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $targetFile)) {
            $attachment_url = 'uploads/' . $filename;
        } else {
            echo 'File upload failed';
            exit;
        }
    }

    // Only allow sending if there is a message or an attachment
    if (empty($message) && !$attachment_url) {
        echo 'Message or attachment required';
        exit;
    }

    $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, message, attachment_url, created_at, is_read) VALUES (?, ?, ?, ?, ?, 0)");
    
    $stmt->bind_param("iisss", $sender_id, $receiver_id, $message, $attachment_url, $created_at);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'Failed to send message' . $attachment_url;
    }
    exit;
}
?>
