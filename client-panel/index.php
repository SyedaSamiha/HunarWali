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
    </style>
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
                    <a href="ordered-services.php">
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
                    <h2 class="mb-4">Dashboard</h2>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Active Services</h5>
                                    <p class="card-text display-4">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Total Orders</h5>
                                    <p class="card-text display-4">0</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Unread Messages</h5>
                                    <p class="card-text display-4">0</p>
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
</body>
</html>
