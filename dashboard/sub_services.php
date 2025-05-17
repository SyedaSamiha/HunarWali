<?php
session_start();
// Check if the user is logged in as an admin
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sub Services Management - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .navbar-brand {
            font-weight: bold;
        }
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        .action-buttons a {
            margin: 0 5px;
        }
        .welcome-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        /* Sidebar Styles */
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
                <a class="nav-link active" href="sub_services.php"><i class="fas fa-list"></i> Sub Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-users"></i> Members</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-address-book"></i> Contact Info</a>
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
        <div class="welcome-section">
            <h1 class="display-5"><i class="fas fa-list"></i> Sub Services Management</h1>
            <p class="lead">Manage your sub-services and their details from here.</p>
        </div>

        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-list"></i> Sub Services List</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubServiceModal">
                    <i class="fas fa-plus"></i> Add New Sub Service
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Sub Service Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = new mysqli("localhost", "root", "", "freelance_website");
                        $result = $conn->query("
                            SELECT ss.*, s.name as service_name 
                            FROM sub_services ss 
                            JOIN services s ON ss.service_id = s.id 
                            ORDER BY ss.id DESC
                        ");

                        while ($row = $result->fetch_assoc()) {
                            $statusClass = $row['status'] == 'active' ? 'bg-success' : 'bg-danger';
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['service_name']}</td>
                                    <td>{$row['name']}</td>
                                    <td>{$row['description']}</td>
                                    <td><span class='badge {$statusClass}'>{$row['status']}</span></td>
                                    <td>{$row['created_at']}</td>
                                    <td class='action-buttons'>
                                        <a href='edit_sub_service.php?id={$row['id']}' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Edit</a>
                                        <a href='delete_sub_service.php?id={$row['id']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Delete</a>
                                    </td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Sub Service Modal -->
    <div class="modal fade" id="addSubServiceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Sub Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="add_sub_service.php" method="POST">
                        <div class="mb-3">
                            <label for="serviceId" class="form-label">Parent Service</label>
                            <select class="form-select" id="serviceId" name="service_id" required>
                                <option value="">Select a Service</option>
                                <?php
                                $services = $conn->query("SELECT id, name FROM services WHERE status = 'active'");
                                while ($service = $services->fetch_assoc()) {
                                    echo "<option value='{$service['id']}'>{$service['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="subServiceName" class="form-label">Sub Service Name</label>
                            <input type="text" class="form-control" id="subServiceName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="subServiceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="subServiceDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="subServiceStatus" class="form-label">Status</label>
                            <select class="form-select" id="subServiceStatus" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Sub Service</button>
                        </div>
                    </form>
                </div>
            </div>
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