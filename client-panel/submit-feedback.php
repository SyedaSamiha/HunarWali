<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ordered-services.php');
    exit();
}

$orderId = $_POST['order_id'];
$rating = isset($_POST['rating']) ? (float)$_POST['rating'] : 0;
$review = trim($_POST['review']);
$currentUserId = $_SESSION['user_id'];

// Validate inputs
if ($rating < 1 || $rating > 5) {
    $_SESSION['error'] = "Please select a valid rating (1-5 stars).";
    header('Location: view-order.php?id=' . $orderId . '#feedback');
    exit();
}

if (empty($review) || strlen($review) < 10) {
    $_SESSION['error'] = "Please provide a review with at least 10 characters.";
    header('Location: view-order.php?id=' . $orderId . '#feedback');
    exit();
}

try {
    // Get order details and verify it's completed
    $orderQuery = "SELECT o.gig_id, o.buyer_id, o.seller_id, o.status 
                  FROM orders o 
                  WHERE o.id = ? AND (o.buyer_id = ? OR o.seller_id = ?) 
                  AND o.status = 'Completed'";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("iii", $orderId, $currentUserId, $currentUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        throw new Exception("Invalid order or order is not completed.");
    }

    $gigId = $order['gig_id'];

    // Check if review already exists for this order and user
    $reviewQuery = "SELECT id FROM reviews WHERE order_id = ? AND user_id = ?";
    $stmtReview = $conn->prepare($reviewQuery);
    $stmtReview->bind_param("ii", $orderId, $currentUserId);
    $stmtReview->execute();
    $resultReview = $stmtReview->get_result();
    
    if ($resultReview->num_rows > 0) {
        throw new Exception("You have already submitted a review for this order.");
    }

    // Insert new review
    $insertQuery = "INSERT INTO reviews (order_id, gig_id, user_id, rating, review_text, created_at) 
                   VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiids", $orderId, $gigId, $currentUserId, $rating, $review);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Thank you for your review!";
    } else {
        throw new Exception("Failed to submit review. Please try again.");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: view-order.php?id=' . $orderId);
exit(); 