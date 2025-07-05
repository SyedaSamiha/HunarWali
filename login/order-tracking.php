<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
          u.username as buyer_name, u.email as buyer_email,
          f.username as freelancer_name, f.email as freelancer_email
          FROM orders o
          JOIN gigs g ON o.gig_id = g.id
          JOIN users u ON o.buyer_id = u.id
          JOIN users f ON g.user_id = f.id
          WHERE o.id = ? AND (g.user_id = ? OR o.buyer_id = ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $order_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: dashboard.php?page=orders');
    exit();
}

// Check if there are any tracking entries for this order
$check_tracking_query = "SELECT COUNT(*) as count FROM order_tracking WHERE order_id = ?";
$check_tracking_stmt = $conn->prepare($check_tracking_query);
if ($check_tracking_stmt === false) {
    die("Error preparing check tracking query: " . $conn->error);
}
$check_tracking_stmt->bind_param("i", $order_id);
$check_tracking_stmt->execute();
$check_result = $check_tracking_stmt->get_result();
$tracking_count = $check_result->fetch_assoc()['count'];

// If no tracking entries exist, create an initial one based on the order's current status
if ($tracking_count == 0) {
    $initial_description = "Order was placed";
    $insert_tracking_query = "INSERT INTO order_tracking (order_id, status, description, updated_by) VALUES (?, ?, ?, ?)";
    $insert_tracking_stmt = $conn->prepare($insert_tracking_query);
    if ($insert_tracking_stmt === false) {
        die("Error preparing insert tracking query: " . $conn->error);
    }
    $insert_tracking_stmt->bind_param("issi", $order_id, $order['status'], $initial_description, $order['freelancer_id']);
    $insert_tracking_stmt->execute();
}

// Get order tracking history
$tracking_query = "SELECT ot.*, u.username as updated_by_name
                  FROM order_tracking ot
                  JOIN users u ON ot.updated_by = u.id
                  WHERE ot.order_id = ?
                  ORDER BY ot.updated_at DESC";

$tracking_stmt = $conn->prepare($tracking_query);
if ($tracking_stmt === false) {
    die("Error preparing tracking query: " . $conn->error);
}
$tracking_stmt->bind_param("i", $order_id);
$tracking_stmt->execute();
$tracking_result = $tracking_stmt->get_result();

// Determine user role
$is_freelancer = ($order['freelancer_id'] == $user_id);
$is_buyer = ($order['buyer_id'] == $user_id);
?>

<div class="container-fluid">
    <div class="welcome-section">
        <h2><i class="fas fa-truck me-2"></i>Order Tracking #<?php echo $order_id; ?></h2>
        <p>Track the progress of your order in real-time</p>
        <div class="mt-3">
            <a href="dashboard.php?page=orders" class="btn btn-outline-light me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Orders
            </a>
            <a href="../index.php" class="btn btn-outline-light">
                <i class="fas fa-home me-2"></i>Back to Main Site
            </a>
        </div>
    </div>

    <!-- Order Information Card -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($order['gig_title']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($order['gig_description']); ?></p>
                    <p><strong>Price:</strong> PKR <?php echo number_format($order['price'], 2); ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Contact Information</h5>
                    <?php if ($is_freelancer): ?>
                        <p><strong>Client:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?></p>
                        <p><strong>Client Email:</strong> <?php echo htmlspecialchars($order['buyer_email']); ?></p>
                    <?php else: ?>
                        <p><strong>Freelancer:</strong> <?php echo htmlspecialchars($order['freelancer_name']); ?></p>
                        <p><strong>Freelancer Email:</strong> <?php echo htmlspecialchars($order['freelancer_email']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-flag me-2"></i>Current Status</h5>
                    <?php
                    $status_class = '';
                    $status_icon = '';
                    switch (strtolower($order['status'])) {
                        case 'pending':
                            $status_class = 'bg-warning';
                            $status_icon = 'fas fa-clock';
                            break;
                        case 'in_progress':
                            $status_class = 'bg-info';
                            $status_icon = 'fas fa-cogs';
                            break;
                        case 'completed':
                            $status_class = 'bg-success';
                            $status_icon = 'fas fa-check-circle';
                            break;
                        case 'cancelled':
                            $status_class = 'bg-danger';
                            $status_icon = 'fas fa-times-circle';
                            break;
                        default:
                            $status_class = 'bg-secondary';
                            $status_icon = 'fas fa-question-circle';
                    }
                    ?>
                    <span class="badge <?php echo $status_class; ?> fs-6">
                        <i class="<?php echo $status_icon; ?> me-2"></i>
                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking Timeline -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-history me-2"></i>Order History & Timeline</h5>
                    
                    <?php if ($tracking_result->num_rows > 0): ?>
                        <div class="timeline">
                            <?php while ($track = $tracking_result->fetch_assoc()): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($track['status']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y H:i', strtotime($track['updated_at'])); ?> 
                                                by <?php echo htmlspecialchars($track['updated_by_name']); ?>
                                            </small>
                                        </div>
                                        <p class="timeline-description mb-0">
                                            <?php echo htmlspecialchars($track['description']); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">No tracking history available yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status (for freelancers only) -->
    <?php if ($is_freelancer && strtolower($order['status']) !== 'completed' && strtolower($order['status']) !== 'cancelled'): ?>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-edit me-2"></i>Update Order Status</h5>
                        <form id="updateStatusForm">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="newStatus" class="form-label">New Status</label>
                                    <select class="form-select" id="newStatus" name="newStatus" required>
                                        <?php if (strtolower($order['status']) === 'pending'): ?>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                        <?php elseif (strtolower($order['status']) === 'in_progress'): ?>
                                            <option value="completed">Completed</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label for="statusDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="statusDescription" name="statusDescription" rows="2" placeholder="Add a description for this status update..." required></textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Status
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    background-color: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.timeline-content {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.timeline-header h6 {
    color: var(--dark-color);
    font-weight: 600;
}

.timeline-description {
    color: #6c757d;
    line-height: 1.6;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    padding: 10px 20px;
    font-size: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const updateStatusForm = document.getElementById('updateStatusForm');
    
    if (updateStatusForm) {
        updateStatusForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('order_id', <?php echo $order_id; ?>);
            
            fetch('update-order-tracking.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order status updated successfully!');
                    location.reload();
                } else {
                    alert('Error updating order status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the order status.');
            });
        });
    }
});
</script>