<?php
session_start();
require_once '../profile-form-2/db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Freelance Platform</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="services.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
    <?php include '../navbar/navbar.php'; ?>
    </header>
    <div class="main-content">
        <section class="services-section">
            <h2>Our Services</h2>
            <div style="height: 20px;"></div>
            <div class="service-container">
                <div class="service-cards">
                    <?php
                    // Fetch all active services
                    $services_query = "SELECT * FROM services WHERE status = 'active' ORDER BY name";
                    $services_result = $conn->query($services_query);

                    while ($service = $services_result->fetch_assoc()) {
                        // Fetch sub-services for this service
                        $sub_services_query = "SELECT * FROM sub_services WHERE service_id = {$service['id']} AND status = 'active' ORDER BY name";
                        $sub_services_result = $conn->query($sub_services_query);
                        
                        // Get appropriate icon based on service name
                        $icon_class = 'fa-home'; // default icon
                        if (stripos($service['name'], 'remote') !== false) {
                            $icon_class = 'fa-laptop';
                        } elseif (stripos($service['name'], 'digital') !== false) {
                            $icon_class = 'fa-digital-tachograph';
                        } elseif (stripos($service['name'], 'creative') !== false) {
                            $icon_class = 'fa-paint-brush';
                        }
                        ?>
                        <div class="service-card">
                            <div class="icon">
                                <i class="fa <?php echo $icon_class; ?>"></i>
                            </div>
                            <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                            <p><?php echo htmlspecialchars($service['description']); ?></p>
                            
                            <?php if ($sub_services_result->num_rows > 0) { ?>
                                <div class="sub-services">
                                    <h4>Available Services:</h4>
                                    <ul>
                                        <?php while ($sub_service = $sub_services_result->fetch_assoc()) { ?>
                                            <li>
                                                <i class="fa fa-check-circle"></i> 
                                                <!-- This is the line which is writing the sub services on the screen -->
                                                <?php echo htmlspecialchars($sub_service['name']); ?> 
                                                
                                            </li>
                                          
                                            <!-- This is the line which is writing the description of the sub services on the screen -->
                                             <!-- <?php echo htmlspecialchars($sub_service['service_name_1']); ?>  -->
                            

                                        <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </section>
    </div>

    <?php include '../footer/footer.php'; ?>
</body>
</html>
