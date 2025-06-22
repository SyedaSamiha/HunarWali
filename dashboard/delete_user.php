<?php
session_start();
// Check if the user is logged in as an admin
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

// Include database connection
require_once '../config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header("Location: admin.php");
    exit();
}

// Check if user exists and get user info
$query = "SELECT username, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: admin.php");
    exit();
}

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "You cannot delete your own account.";
    header("Location: admin.php");
    exit();
}

// Handle deletion confirmation
if (isset($_POST['confirm_delete'])) {
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Delete related records first (foreign key constraints)
        
        // Delete messages
        $delete_messages = "DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?";
        $stmt = $conn->prepare($delete_messages);
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
        }
        
        // Delete orders where user is buyer or seller
        $delete_orders = "DELETE FROM orders WHERE buyer_id = ? OR seller_id = ?";
        $stmt = $conn->prepare($delete_orders);
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
        }
        
        // Delete gigs
        $delete_gigs = "DELETE FROM gigs WHERE user_id = ?";
        $stmt = $conn->prepare($delete_gigs);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }
        
        // Delete feedback (if exists) - using a simpler approach
        $delete_feedback = "DELETE FROM feedback WHERE order_id IN (SELECT id FROM orders WHERE buyer_id = ? OR seller_id = ?)";
        $stmt = $conn->prepare($delete_feedback);
        if ($stmt) {
            $stmt->bind_param("ii", $user_id, $user_id);
            $stmt->execute();
        }
        
        // Delete reviews (if exists)
        $delete_reviews = "DELETE FROM reviews WHERE user_id = ?";
        $stmt = $conn->prepare($delete_reviews);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        }
        
        // Finally, delete the user
        $delete_user = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_user);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            if ($stmt->affected_rows > 0) {
                // Commit transaction
                $conn->commit();
                $_SESSION['success'] = "User '{$user['username']}' has been deleted successfully.";
                header("Location: admin.php");
                exit();
            } else {
                throw new Exception("Failed to delete user. User may not exist.");
            }
        } else {
            throw new Exception("Failed to prepare user deletion statement.");
        }
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
        header("Location: admin.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: #343a40;
            padding-top: 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #495057;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .top-navbar {
            margin-left: 250px;
        }
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .top-navbar {
                margin-left: 0;
            }
        }
        .confirmation-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 30px;
            margin-top: 20px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="px-3 mb-4">
            <h4 class="text-white"><i class="fas fa-user-shield"></i> Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services.php"><i class="fas fa-cogs"></i> Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="members.php"><i class="fas fa-users"></i> Members</a>
            </li>
            <li class="nav-item mt-4">
                <a class="nav-link" href="/Login/index.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark top-navbar">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" id="sidebarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-trash text-danger"></i> Delete User</h1>
            <a href="admin.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>

        <div class="confirmation-container">
            <div class="text-center mb-4">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                <h2 class="mt-3">Confirm Deletion</h2>
            </div>

            <div class="warning-box">
                <h5><i class="fas fa-exclamation-triangle text-warning"></i> Warning</h5>
                <p class="mb-0">You are about to delete the user <strong><?php echo htmlspecialchars($user['username']); ?></strong> (Role: <?php echo ucfirst($user['role']); ?>).</p>
            </div>

            <div class="alert alert-danger">
                <h6><i class="fas fa-info-circle"></i> This action will:</h6>
                <ul class="mb-0">
                    <li>Permanently delete the user account</li>
                    <li>Remove all associated messages</li>
                    <li>Delete all orders related to this user</li>
                    <li>Remove all gigs created by this user</li>
                    <li>Delete all feedback and reviews related to this user</li>
                </ul>
                <p class="mb-0 mt-2"><strong>This action cannot be undone!</strong></p>
            </div>

            <form method="POST" action="" class="text-center">
                <div class="d-flex justify-content-center gap-3">
                    <button type="submit" name="confirm_delete" class="btn btn-danger btn-lg">
                        <i class="fas fa-trash"></i> Yes, Delete User
                    </button>
                    <a href="admin.php" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html> 