<?php
// Enable error reporting at the top of the file
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order - Client Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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

        .sidebar {
            min-height: 100vh;
            background-color: var(--dark-color);
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar h3 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s ease;
            border-radius: 5px;
            margin: 5px 10px;
        }

        .sidebar a:hover {
            background-color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar .active {
            background-color: var(--primary-color);
        }

        .content {
            padding: 30px;
        }
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }
        .rating input {
            display: none;
        }
        .rating label {
            cursor: pointer;
            font-size: 30px;
            color: #ddd;
            padding: 5px;
        }
        .rating input:checked ~ label,
        .rating label:hover,
        .rating label:hover ~ label {
            color: #ffd700;
        }
        .review-box {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .review-box .rating-display {
            color: #ffd700;
            font-size: 20px;
        }
        .review-box .review-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
    <script>
        // Page initialization
        document.addEventListener('DOMContentLoaded', function() {
            // Page initialization code can go here
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-white text-center mb-4">Client Panel</h3>
                <nav>
                    <a href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="ordered-services.php" class="active">
                        <i class="fas fa-list"></i> Ordered Services
                    </a>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                    <a href="../index.php">
                        <i class="fas fa-home"></i> Back to Main Site
                    </a>
                    <a href="logout.php" class="mt-5">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="container">
                    <h2 class="mb-4">Order Details</h2>
                    <?php
                    // Database connection already established at the top of the file

                    if (!isset($_SESSION['user_id'])) {
                        header('Location: ../login/login.php');
                        exit();
                    }

                    if (!isset($_GET['id'])) {
                        header('Location: ordered-services.php');
                        exit();
                    }

                    $orderId = $_GET['id'];
                    $currentUserId = $_SESSION['user_id'];

                    try {
                        // Debug information
                        error_log("Fetching order details for ID: " . $orderId . " and user ID: " . $currentUserId);

                        $query = "
                            SELECT 
                                o.*,
                                g.gig_title,
                                g.gig_description,
                                u.username as seller_username,
                                u.email as seller_email
                            FROM orders o
                            LEFT JOIN gigs g ON o.gig_id = g.id
                            JOIN users u ON o.seller_id = u.id
                            WHERE o.id = ? AND (o.buyer_id = ? OR o.seller_id = ?)
                        ";

                        $stmt = $conn->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Failed to prepare statement: " . $conn->error);
                        }

                        $stmt->bind_param("iii", $orderId, $currentUserId, $currentUserId);
                        if (!$stmt->execute()) {
                            throw new Exception("Failed to execute statement: " . $stmt->error);
                        }

                        $result = $stmt->get_result();
                        $order = $result->fetch_assoc();

                        if (!$order) {
                            throw new Exception("Order not found or you don't have permission to view it.");
                        }

                        // Fetch reviews for this order
                        $reviewQuery = "
                            SELECT r.*, u.username 
                            FROM reviews r
                            JOIN users u ON r.user_id = u.id
                            WHERE r.order_id = ?
                            ORDER BY r.created_at DESC
                        ";
                        error_log("Review Query: " . $reviewQuery);
                        error_log("Order ID: " . $orderId);
                        
                        $reviewStmt = $conn->prepare($reviewQuery);
                        if (!$reviewStmt) {
                            error_log("MySQL Error: " . $conn->error);
                            throw new Exception("Failed to prepare review statement: " . $conn->error);
                        }
                        
                        $reviewStmt->bind_param("i", $orderId);
                        $reviewStmt->execute();
                        $reviews = $reviewStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        // Fetch freelancer feedback for this order
                        $freelancerFeedbackQuery = "
                            SELECT ff.*, u.username as freelancer_name
                            FROM freelancer_feedback ff
                            JOIN users u ON ff.freelancer_id = u.id
                            WHERE ff.order_id = ?
                            ORDER BY ff.created_at DESC
                        ";
                        
                        $freelancerFeedbackStmt = $conn->prepare($freelancerFeedbackQuery);
                        if (!$freelancerFeedbackStmt) {
                            error_log("MySQL Error: " . $conn->error);
                            throw new Exception("Failed to prepare freelancer feedback statement: " . $conn->error);
                        }
                        
                        $freelancerFeedbackStmt->bind_param("i", $orderId);
                        $freelancerFeedbackStmt->execute();
                        $freelancerFeedback = $freelancerFeedbackStmt->get_result()->fetch_all(MYSQLI_ASSOC);

                        // Check if current user has already submitted a review
                        $hasReviewed = false;
                        foreach ($reviews as $review) {
                            if ($review['user_id'] == $currentUserId) {
                                $hasReviewed = true;
                                break;
                            }
                        }

                    } catch (Exception $e) {
                        error_log("Error in view-order.php: " . $e->getMessage());
                        echo "<div class='alert alert-danger'>" . htmlspecialchars($e->getMessage()) . "</div>";
                        exit();
                    }

                    // Display any session messages
                    if (isset($_SESSION['message'])) {
                        echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['message']) . "</div>";
                        unset($_SESSION['message']);
                    }
                    if (isset($_SESSION['error'])) {
                        echo "<div class='alert alert-danger'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                        unset($_SESSION['error']);
                    }
                    ?>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order #<?php echo htmlspecialchars($order['id']); ?></h5>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h6>Service Details</h6>
                                    <?php if ($order['gig_id']): ?>
                                        <p><strong>Service Name:</strong> <?php echo htmlspecialchars($order['gig_title']); ?></p>
                                        <p><strong>Description:</strong> <?php echo htmlspecialchars($order['gig_description']); ?></p>
                                    <?php else: ?>
                                        <p><strong>Service Name:</strong> Custom Order</p>
                                        <p><strong>Description:</strong> <?php echo htmlspecialchars($order['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6>Order Information</h6>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                                    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
                                    <p><strong>Seller:</strong> <?php echo htmlspecialchars($order['seller_username']); ?></p>
                                    <p><strong>Price:</strong> PKR <?php echo htmlspecialchars($order['price']); ?></p>
                                </div>
                            </div>
                            
                            <!-- Reviews Section -->
                            <div class="mt-4">
                                <h6>Reviews</h6>
                                <?php if (!empty($reviews)): ?>
                                    <?php foreach ($reviews as $review): ?>
                                        <div class="review-box">
                                            <div class="review-meta">
                                                <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                                <span class="ms-2"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                            </div>
                                            <div class="rating-display">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= $review['rating'] ? '★' : '☆';
                                                }
                                                ?>
                                            </div>
                                            <p class="mt-2"><?php echo htmlspecialchars($review['review_text']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No reviews yet.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Freelancer Feedback Section -->
                            <div class="mt-4">
                                <h6>Freelancer Feedback</h6>
                                <?php if (!empty($freelancerFeedback)): ?>
                                    <?php foreach ($freelancerFeedback as $feedback): ?>
                                        <div class="review-box">
                                            <div class="review-meta">
                                                <strong><?php echo htmlspecialchars($feedback['freelancer_name']); ?> (Freelancer)</strong>
                                                <span class="ms-2"><?php echo date('M d, Y', strtotime($feedback['created_at'])); ?></span>
                                            </div>
                                            <div class="rating-display">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    echo $i <= $feedback['overall_rating'] ? '★' : '☆';
                                                }
                                                ?>
                                                <span class="ms-2 text-muted">(Overall Rating)</span>
                                            </div>
                                            <div class="mt-2">
                                                <small class="text-muted">
                                                    <strong>Communication:</strong> 
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo $i <= $feedback['communication_rating'] ? '★' : '☆';
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <strong>Payment:</strong> 
                                                    <?php
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        echo $i <= $feedback['payment_rating'] ? '★' : '☆';
                                                    }
                                                    ?>
                                                </small>
                                            </div>
                                            <p class="mt-2"><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No freelancer feedback yet.</p>
                                <?php endif; ?>
                            </div>

                            <?php if ($order['status'] === 'Completed' && !$hasReviewed): ?>
                            <div class="mt-4" id="feedback">
                                <h6>Submit Your Review</h6>
                                <form action="submit-feedback.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="rating">
                                            <?php for($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                            <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="review" class="form-label">Your Review</label>
                                        <textarea class="form-control" id="review" name="review" rows="4" placeholder="Please share your experience with this service..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Review</button>
                                </form>
                            </div>
                            <?php endif; ?>

                            <div class="mt-4">
                                <a href="ordered-services.php" class="btn btn-secondary">Back to Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>