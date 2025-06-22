<?php 
session_start();
require_once '../config/database.php';

// Initialize the base query
$query = "SELECT g.*, u.username AS seller_name
          FROM gigs g
          JOIN users u ON g.user_id = u.id
          WHERE 1=1";

// Handle search filter
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query .= " AND (g.gig_title LIKE '%$search%' OR g.gig_description LIKE '%$search%')";
}

// Handle category filter
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $conn->real_escape_string($_GET['category']);
    $query .= " AND g.category = '$category'";
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
            'price' => $row['gig_pricing'] // Convert USD to PKR
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
    <title>Hire Freelancers - Hunarwali</title>
    <link rel="stylesheet" href="hire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include '../navbar/navbar.php'; ?>
    </header>

    <div class="main-content">
        <section class="filters-section">
            <h2>Find the Perfect Freelancer</h2>
            <form method="GET" action="" class="filters">
                <div class="dropdown">
                    <button type="button" class="dropbtn">Category <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'cleaning'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'cleaning') ? 'class="active"' : ''; ?>><i class="fa fa-broom"></i> Cleaning</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'art'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'art') ? 'class="active"' : ''; ?>><i class="fa fa-paint-brush"></i> Art and Craft</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'fashion'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'fashion') ? 'class="active"' : ''; ?>><i class="fa fa-cut"></i> Fashion and Textile</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'beauty'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'beauty') ? 'class="active"' : ''; ?>><i class="fa fa-spa"></i> Beauty and Wellness</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'culinary'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'culinary') ? 'class="active"' : ''; ?>><i class="fa fa-utensils"></i> Culinary Art</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'health'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'health') ? 'class="active"' : ''; ?>><i class="fa fa-heartbeat"></i> Health and Care</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['category' => 'decorative'])); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === 'decorative') ? 'class="active"' : ''; ?>><i class="fa fa-palette"></i> Decorative Art</a>
                    </div>
                </div>
            
                <div class="dropdown">
                    <button type="button" class="dropbtn">Price Range <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '0-1000'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '0-1000') ? 'class="active"' : ''; ?>>Rs 0 - 1,000</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '1000-5000'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '1000-5000') ? 'class="active"' : ''; ?>>Rs 1,000 - 5,000</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '5000-10000'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '5000-10000') ? 'class="active"' : ''; ?>>Rs 5,000 - 10,000</a>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['price_range' => '10000+'])); ?>" <?php echo (isset($_GET['price_range']) && $_GET['price_range'] === '10000+') ? 'class="active"' : ''; ?>>Rs 10,000+</a>
                    </div>
                </div>

                <div class="search-box">
                    <input type="text" name="search" placeholder="Search gigs..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </div>
            </form>

            <!-- Active Filters Section -->
            <div class="active-filters">
                <?php if (isset($_GET['category']) || isset($_GET['price_range']) || isset($_GET['search'])): ?>
                    <h3>Active Filters:</h3>
                    <div class="filter-tags">
                        <?php if (isset($_GET['category'])): ?>
                            <span class="filter-tag">
                                Category: <?php echo ucfirst(htmlspecialchars($_GET['category'])); ?>
                                <a href="?<?php echo http_build_query(array_diff_key($_GET, ['category' => ''])); ?>" class="remove-filter">&times;</a>
                            </span>
                        <?php endif; ?>

                        <?php if (isset($_GET['price_range'])): ?>
                            <span class="filter-tag">
                                Price: <?php 
                                    $range = $_GET['price_range'];
                                    if (strpos($range, '+') !== false) {
                                        echo 'Rs ' . number_format((int)str_replace('+', '', $range)) . '+';
                                    } else {
                                        $prices = explode('-', $range);
                                        echo 'Rs ' . number_format((int)$prices[0]) . ' - ' . number_format((int)$prices[1]);
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

                        <?php if (isset($_GET['category']) || isset($_GET['price_range']) || isset($_GET['search'])): ?>
                            <a href="hire.php" class="clear-all"><i class="fa fa-times-circle"></i> Clear All Filters</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Gigs Section -->
        <section class="gigs-section">
            <h3 class="gigs-title">Gigs you may like</h3>
            <div class="gigs-grid">
                <?php foreach ($gigs as $gig): ?>
                <a href="gig-detail.php?id=<?php echo $gig['id']; ?>" class="gig-link" style="text-decoration:none;color:inherit;">
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
                            <div class="gig-price">From Rs <?php echo number_format($gig['price']); ?> PKR</div>
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