<?php
// Universal JSON error handler
set_exception_handler(function($e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Exception: ' . $e->getMessage()]);
    exit();
});
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "PHP Error: $errstr in $errfile on line $errline"]);
    exit();
});
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Shutdown error: ' . $error['message']]);
        exit();
    }
});

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$message_id = $_POST['message_id'] ?? null;
$response = $_POST['response'] ?? null;

if (!$message_id || !in_array($response, ['accept', 'decline'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit();
}

$conn->query("START TRANSACTION");

$stmt = $conn->prepare("SELECT * FROM messages WHERE id = ? AND message_type = 'offer'");
$stmt->bind_param("i", $message_id);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();

if (!$message) {
    throw new Exception('Offer not found');
}

$offer_data = json_decode($message['message'], true);
$offer_data['status'] = $response;

$updated_message = json_encode($offer_data);
$stmt = $conn->prepare("UPDATE messages SET message = ? WHERE id = ?");
$stmt->bind_param("si", $updated_message, $message_id);
$stmt->execute();

if ($response === 'accept') {
    $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("iidsi", 
        $message['sender_id'], 
        $message['receiver_id'], 
        $offer_data['amount'],
        $offer_data['description'],
        $offer_data['delivery_time']
    );
    $stmt->execute();
}

$conn->query("COMMIT");

echo json_encode(['success' => true]);