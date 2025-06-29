<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug information
// echo "Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'not set') . "<br>";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
require_once '../config/database.php';

// --- Start: Auto-create order_tracking table ---
try {
    $create_table_query = "CREATE TABLE IF NOT EXISTS order_tracking (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        status VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        updated_by INT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (updated_by) REFERENCES users(id)
    )";

    if (!$conn->query($create_table_query)) {
        throw new Exception("Error creating order_tracking table: " . $conn->error);
    }

    // Check and create indexes
    $index_queries = [
        'idx_order_tracking_order_id' => "CREATE INDEX idx_order_tracking_order_id ON order_tracking(order_id)",
        'idx_order_tracking_updated_at' => "CREATE INDEX idx_order_tracking_updated_at ON order_tracking(updated_at)"
    ];

    foreach ($index_queries as $index_name => $index_query) {
        $check_index_sql = "SHOW INDEX FROM order_tracking WHERE Key_name = '$index_name'";
        $result = $conn->query($check_index_sql);
        if ($result && $result->num_rows == 0) {
            if (!$conn->query($index_query)) {
                // Non-fatal, just log or ignore in production.
            }
        }
    }
} catch (Exception $e) {
    die("Database setup failed: " . $e->getMessage());
}
// --- End: Auto-create order_tracking table ---

// Get user's orders with feedback information
$user_id = $_SESSION['user_id'];
$query = "SELECT o.*, u.username as buyer_name, g.gig_title as gig_title,
          CASE 
            WHEN ff.id IS NOT NULL THEN 'freelancer_feedback'
            WHEN r.id IS NOT NULL THEN 'client_feedback'
            ELSE 'no_feedback'
          END as feedback_status
          FROM orders o 
          JOIN users u ON o.buyer_id = u.id 
          JOIN gigs g ON o.gig_id = g.id 
          LEFT JOIN reviews r ON o.id = r.order_id AND r.user_id = o.buyer_id
          LEFT JOIN freelancer_feedback ff ON o.id = ff.order_id AND ff.freelancer_id = g.user_id
          WHERE g.user_id = ? 
          ORDER BY o.created_at DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();
?>

<div class="container-fluid">
    <div class="welcome-section">
        <h2><i class="fas fa-shopping-cart me-2"></i>Orders Received</h2>
        <p>Manage and track all your service orders</p>
        <div class="mt-3">
            <a href="dashboard.php?page=view-freelancer-feedback" class="btn btn-outline-light">
                <i class="fas fa-star me-2"></i>View My Submitted Feedback
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Gig</th>
                                    <th>Buyer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Feedback</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status_class = '';
                                        switch (strtolower($row['status'])) {
                                            case 'order placed':
                                                $status_class = 'bg-warning';
                                                break;
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

                                        // Determine feedback status display
                                        $feedback_badge = '';
                                        if (strtolower($row['status']) === 'completed') {
                                            switch ($row['feedback_status']) {
                                                case 'client_feedback':
                                                    $feedback_badge = '<span class="badge bg-info"><i class="fas fa-star me-1"></i>Client Reviewed</span>';
                                                    break;
                                                case 'freelancer_feedback':
                                                    $feedback_badge = '<span class="badge bg-success"><i class="fas fa-star me-1"></i>You Reviewed</span>';
                                                    break;
                                                case 'no_feedback':
                                                    $feedback_badge = '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending Review</span>';
                                                    break;
                                            }
                                        }
                                        
                                        ?>
                                        <tr>
                                            <td>#<?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['gig_title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                            <td>PKR <?php echo number_format($row['price'], 2); ?></td>
                                            <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?></span></td>
                                            <td><?php echo $feedback_badge; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="viewOrderDetails(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <button class="btn btn-sm btn-info" onclick="trackOrder(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-truck"></i> Track
                                                </button>
                                                <?php if (strtolower($row['status']) === 'pending') { ?>
                                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(<?php echo $row['id']; ?>, 'in_progress')">
                                                        <i class="fas fa-play"></i> Start
                                                    </button>
                                                <?php } ?>
                                                <?php if (strtolower($row['status']) === 'in_progress') { ?>
                                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(<?php echo $row['id']; ?>, 'completed')">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                <?php } ?>
                                                <?php if (strtolower($row['status']) === 'completed' && $row['feedback_status'] === 'no_feedback') { ?>
                                                    <button class="btn btn-sm btn-info" onclick="provideFeedback(<?php echo $row['id']; ?>)">
                                                        <i class="fas fa-star"></i> Review Client
                                                    </button>
                                                <?php } ?>
                                                <?php if (strtolower($row['status']) === 'completed' && $row['feedback_status'] === 'client_feedback') { ?>
                                                    <button class="btn btn-sm btn-info" onclick="provideFeedback(<?php echo $row['id']; ?>)">
                                                        <i class="fas fa-star"></i> Review Client
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No orders received yet.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    window.location.href = 'dashboard.php?page=order-details&id=' + orderId;
}

function trackOrder(orderId) {
    window.location.href = 'dashboard.php?page=order-tracking&id=' + orderId;
}

function updateOrderStatus(orderId, status) {
    if (confirm('Are you sure you want to update this order status?')) {
        fetch('process-order-update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'order_id=' + orderId + '&status=' + status
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating order status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order status.');
        });
    }
}

function provideFeedback(orderId) {
    window.location.href = 'freelancer-feedback.php?order_id=' + orderId;
}
</script>