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
$activeServices = 0;
$totalOrders = 0;
$orderStatusData = [];
$monthlyOrderData = [];

// Get active services count (orders with status 'In Progress')
try {
    $query = "SELECT COUNT(*) as active_count FROM orders WHERE buyer_id = ? AND status = 'In Progress'";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $activeServices = $row['active_count'];
        }
    }
} catch (Exception $e) {
    // Handle error silently
}

// Get total orders count
try {
    $query = "SELECT COUNT(*) as total_count FROM orders WHERE buyer_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $totalOrders = $row['total_count'];
        }
    }
} catch (Exception $e) {
    // Handle error silently
}



// Get order status distribution for pie chart
try {
    $query = "SELECT status, COUNT(*) as count FROM orders WHERE buyer_id = ? GROUP BY status";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $orderStatusData[] = [
                'status' => $row['status'],
                'count' => $row['count']
            ];
        }
    }
} catch (Exception $e) {
    // Handle error silently
}

// Get monthly order data for line chart (last 6 months)
try {
    $query = "SELECT 
                MONTH(created_at) as month, 
                YEAR(created_at) as year, 
                COUNT(*) as count 
              FROM orders 
              WHERE buyer_id = ? 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) 
              GROUP BY YEAR(created_at), MONTH(created_at) 
              ORDER BY YEAR(created_at), MONTH(created_at)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $currentUserId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $monthName = date("M", mktime(0, 0, 0, $row['month'], 1, $row['year']));
            $monthlyOrderData[] = [
                'month' => $monthName,
                'count' => $row['count']
            ];
        }
    }
} catch (Exception $e) {
    // Handle error silently
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Panel</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .content {
            padding: 30px;
        }

        .stat-card {
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .chart-container {
            position: relative;
            margin: auto;
            height: 300px;
            width: 100%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Page initialization code can go here
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-white text-center mb-4">Client Panel</h3>
                <nav>
                    <a href="index.php" class="active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="../hire-freelancer/hire.php">
                        <i class="fas fa-search"></i> Browse Services
                    </a>
                    <a href="ordered-services.php">
                        <i class="fas fa-list"></i> Ordered Services
                    </a>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i> Messages
                    </a>
                    <a href="../index.php">
                        <i class="fas fa-home"></i> Back to Main Site
                    </a>
                    <a href="logout.php" class="mt-5">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="container">
                    <h2 class="mb-4">Dashboard</h2>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card bg-primary text-white stat-card">
                                <div class="card-body">
                                    <h5 class="card-title">Active Services</h5>
                                    <p class="card-text display-4"><?php echo $activeServices; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-success text-white stat-card">
                                <div class="card-body">
                                    <h5 class="card-title">Total Orders</h5>
                                    <p class="card-text display-4"><?php echo $totalOrders; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts Row -->
                    <div class="row mt-4">
                        <!-- Order Status Pie Chart -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Order Status Distribution</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="orderStatusChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Monthly Orders Line Chart -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Monthly Orders</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="monthlyOrdersChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart Initialization -->
    <script>
        // Order Status Pie Chart
        const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
        const orderStatusChart = new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($orderStatusData, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($orderStatusData, 'count')); ?>,
                    backgroundColor: [
                        '#4e73df', // blue
                        '#1cc88a', // green
                        '#36b9cc', // turquoise
                        '#f6c23e', // yellow
                        '#e74a3b', // red
                        '#858796'  // gray
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', // blue
                        '#17a673', // green
                        '#2c9faf', // turquoise
                        '#dda20a', // yellow
                        '#be2617', // red
                        '#60616f'  // gray
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        titleColor: '#6e707e',
                        titleMarginBottom: 10,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((acc, data) => acc + data, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Monthly Orders Line Chart
        const monthlyCtx = document.getElementById('monthlyOrdersChart').getContext('2d');
        const monthlyOrdersChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlyOrderData, 'month')); ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?php echo json_encode(array_column($monthlyOrderData, 'count')); ?>,
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleColor: '#6e707e',
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        displayColors: false
                    }
                }
            }
        });
    </script>
</body>
</html>
