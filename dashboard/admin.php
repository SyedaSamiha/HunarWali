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
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
        .status-select {
            min-width: 140px;
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
                <a class="nav-link active" href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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
        <div class="welcome-section">
            <h1 class="display-5"><i class="fas fa-tachometer-alt"></i> Welcome to Admin Dashboard</h1>
            <p class="lead">Manage your users and system settings from here.</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="table-container">
            <h2 class="mb-4"><i class="fas fa-users"></i> User Management</h2>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $conn = new mysqli("localhost", "root", "", "freelance_website");
                        $result = $conn->query("SELECT * FROM users");

                        while ($row = $result->fetch_assoc()) {
                            $statusClass = '';
                            switch($row['status']) {
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
                            
                            echo "<tr>
                                    <td>{$row['id']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['email']}</td>
                                    <td><span class='badge bg-primary'>{$row['role']}</span></td>
                                    <td>{$row['gender']}</td>
                                    <td>
                                        <select class='form-select form-select-sm status-select text-{$statusClass}' 
                                                data-user-id='{$row['id']}' 
                                                onchange='updateStatus(this)'>
                                            <option value='Pending Approval' " . ($row['status'] == 'Pending Approval' ? 'selected' : '') . ">Pending Approval</option>
                                            <option value='Approved' " . ($row['status'] == 'Approved' ? 'selected' : '') . ">Approved</option>
                                            <option value='Blocked' " . ($row['status'] == 'Blocked' ? 'selected' : '') . ">Blocked</option>
                                        </select>
                                    </td>
                                    <td class='action-buttons'>
                                        <div class='btn-group' role='group'>
                                            <a href='view_user_details.php?id={$row['id']}' class='btn btn-sm btn-info'><i class='fas fa-eye'></i> View Details</a>
                                            <a href='edit_user.php?id={$row['id']}' class='btn btn-sm btn-warning'><i class='fas fa-edit'></i> Edit</a>
                                            <a href='delete_user.php?id={$row['id']}' class='btn btn-sm btn-danger'><i class='fas fa-trash'></i> Delete</a>
                                        </div>
                                    </td>
                                </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });

        // Initialize Select2
        $(document).ready(function() {
            $('.status-select').select2({
                minimumResultsForSearch: Infinity, // Disable search
                width: '100%'
            });
        });

        // Function to update user status
        function updateStatus(selectElement) {
            const userId = selectElement.getAttribute('data-user-id');
            const newStatus = selectElement.value;
            const originalValue = $(selectElement).find('option[selected]').val();
            
            // Show loading state
            $(selectElement).prop('disabled', true);
            
            // Send AJAX request
            $.ajax({
                url: 'update_user_status.php',
                method: 'POST',
                dataType: 'json',
                data: {
                    id: userId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const alertHtml = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle"></i> ${response.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.welcome-section').after(alertHtml);
                        
                        // Update select color
                        const statusColors = {
                            'Approved': 'success',
                            'Pending Approval': 'warning',
                            'Blocked': 'danger'
                        };
                        $(selectElement).removeClass('text-success text-warning text-danger')
                                      .addClass(`text-${statusColors[newStatus]}`);
                        
                        // Update the selected state
                        $(selectElement).find('option').removeAttr('selected');
                        $(selectElement).find(`option[value="${newStatus}"]`).attr('selected', 'selected');
                    } else {
                        // Revert selection on error
                        $(selectElement).val(originalValue);
                        alert('Error updating status: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // Revert selection on error
                    $(selectElement).val(originalValue);
                    console.error('AJAX Error:', xhr.responseText);
                    alert('Error updating status. Please try again.');
                },
                complete: function() {
                    // Re-enable select
                    $(selectElement).prop('disabled', false);
                }
            });
        }
    </script>
</body>
</html>
