<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get freelancer's submitted feedback
$query = "SELECT ff.*, o.id as order_id, g.gig_title, u.username as client_name
          FROM freelancer_feedback ff
          JOIN orders o ON ff.order_id = o.id
          JOIN gigs g ON o.gig_id = g.id
          JOIN users u ON ff.client_id = u.id
          WHERE ff.freelancer_id = ?
          ORDER BY ff.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Feedback - HunarWali</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #FF6B6B;
            --secondary-color: #4ECDC4;
            --dark-color: #2C3E50;
            --light-color: #F7F9FC;
            --accent-color: #FFE66D;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-color);
        }

        .feedback-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .feedback-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .feedback-body {
            padding: 40px;
        }

        .feedback-item {
            background-color: var(--light-color);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary-color);
        }

        .rating-stars {
            color: #FFD700;
            font-size: 1.2rem;
        }

        .feedback-meta {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #e55a5a;
            border-color: #e55a5a;
            transform: translateY(-2px);
        }

        .no-feedback {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }

        .no-feedback i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="feedback-container">
            <div class="feedback-header">
                <h2><i class="fas fa-star me-2"></i>My Submitted Feedback</h2>
                <p>Reviews you've provided to your clients</p>
            </div>

            <div class="feedback-body">
                <?php if (empty($feedbacks)): ?>
                    <div class="no-feedback">
                        <i class="fas fa-star"></i>
                        <h4>No Feedback Submitted Yet</h4>
                        <p>You haven't submitted any feedback to clients yet. When you complete orders, you'll be able to provide feedback here.</p>
                        <a href="dashboard.php?page=orders" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($feedbacks as $feedback): ?>
                        <div class="feedback-item">
                            <div class="feedback-meta">
                                <strong>Order #<?php echo $feedback['order_id']; ?></strong> • 
                                <strong><?php echo htmlspecialchars($feedback['client_name']); ?></strong> • 
                                <?php echo date('M d, Y', strtotime($feedback['created_at'])); ?>
                            </div>
                            
                            <h5><?php echo htmlspecialchars($feedback['gig_title']); ?></h5>
                            
                            <div class="row mt-3">
                                <div class="col-md-4">
                                    <strong>Overall Rating:</strong><br>
                                    <span class="rating-stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $feedback['overall_rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Communication:</strong><br>
                                    <span class="rating-stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $feedback['communication_rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Payment:</strong><br>
                                    <span class="rating-stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $feedback['payment_rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <strong>Your Feedback:</strong>
                                <p class="mt-2"><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="text-center mt-4">
                        <a href="dashboard.php?page=orders" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Orders
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 