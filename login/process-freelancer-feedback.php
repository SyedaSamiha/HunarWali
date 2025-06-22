<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php?page=orders');
    exit();
}

$order_id = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
$rating = isset($_POST['rating']) ? (float)$_POST['rating'] : 0;
$feedback_text = trim($_POST['feedback_text']);
$communication_rating = isset($_POST['communication_rating']) ? (int)$_POST['communication_rating'] : 0;
$payment_rating = isset($_POST['payment_rating']) ? (int)$_POST['payment_rating'] : 0;
$freelancer_id = $_SESSION['user_id'];

// Validate inputs
if ($rating < 1 || $rating > 5) {
    $_SESSION['error'] = "Please select a valid overall rating (1-5 stars).";
    header('Location: freelancer-feedback.php?order_id=' . $order_id);
    exit();
}

if (empty($feedback_text) || strlen($feedback_text) < 10) {
    $_SESSION['error'] = "Please provide feedback with at least 10 characters.";
    header('Location: freelancer-feedback.php?order_id=' . $order_id);
    exit();
}

if ($communication_rating < 1 || $communication_rating > 5) {
    $_SESSION['error'] = "Please select a valid communication rating.";
    header('Location: freelancer-feedback.php?order_id=' . $order_id);
    exit();
}

if ($payment_rating < 1 || $payment_rating > 5) {
    $_SESSION['error'] = "Please select a valid payment rating.";
    header('Location: freelancer-feedback.php?order_id=' . $order_id);
    exit();
}

try {
    // Get order details and verify it belongs to the freelancer and is completed
    $orderQuery = "SELECT o.*, o.buyer_id as client_id, g.user_id as freelancer_id 
                  FROM orders o 
                  JOIN gigs g ON o.gig_id = g.id 
                  WHERE o.id = ? AND g.user_id = ? AND LOWER(o.status) = 'completed'";
    $stmt = $conn->prepare($orderQuery);
    $stmt->bind_param("ii", $order_id, $freelancer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        throw new Exception("Invalid order or order is not completed.");
    }

    $client_id = $order['client_id'];

    // Check if freelancer has already given feedback for this order
    $feedbackQuery = "SELECT id FROM freelancer_feedback WHERE order_id = ? AND freelancer_id = ?";
    $stmtFeedback = $conn->prepare($feedbackQuery);
    $stmtFeedback->bind_param("ii", $order_id, $freelancer_id);
    $stmtFeedback->execute();
    $resultFeedback = $stmtFeedback->get_result();
    
    if ($resultFeedback->num_rows > 0) {
        throw new Exception("You have already submitted feedback for this order.");
    }

    // Insert new freelancer feedback
    $insertQuery = "INSERT INTO freelancer_feedback (order_id, freelancer_id, client_id, overall_rating, communication_rating, payment_rating, feedback_text, created_at) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("iiiddis", $order_id, $freelancer_id, $client_id, $rating, $communication_rating, $payment_rating, $feedback_text);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Thank you for your feedback! Your review has been submitted successfully.";
    } else {
        throw new Exception("Failed to submit feedback. Please try again.");
    }

} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
}

header('Location: dashboard.php?page=orders');
exit();
?> 