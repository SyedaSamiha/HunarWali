<?php
//session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php?page=orders');
    exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Get order details with gig and buyer information
$query = "SELECT o.*, g.gig_title, g.gig_description, g.user_id as freelancer_id,
          u.username as buyer_name, u.email as buyer_email
          FROM orders o
          JOIN gigs g ON o.gig_id = g.id
          JOIN users u ON o.buyer_id = u.id
          WHERE o.id = ? AND g.user_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: dashboard.php?page=orders');
    exit();
}

// Get client review if exists
$reviewQuery = "SELECT r.*, u.username
               FROM reviews r
               JOIN users u ON r.user_id = u.id
               WHERE r.order_id = ? AND r.user_id = ?";

$reviewStmt = $conn->prepare($reviewQuery);
$reviewStmt->bind_param("ii", $order_id, $order['buyer_id']);
$reviewStmt->execute();
$clientReview = $reviewStmt->get_result()->fetch_assoc();

// Get freelancer feedback if exists
$feedbackQuery = "SELECT ff.*, u.username as freelancer_name
                 FROM freelancer_feedback ff
                 JOIN users u ON ff.freelancer_id = u.id
                 WHERE ff.order_id = ? AND ff.freelancer_id = ?";

$feedbackStmt = $conn->prepare($feedbackQuery);
$feedbackStmt->bind_param("ii", $order_id, $user_id);
$feedbackStmt->execute();
$freelancerFeedback = $feedbackStmt->get_result()->fetch_assoc();
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="card-title mb-4">Order Details #<?php echo $order_id; ?></h2>
            
            <!-- Order Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Service Information</h5>
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($order['gig_title']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($order['gig_description']); ?></p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($order['price'], 2); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Client Information</h5>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['buyer_email']); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order['created_at'])); ?></p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <h5>Order Status</h5>
                    <?php
                    $status_class = '';
                    switch (strtolower($order['status'])) {
                        case 'pending':
                            $status_class = 'bg-warning';
                            break;
                        case 'in_progress':
                            $status_class = 'bg-info';
                            break;
                        case 'completed':
                            $status_class = 'bg-success';
                            break;
                        case 'cancelled':
                            $status_class = 'bg-danger';
                            break;
                    }
                    ?>
                    <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($order['status']); ?></span>
                </div>
            </div>

            <!-- Reviews Section -->
            <?php if (strtolower($order['status']) === 'completed'): ?>
                <div class="row">
                    <!-- Client Review -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Client's Review</h5>
                                <?php if ($clientReview): ?>
                                    <div class="mb-2">
                                        <div class="rating-stars">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $clientReview['rating'] ? '★' : '☆';
                                            }
                                            ?>
                                        </div>
                                        <small class="text-muted">Posted on <?php echo date('M d, Y', strtotime($clientReview['created_at'])); ?></small>
                                    </div>
                                    <p class="card-text"><?php echo htmlspecialchars($clientReview['review_text']); ?></p>
                                <?php else: ?>
                                    <p class="card-text text-muted">Client hasn't provided a review yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Freelancer Feedback -->
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Your Feedback</h5>
                                <?php if ($freelancerFeedback): ?>
                                    <div class="mb-2">
                                        <p><strong>Overall Rating:</strong></p>
                                        <div class="rating-stars">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $freelancerFeedback['overall_rating'] ? '★' : '☆';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <p><strong>Communication:</strong></p>
                                        <div class="rating-stars">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $freelancerFeedback['communication_rating'] ? '★' : '☆';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <p><strong>Payment:</strong></p>
                                        <div class="rating-stars">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo $i <= $freelancerFeedback['payment_rating'] ? '★' : '☆';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <p class="card-text mt-3"><?php echo htmlspecialchars($freelancerFeedback['feedback_text']); ?></p>
                                    <small class="text-muted">Posted on <?php echo date('M d, Y', strtotime($freelancerFeedback['created_at'])); ?></small>
                                <?php else: ?>
                                    <?php if (strtolower($order['status']) === 'completed'): ?>
                                        <p class="card-text text-muted">You haven't provided feedback yet.</p>
                                        <a href="dashboard.php?page=freelancer-feedback&order_id=<?php echo $order_id; ?>" class="btn btn-primary">
                                            <i class="fas fa-star me-2"></i>Provide Feedback
                                        </a>
                                    <?php else: ?>
                                        <p class="card-text text-muted">You can provide feedback once the order is completed.</p>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="dashboard.php?page=order-tracking&id=<?php echo $order_id; ?>" class="btn btn-info me-2">
                    <i class="fas fa-truck me-2"></i>Track Order
                </a>
                
                <?php if (strtolower($order['status']) === 'pending'): ?>
                    <button class="btn btn-success me-2" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'in_progress')">
                        <i class="fas fa-play me-2"></i>Start Order
                    </button>
                <?php endif; ?>
                
                <?php if (strtolower($order['status']) === 'in_progress'): ?>
                    <button class="btn btn-success me-2" onclick="updateOrderStatus(<?php echo $order_id; ?>, 'completed')">
                        <i class="fas fa-check me-2"></i>Mark as Completed
                    </button>
                <?php endif; ?>
                
                <a href="dashboard.php?page=orders" class="btn btn-secondary me-2">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>
                <a href="../index.php" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i>Back to Main Site
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.rating-stars {
    color: #ffd700;
    font-size: 1.2em;
}
</style>

<script>
function updateOrderStatus(orderId, status) {
    if (confirm('Are you sure you want to update this order status?')) {
        fetch('process-order-update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId + '&status=' + status
        })
        .then(response => response.text())
        .then(() => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update order status. Please try again.');
        });
    }
}
</script>