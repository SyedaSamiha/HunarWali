<?php 
session_start();
require_once '../config/database.php';

// Fetch gigs from database
$query = "SELECT g.*, u.username AS seller_name
          FROM gigs g
          JOIN users u ON g.user_id = u.id
          ORDER BY g.created_at DESC";
$result = $conn->query($query);

$gigs = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $gigs[] = [
            'id' => $row['id'],
            'image' => $row['gig_images'] ? 'https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg' : 'https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg',
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
            <div class="filters">
                <div class="dropdown">
                    <button class="dropbtn">Domestic Services <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="#"><i class="fa fa-broom"></i> Cleaning</a>
                        <a href="#"><i class="fa fa-paint-brush"></i> Art and Craft</a>
                        <a href="#"><i class="fa fa-cut"></i> Fashion and Textile</a>
                        <a href="#"><i class="fa fa-spa"></i> Beauty and Wellness</a>
                        <a href="#"><i class="fa fa-utensils"></i> Culinary Art</a>
                        <a href="#"><i class="fa fa-heartbeat"></i> Health and Care</a>
                        <a href="#"><i class="fa fa-palette"></i> Decorative Art</a>
                    </div>
                </div>
            
                <div class="dropdown">
                    <button class="dropbtn">Remote Services <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="#"><i class="fa fa-video"></i> Video and Animation</a>
                        <a href="#"><i class="fa fa-pencil"></i> Graphic Designer</a>
                        <a href="#"><i class="fa fa-bullhorn"></i> Digital Marketing</a>
                        <a href="#"><i class="fa fa-pencil-square-o"></i> Writing And Content Creation</a>
                        <a href="#"><i class="fa fa-graduation-cap"></i> Online Education</a>
                        <a href="#"><i class="fa fa-code"></i> Web Development</a>
                        <a href="#"><i class="fa fa-mobile"></i> App Development</a>
                    </div>
                </div>
            
                <div class="dropdown">
                    <button class="dropbtn">Seller Level <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="#"><i class="fa fa-star"></i> Top Rated Seller</a>
                        <a href="#"><i class="fa fa-star-half-o"></i> Level 1 Seller</a>
                        <a href="#"><i class="fa fa-star-o"></i> New Seller</a>
                    </div>
                </div>
            
                <div class="dropdown">
                    <button class="dropbtn">Budget Range <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="#"><i class="fa fa-dollar"></i> $10 - $50</a>
                        <a href="#"><i class="fa fa-dollar"></i> $50 - $200</a>
                        <a href="#"><i class="fa fa-dollar"></i> $200+</a>
                    </div>
                </div>
            
                <div class="dropdown">
                    <button class="dropbtn">Delivery Time <i class="fa fa-chevron-down"></i></button>
                    <div class="dropdown-content">
                        <a href="#"><i class="fa fa-clock-o"></i> 24 Hours</a>
                        <a href="#"><i class="fa fa-calendar"></i> 3 Days</a>
                        <a href="#"><i class="fa fa-calendar-check-o"></i> 7 Days</a>
                    </div>
                </div>
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