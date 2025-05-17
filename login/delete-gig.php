<?php
session_start();
require_once('../config/database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Check if gig_id is provided
if (!isset($_POST['gig_id'])) {
    echo json_encode(['success' => false, 'message' => 'No gig ID provided']);
    exit();
}

$gig_id = $_POST['gig_id'];
$user_id = $_SESSION['user_id'];

// Verify that the gig belongs to the user
$check_query = "SELECT id FROM gigs WHERE id = ? AND user_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ii", $gig_id, $user_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Gig not found or unauthorized']);
    exit();
}

// Delete the gig
$delete_query = "DELETE FROM gigs WHERE id = ? AND user_id = ?";
$delete_stmt = $conn->prepare($delete_query);
$delete_stmt->bind_param("ii", $gig_id, $user_id);

if ($delete_stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting gig']);
}

$delete_stmt->close();
$conn->close();
?> 