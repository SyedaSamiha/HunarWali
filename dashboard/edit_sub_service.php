<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location:/Login/index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "freelance_website");
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = (int)$_POST['service_id'];
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE sub_services SET service_id=$service_id, name='$name', description='$description', status='$status' WHERE id=$id";
    
    if ($conn->query($sql)) {
        header("Location: sub_services.php?success=2");
    } else {
        header("Location: sub_services.php?error=2");
    }
    exit();
}

$result = $conn->query("SELECT * FROM sub_services WHERE id=$id");
$sub_service = $result->fetch_assoc();

if (!$sub_service) {
    header("Location: sub_services.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Sub Service - Admin Dashboard</title>
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
            <h1 class="display-5"><i class="fas fa-edit"></i> Edit Sub Service</h1>
            <p class="lead">Modify the sub-service details below.</p>
        </div>

        <div class="table-container">
            <form action="edit_sub_service.php?id=<?php echo $id; ?>" method="POST">
                <div class="mb-3">
                    <label for="serviceId" class="form-label">Parent Service</label>
                    <select class="form-select" id="serviceId" name="service_id" required>
                        <?php
                        $services = $conn->query("SELECT id, name FROM services WHERE status = 'active'");
                        while ($service = $services->fetch_assoc()) {
                            $selected = ($service['id'] == $sub_service['service_id']) ? 'selected' : '';
                            echo "<option value='{$service['id']}' {$selected}>{$service['name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subServiceName" class="form-label">Sub Service Name</label>
                    <input type="text" class="form-control" id="subServiceName" name="name" value="<?php echo htmlspecialchars($sub_service['name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="subServiceDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="subServiceDescription" name="description" rows="3" required><?php echo htmlspecialchars($sub_service['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="subServiceStatus" class="form-label">Status</label>
                    <select class="form-select" id="subServiceStatus" name="status" required>
                        <option value="active" <?php echo $sub_service['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $sub_service['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="text-end">
                    <a href="sub_services.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Sub Service</button>
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