<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordered Services - Client Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar a.active {
            background-color: #0d6efd;
        }
        .sidebar i {
            margin-right: 10px;
        }
        .content {
            padding: 20px;
        }
        .alert {
            margin-top: 10px;
        }
    </style>
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
                    <a href="logout.php" class="mt-5">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="container">
                    <h2 class="mb-4">Ordered Services</h2>
                    <?php
                    session_start();
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

                    // Enable error reporting
                    error_reporting(E_ALL);
                    ini_set('display_errors', 1);

                    if (!isset($_SESSION['user_id'])) {
                        header('Location: ../login/login.php');
                        exit();
                    }

                    $currentUserId = $_SESSION['user_id'];
                    $message = '';

                    // Handle status update
                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
                        $orderId = $_POST['order_id'];
                        $newStatus = $_POST['status'];

                        try {
                            $updateQuery = "UPDATE orders SET status = ? WHERE id = ? AND buyer_id = ?";
                            $stmt = $conn->prepare($updateQuery);
                            if (!$stmt) {
                                throw new Exception("Failed to prepare update statement: " . $conn->error);
                            }
                            $stmt->bind_param("sii", $newStatus, $orderId, $currentUserId);
                            if ($stmt->execute()) {
                                $message = "<div class='alert alert-success'>Order status updated successfully!</div>";
                            } else {
                                throw new Exception("Failed to update order status: " . $conn->error);
                            }
                        } catch (Exception $e) {
                            $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                        }
                    }

                    // Fetch orders
                    try {
                        $query = "
                            SELECT 
                                o.id, 
                                g.gig_title AS service_name, 
                                o.created_at, 
                                o.status, 
                                u.username AS seller_username,
                                CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END as has_reviewed
                            FROM orders o
                            JOIN gigs g ON o.gig_id = g.id
                            JOIN users u ON o.seller_id = u.id
                            LEFT JOIN reviews r ON r.order_id = o.id AND r.user_id = ?
                            WHERE o.buyer_id = ?
                            ORDER BY o.created_at DESC
                        ";

                        $stmt = $conn->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Failed to prepare statement: " . $conn->error);
                        }

                        $stmt->bind_param("ii", $currentUserId, $currentUserId);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $orders = $result->fetch_all(MYSQLI_ASSOC);
                    } catch (Exception $e) {
                        $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                        $orders = [];
                    }
                    ?>
                    <?php echo $message; ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Service Name</th>
                                            <th>Order Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (count($orders) > 0): ?>
                                            <?php foreach ($orders as $order): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                                    <td>
                                                        <form method="POST" style="display:inline;">
                                                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                                            <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                                                <option value="Order Placed" <?php echo $order['status'] === 'Order Placed' ? 'selected' : ''; ?>>Order Placed</option>
                                                                <option value="In Progress" <?php echo $order['status'] === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                                                                <option value="Pending Review" <?php echo $order['status'] === 'Pending Review' ? 'selected' : ''; ?>>Pending Review</option>
                                                                <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>Completed</option>
                                                            </select>
                                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm ms-2"><i class="fas fa-save"></i> Update</button>
                                                        </form>
                                                        <a href="view-order.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-info btn-sm ms-2"><i class="fas fa-eye"></i> View</a>
                                                        <a href="order-tracking.php?id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-warning btn-sm ms-2"><i class="fas fa-truck"></i> Track</a>
                                                        <?php if ($order['status'] === 'Completed' && !$order['has_reviewed']): ?>
                                                            <a href="view-order.php?id=<?php echo htmlspecialchars($order['id']); ?>&section=feedback" class="btn btn-success btn-sm ms-2"><i class="fas fa-star"></i> Give Feedback</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center">No orders found</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
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