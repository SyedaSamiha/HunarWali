<?php 
session_start();
require_once '../config/database.php';

// Initialize the base query
$query = "SELECT g.*, u.username AS seller_name, s.name AS service_name, ss.name AS sub_service_name
          FROM gigs g
          JOIN users u ON g.user_id = u.id
          LEFT JOIN services s ON g.service_id = s.id
          LEFT JOIN sub_services ss ON g.sub_service_id = ss.id
          WHERE 1=1";

// Handle search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query .= " AND (g.gig_title LIKE '%$search%' OR g.gig_description LIKE '%$search%')";
}

// Handle domestic sub-service filter
if (isset($_GET['domestic_sub_service_id']) && !empty($_GET['domestic_sub_service_id'])) {
    $domestic_sub_service_id = (int)$_GET['domestic_sub_service_id'];
    $query .= " AND g.sub_service_id = $domestic_sub_service_id AND (s.name LIKE '%domestic%' OR s.name NOT LIKE '%remote%')";
}

// Handle remote sub-service filter
if (isset($_GET['remote_sub_service_id']) && !empty($_GET['remote_sub_service_id'])) {
    $remote_sub_service_id = (int)$_GET['remote_sub_service_id'];
    $query .= " AND g.sub_service_id = $remote_sub_service_id AND s.name LIKE '%remote%'";
}

// Handle category filter (sub-service)
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $query .= " AND g.category = '$category'";
}

// Handle specific service filter
if (isset($_GET['service_id']) && !empty($_GET['service_id'])) {
    $service_id = (int)$_GET['service_id'];
    $query .= " AND g.service_id = $service_id";
}

// Handle sub-service filter
if (isset($_GET['sub_service_id']) && !empty($_GET['sub_service_id'])) {
    $sub_service_id = (int)$_GET['sub_service_id'];
    $query .= " AND g.sub_service_id = $sub_service_id";
}

// Handle price range filter
if (isset($_GET['price_range']) && !empty($_GET['price_range'])) {
    $price_range = explode('-', $_GET['price_range']);
    if (count($price_range) === 2) {
        $min_price = (int)$price_range[0];
        $max_price = (int)$price_range[1];
        $query .= " AND g.gig_pricing BETWEEN $min_price AND $max_price";
    } elseif (strpos($_GET['price_range'], '+') !== false) {
        $min_price = (int)str_replace('+', '', $_GET['price_range']);
        $query .= " AND g.gig_pricing >= $min_price";
    }
}

$query .= " ORDER BY g.created_at DESC";
$result = $conn->query($query);

$gigs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Handle gig image - use actual image if exists, otherwise use default
        $image_path = '';
        if (!empty($row['gig_images']) && file_exists('../' . $row['gig_images'])) {
            $image_path = '../' . $row['gig_images'];
        } else {
            $image_path = 'https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg';
        }
        
        $gigs[] = [
            'id' => $row['id'],
            'image' => $image_path,
            'seller' => $row['seller_name'],
            'badge' => '', // You can add logic for seller level if you have it
            'title' => $row['gig_title'],
            'description' => $row['gig_description'],
            'rating' => 0, // Add rating logic if you have a reviews table
            'reviews' => 0, // Add review count logic if you have a reviews table
            'price' => $row['gig_pricing'] // Price in PKR
        ];
    }
    // Save all gig IDs in session for later use
    $_SESSION['gig_ids'] = array_column($gigs, 'id');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hire Freelancers - HunarWali</title>
    <link rel="stylesheet" href="hire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <?php include '../navbar/navbar.php'; ?>
    </header>

    <div class="main-content" style="max-width: 1200px; margin: 2rem auto;">
        <section class="filters-section">
            <div class="section-header">
                <div class="icon-container">
                    <i class="fas fa-search-dollar" style="color: var(--primary-color); font-size: 2.5rem;"></i>
                </div>
                <h2>Find the Perfect Freelancer</h2>
                <p style="max-width: 700px; margin: 0 auto; color: var(--text-light);">Discover talented freelancers who can help bring your projects to life with quality and efficiency.</p>
            </div>
            <form method="GET" action="" class="filters">
                <!-- Domestic Sub-Services Filter -->
                <div class="dropdown">
                    <button type="button" class="dropbtn"><i class="fa fa-home"></i> Domestic Services <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <?php
                        // Fetch domestic services
                        $domestic_services_query = "SELECT * FROM services WHERE status = 'active' AND (name LIKE '%domestic%' OR name NOT LIKE '%remote%') ORDER BY name";
                        $domestic_services_result = $conn->query($domestic_services_query);
                        
                        // Get all sub-services for domestic services
                        $domestic_sub_services = [];
                        while ($service = $domestic_services_result->fetch_assoc()) {
                            $sub_services_query = "SELECT ss.* FROM sub_services ss 
                                                  WHERE ss.service_id = ? AND ss.status = 'active' 
                                                  ORDER BY ss.name";
                            $stmt = $conn->prepare($sub_services_query);
                            $stmt->bind_param("i", $service['id']);
                            $stmt->execute();
                            $sub_services_result = $stmt->get_result();
                            
                            while ($sub_service = $sub_services_result->fetch_assoc()) {
                                $domestic_sub_services[] = [
                                    'id' => $sub_service['id'],
                                    'name' => $sub_service['name'],
                                    'service_name' => $service['name']
                                ];
                            }
                        }
                        
                        // Display domestic sub-services
                        foreach ($domestic_sub_services as $sub_service) {
                        ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['domestic_sub_service_id' => $sub_service['id']], ['remote_sub_service_id' => null])); ?>" 
                               <?php echo (isset($_GET['domestic_sub_service_id']) && $_GET['domestic_sub_service_id'] == $sub_service['id']) ? 'class="active"' : ''; ?>>
                                <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($sub_service['name']); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
                
                <!-- Remote Sub-Services Filter -->
                <div class="dropdown">
                    <button type="button" class="dropbtn"><i class="fa fa-laptop"></i> Remote Services <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <?php
                        // Fetch remote services
                        $remote_services_query = "SELECT * FROM services WHERE status = 'active' AND name LIKE '%remote%' ORDER BY name";
                        $remote_services_result = $conn->query($remote_services_query);
                        
                        // Get all sub-services for remote services
                        $remote_sub_services = [];
                        while ($service = $remote_services_result->fetch_assoc()) {
                            $sub_services_query = "SELECT ss.* FROM sub_services ss 
                                                  WHERE ss.service_id = ? AND ss.status = 'active' 
                                                  ORDER BY ss.name";
                            $stmt = $conn->prepare($sub_services_query);
                            $stmt->bind_param("i", $service['id']);
                            $stmt->execute();
                            $sub_services_result = $stmt->get_result();
                            
                            while ($sub_service = $sub_services_result->fetch_assoc()) {
                                $remote_sub_services[] = [
                                    'id' => $sub_service['id'],
                                    'name' => $sub_service['name'],
                                    'service_name' => $service['name']
                                ];
                            }
                        }
                        
                        // Display remote sub-services
                        foreach ($remote_sub_services as $sub_service) {
                        ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['remote_sub_service_id' => $sub_service['id']], ['domestic_sub_service_id' => null])); ?>" 
                               <?php echo (isset($_GET['remote_sub_service_id']) && $_GET['remote_sub_service_id'] == $sub_service['id']) ? 'class="active"' : ''; ?>>
                                <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($sub_service['name']); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            
                <div class="dropdown">
                    <button type="button" class="dropbtn">Price Range <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '0-1000'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '0-1000') ? 'class="active"' : ''; ?>>PKR 0 - 1,000</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '1000-5000'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '1000-5000') ? 'class="active"' : ''; ?>>PKR 1,000 - 5,000</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '5000-10000'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '5000-10000') ? 'class="active"' : ''; ?>>PKR 5,000 - 10,000</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '10000+'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '10000+') ? 'class="active"' : ''; ?>>PKR 10,000+</a>
                    </div>
                </div>

                <div class="search-box">
                    <input type="text" name="search" placeholder="Search gigs..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>

            <!-- Active Filters Section -->
            <div class="active-filters">
                <?php if (isset($_GET['domestic_sub_service_id']) || isset($_GET['remote_sub_service_id']) || isset($_GET['price_range']) || isset($_GET['search'])): ?>
                    <h3>Active Filters:</h3>
                    <div class="filter-tags">
                        <?php if (isset($_GET['domestic_sub_service_id'])): 
                            $sub_service_id = (int)$_GET['domestic_sub_service_id'];
                            $sub_service_query = "SELECT ss.name, s.name as service_name FROM sub_services ss 
                                                JOIN services s ON ss.service_id = s.id 
                                                WHERE ss.id = ?";
                            $stmt = $conn->prepare($sub_service_query);
                            $stmt->bind_param("i", $sub_service_id);
                            $stmt->execute();
                            $sub_service_result = $stmt->get_result();
                            $sub_service_data = $sub_service_result->fetch_assoc();
                        ?>
                            <span class="filter-tag">
                                Domestic Service: <?php echo htmlspecialchars($sub_service_data['name']); ?>
                                <a href="?<?php echo http_build_query(array_diff_key($_GET, ['domestic_sub_service_id' => ''])); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['remote_sub_service_id'])): 
                            $sub_service_id = (int)$_GET['remote_sub_service_id'];
                            $sub_service_query = "SELECT ss.name, s.name as service_name FROM sub_services ss 
                                                JOIN services s ON ss.service_id = s.id 
                                                WHERE ss.id = ?";
                            $stmt = $conn->prepare($sub_service_query);
                            $stmt->bind_param("i", $sub_service_id);
                            $stmt->execute();
                            $sub_service_result = $stmt->get_result();
                            $sub_service_data = $sub_service_result->fetch_assoc();
                        ?>
                            <span class="filter-tag">
                                Remote Service: <?php echo htmlspecialchars($sub_service_data['name']); ?>
                                <a href="?<?php echo http_build_query(array_diff_key($_GET, ['remote_sub_service_id' => ''])); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>

                        <?php if (isset($_GET['price_range'])): ?>
                            <span class="filter-tag">
                                Price: <?php 
                                    $range = $_GET['price_range'];
                                    if (strpos($range, '+') !== false) {
                                        echo 'PKR ' . number_format((int)str_replace('+', '', $range)) . '+';
                                    } else {
                                        $prices = explode('-', $range);
                                        echo 'PKR ' . number_format((int)$prices[0]) . ' - ' . number_format((int)$prices[1]);
                                    }
                                ?>
                                <a href="?<?php echo http_build_query(array_diff_key($_GET, ['price_range' => ''])); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>

                        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                            <span class="filter-tag">
                                Search: "<?php echo htmlspecialchars($_GET['search']); ?>"
                                <a href="?<?php echo http_build_query(array_diff_key($_GET, ['search' => ''])); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>

                        <?php if (isset($_GET['domestic_sub_service_id']) || isset($_GET['remote_sub_service_id']) || isset($_GET['price_range']) || isset($_GET['search'])): ?>
                            <a href="hire.php" class="clear-all"><i class="fa fa-times-circle"></i> Clear All Filters</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Gigs Section -->
        <section class="gigs-section">
            <div class="section-header" style="text-align: left;">
                <h3 class="gigs-title">Gigs you may like</h3>
            </div>
            <div class="gigs-grid">
                <?php foreach ($gigs as $gig): ?>
                <a href="gig-detail.php?id=<?php echo $gig['id']; ?>" class="gig-link" style="text-decoration:none; color:inherit; display: block; transition: var(--transition-normal);">
                    <div class="gig-card">
                        <div class="gig-image">
                            <img src="<?php echo $gig['image']; ?>" alt="Gig Image">
                            <?php if (!empty($gig['badge'])): ?>
                            <span class="gig-badge"><?php echo $gig['badge']; ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="gig-info">
                            <div class="gig-seller">
                                <i class="fa fa-user-circle"></i> <?php echo $gig['seller']; ?>
                            </div>
                            <div class="gig-title">
                                <?php echo $gig['title']; ?>
                            </div>
                            <div class="gig-rating">
                                <span class="gig-stars">
                                    <?php for ($i = 0; $i < floor($gig['rating']); $i++) echo '<i class="fa fa-star"></i>'; ?>
                                    <?php if ($gig['rating'] - floor($gig['rating']) >= 0.5) echo '<i class="fa fa-star-half-o"></i>'; ?>
                                </span>
                                <span class="gig-rating-value"><?php echo $gig['rating']; ?></span>
                                <span class="gig-reviews">(<?php echo $gig['reviews']; ?>)</span>
                            </div>
                            <div class="gig-price">From PKR <?php echo number_format($gig['price']); ?></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
    </div>


    <?php include '../footer/footer.php'; ?>
</body>
</html>