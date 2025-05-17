<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect to login page if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HunarWali</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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

        .sidebar {
            min-height: 100vh;
            background-color: var(--dark-color);
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar h3 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            transition: all 0.3s ease;
            border-radius: 5px;
            margin: 5px 10px;
        }

        .sidebar a:hover {
            background-color: var(--primary-color);
            transform: translateX(5px);
        }

        .sidebar .active {
            background-color: var(--primary-color);
        }

        .main-content {
            padding: 30px;
        }

        .welcome-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 20px;
            border-radius: 10px;
            color: white;
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-title {
            color: var(--dark-color);
            font-weight: 600;
        }

        .card-body {
            padding: 25px;
        }

        .display-6 {
            color: var(--primary-color);
            font-weight: bold;
        }

        .badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .list-group-item {
            border: none;
            padding: 15px 20px;
            margin-bottom: 5px;
            border-radius: 5px !important;
            background-color: var(--light-color);
        }

        .list-group-item:hover {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .text-muted {
            color: #6c757d !important;
        }

        .icon-container {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .icon-container i {
            color: white;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-center">HunarWali</h3>
                <nav>
                    <a href="dashboard.php" class="<?php echo (!isset($_GET['page'])) ? 'active' : ''; ?>"><i class="fas fa-home me-2"></i> Home</a>
                    <a href="dashboard.php?page=profile" class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'profile') ? 'active' : ''; ?>"><i class="fas fa-user me-2"></i> Profile</a>
                    <a href="dashboard.php?page=my-services" class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'my-services') ? 'active' : ''; ?>"><i class="fas fa-briefcase me-2"></i> My Services</a>
                    <a href="dashboard.php?page=orders" class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'orders') ? 'active' : ''; ?>"><i class="fas fa-shopping-cart me-2"></i> Orders</a>
                    <!-- <a href="dashboard.php?page=analytics" class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'analytics') ? 'active' : ''; ?>"><i class="fas fa-chart-bar me-2"></i> Analytics</a> -->
                    <a href="dashboard.php?page=messages" class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'messages') ? 'active' : ''; ?>"><i class="fas fa-envelope me-2"></i> Messages</a>
                    <!-- <a href="dashboard.php?page=settings" class="<?php echo (isset($_GET['page']) && $_GET['page'] === 'settings') ? 'active' : ''; ?>"><i class="fas fa-cog me-2"></i> Settings</a> -->
                    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?php
                // Check which page to display
                if (isset($_GET['page'])) {
                    switch ($_GET['page']) {
                        case 'my-services':
                            include('my-services.php');
                            break;
                        case 'orders':
                            include('orders.php');
                            break;
                        case 'gig-creation':
                            include('gig-creation.php');
                            break;
                        case 'gig-edit':
                            include('gig-edit.php');
                            break;
                        case 'process-gig-update':
                            include('process-gig-update.php');
                            break;
                        case 'profile':
                            include('profile.php');
                            break;
                        case 'analytics':
                            include('analytics.php');
                            break;
                        case 'messages':
                            include('messages.php');
                            break;
                        case 'settings':
                            include('settings.php');
                            break;
                        default:
                            // Display default dashboard content
                            include('home.php');
                            break;
                    }
                } else {
                    // Display default dashboard content
                    include('home.php');
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
