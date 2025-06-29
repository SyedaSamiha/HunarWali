<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a log file
$logFile = __DIR__ . '/custom_order_debug.log';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Script started\n", FILE_APPEND);

function logError($message) {
    global $logFile;
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: " . $message . "\n", FILE_APPEND);
}

if (!isset($_SESSION['user_id'])) {
    logError("User not authenticated");
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logError("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Log received data
logError("Received POST data: " . print_r($_POST, true));

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$amount = floatval($_POST['amount']);
$description = trim($_POST['description']);
$delivery_time = intval($_POST['delivery_time']);

// Validate inputs
if (!$receiver_id) {
    logError("Receiver ID is missing");
    echo json_encode(['success' => false, 'message' => 'Receiver ID is required']);
    exit();
}

if ($amount <= 0) {
    logError("Invalid amount: " . $amount);
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit();
}

if (empty($description)) {
    logError("Description is empty");
    echo json_encode(['success' => false, 'message' => 'Description is required']);
    exit();
}

if ($delivery_time <= 0) {
    logError("Invalid delivery time: " . $delivery_time);
    echo json_encode(['success' => false, 'message' => 'Delivery time must be greater than 0']);
    exit();
}

try {
    // Test database connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    logError("Database connection successful");

    // Start transaction
    $conn->query("START TRANSACTION");
    logError("Transaction started");

    // Insert the custom order message
    $message = json_encode([
        'type' => 'custom_order',
        'amount' => $amount,
        'description' => $description,
        'delivery_time' => $delivery_time,
        'status' => 'pending'
    ]);

    logError("Prepared message: " . $message);

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, message_type, created_at) VALUES (?, ?, ?, 'custom_order', NOW())");
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $conn->error);
    }
    logError("Statement prepared successfully");

    $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
    logError("Parameters bound successfully");

    if (!$stmt->execute()) {
        throw new Exception("Failed to execute statement: " . $stmt->error);
    }
    logError("Statement executed successfully");

    // Commit transaction
    $conn->query("COMMIT");
    logError("Transaction committed successfully");

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->query("ROLLBACK");
    logError("Transaction rolled back");
    
    logError("Exception caught: " . $e->getMessage());
    
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}