<?php
session_start();

// Ensure proper JSON response headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if the user is logged in as an admin
if ($_SESSION['role'] != 'admin') {
    die(json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]));
}

// Check if we have the required parameters
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    die(json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]));
}

$user_id = intval($_POST['id']);
$new_status = $_POST['status'];

// Validate status
$allowed_statuses = ['Pending Approval', 'Approved', 'Blocked'];
if (!in_array($new_status, $allowed_statuses)) {
    die(json_encode([
        'success' => false,
        'message' => 'Invalid status value'
    ]));
}

try {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "freelance_website");

    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }

    // Update user status
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $new_status, $user_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No user found with the provided ID'
        ]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?> 