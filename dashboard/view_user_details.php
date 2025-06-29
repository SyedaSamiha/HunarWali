<?php
session_start();
// Check if the user is logged in as an admin
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

// Include database connection
require_once '../config/database.php';

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header("Location: admin.php");
    exit();
}

// Get user data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin.php");
    exit();
}

$user = $result->fetch_assoc();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $update_query = "UPDATE users SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("si", $new_status, $user_id);
    
    if ($update_stmt->execute()) {
        $user['status'] = $new_status;
        $success_message = "User status updated successfully!";
    } else {
        $error_message = "Error updating user status.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
        }
        .id-card-container {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 15px 0;
        }
        .id-card-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .id-card-image:hover {
            transform: scale(1.05);
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 16px;
        }
        .back-button {
            background: #6c757d;
            border: none;
            transition: all 0.3s ease;
        }
        .back-button:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        .approval-section {
            background: #e8f5e8;
            border: 2px solid #28a745;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .user-info-row {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">
                <i class="fas fa-user-shield"></i> Admin Dashboard
            </a>
            <a href="admin.php" class="btn btn-outline-light back-button">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- User Information Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-user"></i> User Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="user-info-row">
                                    <div class="info-label">User ID:</div>
                                    <div class="info-value">#<?php echo htmlspecialchars($user['id']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="user-info-row">
                                    <div class="info-label">Username:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['username']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="user-info-row">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="user-info-row">
                                    <div class="info-label">Role:</div>
                                    <div class="info-value">
                                        <span class="badge bg-primary"><?php echo htmlspecialchars(ucfirst($user['role'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="user-info-row">
                                    <div class="info-label">Gender:</div>
                                    <div class="info-value"><?php echo htmlspecialchars(ucfirst($user['gender'])); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="user-info-row">
                                    <div class="info-label">Current Status:</div>
                                    <div class="info-value">
                                        <?php
                                        $statusClass = '';
                                        switch($user['status']) {
                                            case 'Approved':
                                                $statusClass = 'success';
                                                break;
                                            case 'Pending Approval':
                                                $statusClass = 'warning';
                                                break;
                                            case 'Blocked':
                                                $statusClass = 'danger';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass; ?> status-badge">
                                            <?php echo htmlspecialchars($user['status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <?php if (!empty($user['bio'])): ?>
                            <div class="col-12">
                                <div class="user-info-row">
                                    <div class="info-label">Bio:</div>
                                    <div class="info-value"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- ID Card Verification Section -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-id-card"></i> ID Card Verification</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-center mb-3">Front ID Card</h5>
                                <?php if (!empty($user['id_card_front'])): ?>
                                    <div class="id-card-container text-center">
                                        <img src="../registration/<?php echo htmlspecialchars($user['id_card_front']); ?>" 
                                             alt="Front ID Card" 
                                             class="id-card-image"
                                             onclick="openImageModal(this.src, 'Front ID Card')">
                                        <p class="mt-2 text-muted">Click to enlarge</p>
                                    </div>
                                <?php else: ?>
                                    <div class="id-card-container text-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                        <p class="mt-2 text-muted">No front ID card uploaded</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-center mb-3">Back ID Card</h5>
                                <?php if (!empty($user['id_card_back'])): ?>
                                    <div class="id-card-container text-center">
                                        <img src="../registration/<?php echo htmlspecialchars($user['id_card_back']); ?>" 
                                             alt="Back ID Card" 
                                             class="id-card-image"
                                             onclick="openImageModal(this.src, 'Back ID Card')">
                                        <p class="mt-2 text-muted">Click to enlarge</p>
                                    </div>
                                <?php else: ?>
                                    <div class="id-card-container text-center">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                        <p class="mt-2 text-muted">No back ID card uploaded</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Approval Actions Card -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="fas fa-check-circle"></i> Approval Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="approval-section">
                            <h5 class="text-success mb-3"><i class="fas fa-shield-alt"></i> Verification Status</h5>
                            
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Update User Status:</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="Pending Approval" <?php echo ($user['status'] == 'Pending Approval') ? 'selected' : ''; ?>>
                                            Pending Approval
                                        </option>
                                        <option value="Approved" <?php echo ($user['status'] == 'Approved') ? 'selected' : ''; ?>>
                                            Approved
                                        </option>
                                        <option value="Blocked" <?php echo ($user['status'] == 'Blocked') ? 'selected' : ''; ?>>
                                            Blocked
                                        </option>
                                    </select>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save"></i> Update Status
                                    </button>
                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit User
                                    </a>
                                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete User
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-4">
                            <h5 class="text-primary mb-3"><i class="fas fa-bolt"></i> Quick Actions</h5>
                            <div class="d-grid gap-2">
                                <a href="admin.php" class="btn btn-outline-primary">
                                    <i class="fas fa-users"></i> View All Users
                                </a>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">ID Card Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="loadingSpinner" class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <img id="modalImage" src="" alt="ID Card" class="img-fluid" style="display: none;">
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function openImageModal(imageSrc, title) {
            // Show loading state
            document.getElementById('modalImage').src = '';
            document.getElementById('modalImage').style.display = 'none';
            document.getElementById('loadingSpinner').style.display = 'flex';
            document.getElementById('imageModalLabel').textContent = title;
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
            
            // Load image with loading indicator
            const img = new Image();
            img.onload = function() {
                document.getElementById('modalImage').src = imageSrc;
                document.getElementById('modalImage').style.display = 'block';
                document.getElementById('loadingSpinner').style.display = 'none';
            };
            img.onerror = function() {
                document.getElementById('modalImage').style.display = 'none';
                document.getElementById('loadingSpinner').style.display = 'none';
                document.querySelector('.modal-body').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Image not found or could not be loaded.</p></div>';
            };
            img.src = imageSrc;
        }
        
        // Add confirmation for status changes
        document.getElementById('status').addEventListener('change', function() {
            const currentStatus = '<?php echo $user['status']; ?>';
            const newStatus = this.value;
            
            if (newStatus === 'Blocked' && currentStatus !== 'Blocked') {
                if (!confirm('Are you sure you want to block this user? They will not be able to access the system.')) {
                    this.value = currentStatus;
                    return;
                }
            }
            
            if (newStatus === 'Approved' && currentStatus === 'Pending Approval') {
                if (!confirm('Are you sure you want to approve this user? They will be able to access the system.')) {
                    this.value = currentStatus;
                    return;
                }
            }
        });
    </script>
</body>
</html>