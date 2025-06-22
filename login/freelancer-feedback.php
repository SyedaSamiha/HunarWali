<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$user_id = $_SESSION['user_id'];

// Get order details and verify it belongs to the freelancer and is completed
$query = "SELECT o.*, u.username as buyer_name, g.gig_title, g.user_id as freelancer_id 
          FROM orders o 
          JOIN users u ON o.buyer_id = u.id 
          JOIN gigs g ON o.gig_id = g.id 
          WHERE o.id = ? AND g.user_id = ? AND LOWER(o.status) = 'completed'";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: dashboard.php?page=orders');
    exit();
}

// Check if freelancer has already given feedback
$feedback_query = "SELECT id FROM freelancer_feedback WHERE order_id = ? AND freelancer_id = ?";
$stmt_feedback = $conn->prepare($feedback_query);
$stmt_feedback->bind_param("ii", $order_id, $user_id);
$stmt_feedback->execute();
$feedback_result = $stmt_feedback->get_result();

$has_feedback = $feedback_result->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provide Feedback - HunarWali</title>
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
            max-width: 600px;
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

        .order-info {
            background-color: var(--light-color);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 20px 0;
        }

        .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .star.active {
            color: #FFD700;
        }

        .star:hover {
            color: #FFD700;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 15px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 107, 0.25);
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

        .btn-secondary {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="feedback-container">
            <div class="feedback-header">
                <h2><i class="fas fa-star me-2"></i>Provide Feedback</h2>
                <p>Share your experience working with this client</p>
            </div>

            <div class="feedback-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <div class="order-info">
                    <h5><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Order ID:</strong> #<?php echo $order['id']; ?><br>
                            <strong>Gig:</strong> <?php echo htmlspecialchars($order['gig_title']); ?><br>
                            <strong>Client:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Amount:</strong> $<?php echo number_format($order['price'], 2); ?><br>
                            <strong>Status:</strong> <span class="badge bg-success">Completed</span><br>
                            <strong>Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                        </div>
                    </div>
                </div>

                <?php if ($has_feedback): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        You have already provided feedback for this order.
                    </div>
                    <a href="dashboard.php?page=orders" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Orders
                    </a>
                <?php else: ?>
                    <form action="process-freelancer-feedback.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        
                        <div class="mb-4">
                            <label class="form-label"><strong>Rate your experience with this client:</strong></label>
                            <div class="rating-stars">
                                <i class="fas fa-star star" data-rating="1"></i>
                                <i class="fas fa-star star" data-rating="2"></i>
                                <i class="fas fa-star star" data-rating="3"></i>
                                <i class="fas fa-star star" data-rating="4"></i>
                                <i class="fas fa-star star" data-rating="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="rating" value="0" required>
                            <div class="text-center text-muted">
                                <small>Click on the stars to rate (1-5)</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="feedback_text" class="form-label"><strong>Your Feedback:</strong></label>
                            <textarea class="form-control" id="feedback_text" name="feedback_text" rows="5" 
                                      placeholder="Share your experience working with this client. What went well? Any suggestions for improvement?" 
                                      required minlength="10"></textarea>
                            <div class="form-text">Minimum 10 characters required</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><strong>Communication Quality:</strong></label>
                            <select class="form-select" name="communication_rating" required>
                                <option value="">Select rating</option>
                                <option value="5">Excellent - Very responsive and clear</option>
                                <option value="4">Good - Generally responsive</option>
                                <option value="3">Average - Adequate communication</option>
                                <option value="2">Poor - Slow to respond</option>
                                <option value="1">Very Poor - Unresponsive</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><strong>Payment Experience:</strong></label>
                            <select class="form-select" name="payment_rating" required>
                                <option value="">Select rating</option>
                                <option value="5">Excellent - Prompt payment</option>
                                <option value="4">Good - Timely payment</option>
                                <option value="3">Average - Standard payment time</option>
                                <option value="2">Poor - Delayed payment</option>
                                <option value="1">Very Poor - Payment issues</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                            </button>
                            <a href="dashboard.php?page=orders" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Orders
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star rating functionality
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                ratingInput.value = rating;
                
                // Update star display
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });

            star.addEventListener('mouseenter', function() {
                const rating = this.getAttribute('data-rating');
                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.style.color = '#FFD700';
                    }
                });
            });

            star.addEventListener('mouseleave', function() {
                const currentRating = ratingInput.value;
                stars.forEach((s, index) => {
                    if (index < currentRating) {
                        s.style.color = '#FFD700';
                    } else {
                        s.style.color = '#ddd';
                    }
                });
            });
        });
    </script>
</body>
</html> 