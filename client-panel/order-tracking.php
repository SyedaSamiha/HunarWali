<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/database.php';



if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ordered-services.php');
    exit();
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Get order details with gig and seller information
$query = "SELECT o.*, 
          CASE WHEN g.gig_title IS NULL THEN 'Custom Order' ELSE g.gig_title END as gig_title, 
          CASE WHEN g.gig_description IS NULL THEN o.description ELSE g.gig_description END as gig_description, 
          CASE WHEN g.user_id IS NULL THEN o.seller_id ELSE g.user_id END as seller_id,
          u.username as buyer_name, u.email as buyer_email,
          s.username as seller_name, s.email as seller_email
          FROM orders o
          LEFT JOIN gigs g ON o.gig_id = g.id
          JOIN users u ON o.buyer_id = u.id
          JOIN users s ON o.seller_id = s.id
          WHERE o.id = ? AND o.buyer_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header('Location: ordered-services.php');
    exit();
}

// Get order tracking history
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
    $insert_tracking_stmt->bind_param("issi", $order_id, $order['status'], $initial_description, $order['seller_id']);
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking - HunarWali</title>
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

        .navbar {
            background-color: var(--dark-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: bold;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 30px;
            border-radius: 15px;
            color: white;
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

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

        .badge {
            padding: 10px 20px;
            font-size: 1rem;
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
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
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">HunarWali</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="ordered-services.php">My Orders</a>
                <a class="nav-link" href="messages.php">Messages</a>
                <a class="nav-link" href="../index.php">Back to Main Site</a>
                <a class="nav-link" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="welcome-section">
            <h2><i class="fas fa-truck me-2"></i>Order Tracking #<?php echo $order_id; ?></h2>
            <p>Track the progress of your order in real-time</p>
            <div class="mt-3">
                <a href="ordered-services.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Back to My Orders
                </a>
            </div>
        </div>

        <!-- Order Information Cards -->
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
                        <h5 class="card-title"><i class="fas fa-user me-2"></i>Freelancer Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['seller_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['seller_email']); ?></p>
                        <p><strong>Contact:</strong> 
                            <a href="messages.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-envelope me-1"></i>Send Message
                            </a>
                        </p>
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

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-actions me-2"></i>Actions</h5>
                        <div class="d-flex gap-2">
                            <a href="messages.php" class="btn btn-primary">
                                <i class="fas fa-envelope me-2"></i>Contact Freelancer
                            </a>
                            <a href="view-order.php?id=<?php echo $order_id; ?>" class="btn btn-info">
                                <i class="fas fa-eye me-2"></i>View Order Details
                            </a>
                            <a href="ordered-services.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to My Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>