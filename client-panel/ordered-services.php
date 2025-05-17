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
                                u.username AS seller_username
                            FROM orders o
                            JOIN gigs g ON o.gig_id = g.id
                            JOIN users u ON o.seller_id = u.id
                            WHERE o.buyer_id = ?
                            ORDER BY o.created_at DESC
                        ";

                        $stmt = $conn->prepare($query);
                        if (!$stmt) {
                            throw new Exception("Failed to prepare statement: " . $conn->error);
                        }

                        $stmt->bind_param("i", $currentUserId);
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