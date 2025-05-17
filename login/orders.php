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

// Get user's orders
$user_id = $_SESSION['user_id'];
$query = "SELECT o.*, u.username as buyer_name, g.gig_title as gig_title 
          FROM orders o 
          JOIN users u ON o.buyer_id = u.id 
          JOIN gigs g ON o.gig_id = g.id 
          WHERE g.user_id = ? 
          ORDER BY o.created_at DESC";

// Debug query
// echo "Query: " . $query . "<br>";
// echo "User ID: " . $user_id . "<br>";

$stmt = $conn->prepare($query);
if (!$stmt) {
    // echo "Prepare failed: " . $conn->error . "<br>";
    exit();
}

$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    // echo "Execute failed: " . $stmt->error . "<br>";
    exit();
}

$result = $stmt->get_result();
// echo "Number of orders found: " . $result->num_rows . "<br>";
?>

<div class="container-fluid">
    <div class="welcome-section">
        <h2><i class="fas fa-shopping-cart me-2"></i>Orders Received</h2>
        <p>Manage and track all your service orders</p>
    </div>

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
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $status_class = '';
                                        switch ($row['status']) {
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
                                        <tr>
                                            <td>#<?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['gig_title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['buyer_name']); ?></td>
                                            <td>$<?php echo number_format($row['price'], 2); ?></td>
                                            <td><span class="badge <?php echo $status_class; ?>"><?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?></span></td>
                                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="viewOrderDetails(<?php echo $row['id']; ?>)">
                                                    <i class="fas fa-eye"></i> View
                                                </button>
                                                <?php if ($row['status'] === 'pending') { ?>
                                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(<?php echo $row['id']; ?>, 'in_progress')">
                                                        <i class="fas fa-play"></i> Start
                                                    </button>
                                                <?php } ?>
                                                <?php if ($row['status'] === 'in_progress') { ?>
                                                    <button class="btn btn-sm btn-success" onclick="updateOrderStatus(<?php echo $row['id']; ?>, 'completed')">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No orders received yet.</td>
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
    // Implement order details view functionality
    window.location.href = 'dashboard.php?page=order-details&id=' + orderId;
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
</script> 