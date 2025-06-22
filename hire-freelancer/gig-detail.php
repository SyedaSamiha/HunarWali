<?php
session_start();
require_once '../config/database.php';

// Function to initiate chat with seller
function initiateChat($conn, $gig_id, $buyer_id, $seller_id) {
    // Check if chat already exists
    $check_query = "SELECT id FROM chats WHERE gig_id = ? AND buyer_id = ? AND seller_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param('iii', $gig_id, $buyer_id, $seller_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Chat exists, return the chat ID
        $chat = $result->fetch_assoc();
        return $chat['id'];
    } else {
        // Create new chat
        $create_query = "INSERT INTO chats (gig_id, buyer_id, seller_id, created_at) VALUES (?, ?, ?, NOW())";
        $create_stmt = $conn->prepare($create_query);
        $create_stmt->bind_param('iii', $gig_id, $buyer_id, $seller_id);
        
        if ($create_stmt->execute()) {
            $chat_id = $conn->insert_id;
            
            // Add initial message
            $message = "Hey, I saw your Gig Listed, Let's discuss further";
            $message_query = "INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())";
            $message_stmt = $conn->prepare($message_query);
            $message_stmt->bind_param('iis', $chat_id, $buyer_id, $message);
            $message_stmt->execute();
            
            return $chat_id;
        }
    }
    return false;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle chat initiation
if (isset($_POST['start_chat']) && isset($_SESSION['user_id'])) {
    $chat_id = initiateChat($conn, $id, $_SESSION['user_id'], $gig['user_id']);
    if ($chat_id) {
        // Add initial message
        $message = "Hey, I'm interested in your service";
        $message_query = "INSERT INTO messages (chat_id, sender_id, message, created_at) VALUES (?, ?, ?, NOW())";
        $message_stmt = $conn->prepare($message_query);
        $message_stmt->bind_param('iis', $chat_id, $_SESSION['user_id'], $message);
        $message_stmt->execute();
    }
    // Redirect to chat screen
    header("Location: /login/dashboard.php?page=messages");
    exit;
}

// Check if the ID is in the session's gig_ids
if (!isset($_SESSION['gig_ids']) || !in_array($id, $_SESSION['gig_ids'])) {
    echo '<h2>Gig not found.</h2>';
    exit;
}

// Fetch gig by id
$query = "SELECT g.*, u.username AS seller_name FROM gigs g JOIN users u ON g.user_id = u.id WHERE g.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$gig = $result->fetch_assoc();

if (!$gig) {
    echo '<h2>Gig not found.</h2>';
    exit;
}

// Store gig details in session
$_SESSION['gig_details'] = [
    'gig_id' => $gig['id'],
    'gig_title' => $gig['gig_title'],
    'gig_pricing' => $gig['gig_pricing'],
    'seller_id' => $gig['user_id'],
    'seller_name' => $gig['seller_name'],
    'gig_images' => $gig['gig_images'],
    'gig_description' => $gig['gig_description'],
    'tags' => $gig['tags'],
    'created_at' => $gig['created_at']
];

// Convert price to PKR
$price_pkr = $gig['gig_pricing'];

// Handle gig image - use actual image if exists, otherwise use default
$image = '';
if (!empty($gig['gig_images']) && file_exists('../' . $gig['gig_images'])) {
    $image = '../' . $gig['gig_images'];
} else {
    $image = 'https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg';
}

// After fetching the gig details, add this code to fetch reviews
$reviewsQuery = "
    SELECT r.*, u.username 
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN orders o ON r.order_id = o.id
    WHERE o.gig_id = ? AND r.user_id = o.buyer_id
    ORDER BY r.created_at DESC
";
$reviewsStmt = $conn->prepare($reviewsQuery);
$reviewsStmt->bind_param('i', $id);
$reviewsStmt->execute();
$reviews = $reviewsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate average rating
$avgRating = 0;
$totalReviews = count($reviews);
if ($totalReviews > 0) {
    $ratingSum = array_sum(array_column($reviews, 'rating'));
    $avgRating = round($ratingSum / $totalReviews, 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gig['gig_title']); ?> - Gig Details</title>
    <link rel="stylesheet" href="hire.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            color: #1f2937;
        }
        .main-content {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .breadcrumb {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .breadcrumb a {
            color: #8a3342;
            text-decoration: none;
            transition: color 0.2s;
        }
        .breadcrumb a:hover {
            color: #6b2834;
        }
        .breadcrumb span::before {
            content: "›";
            margin: 0 0.5rem;
            color: #9ca3af;
        }
        .gig-detail-flex {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            align-items: start;
        }
        .gig-left {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .gig-image {
            height: 450px;
            position: relative;
            background: #f3f4f6;
            overflow: hidden;
        }
        .gig-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(100%);
        }
        .gig-image img.active {
            opacity: 1;
            transform: translateX(0);
        }
        .carousel-controls {
            position: absolute;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }
        .carousel-btn {
            background: rgba(255, 255, 255, 0.9);
            color: #8a3342;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .carousel-btn:hover {
            background: white;
            transform: scale(1.05);
        }
        .carousel-dots {
            position: absolute;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.75rem;
            z-index: 2;
        }
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s;
        }
        .dot.active {
            background: white;
            transform: scale(1.2);
        }
        .gig-content {
            padding: 2rem;
        }
        .gig-title {
            color: #1f2937;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
        .seller-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem;
            background: #f9fafb;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        .seller-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: #8a3342;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .seller-details {
            flex: 1;
        }
        .seller-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        .seller-rating {
            color: #fbbf24;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .seller-rating span {
            color: #6b7280;
            font-size: 0.95rem;
        }
        .gig-description {
            color: #4b5563;
            line-height: 1.8;
            font-size: 1.1rem;
            margin: 2rem 0;
            white-space: pre-line;
        }
        .gig-tags {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin: 2rem 0;
        }
        .tag {
            background: #f3e8ea;
            padding: 0.6rem 1.2rem;
            border-radius: 24px;
            color: #8a3342;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .tag:hover {
            background: #ecdee0;
            transform: translateY(-1px);
        }
        .reviews-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 3rem;
        }
        .reviews-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .reviews-header h2 {
            font-size: 1.5rem;
            color: #1f2937;
            margin: 0;
        }
        .rating-summary {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .avg-rating {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }
        .rating-stars {
            color: #fbbf24;
            font-size: 1.25rem;
            letter-spacing: 0.1em;
        }
        .review-card {
            padding: 1.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .review-card:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .review-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .reviewer-name {
            font-weight: 600;
            color: #1f2937;
        }
        .review-date {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .review-text {
            color: #4b5563;
            line-height: 1.7;
            font-size: 1.05rem;
            margin: 0;
        }
        .gig-right {
            position: sticky;
            top: 2rem;
        }
        .order-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .order-card h4 {
            color: #1f2937;
            font-size: 1.25rem;
            margin: 0 0 1.5rem 0;
        }
        .gig-price {
            font-size: 2.5rem;
            color: #8a3342;
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
        }
        .gig-price::before {
            content: "Rs";
            font-size: 1.5rem;
            color: #6b7280;
        }
        .order-features {
            margin: 0 0 2rem 0;
            padding: 0;
        }
        .order-features li {
            color: #4b5563;
            margin-bottom: 1rem;
            list-style: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.05rem;
        }
        .order-features li i {
            color: #8a3342;
            font-size: 1.2rem;
        }
        .order-btn {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            background: #8a3342;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .order-btn:hover {
            background: #6b2834;
            transform: translateY(-2px);
        }
        .chat-btn {
            width: 100%;
            padding: 1rem;
            border-radius: 12px;
            background: white;
            color: #8a3342;
            font-size: 1.1rem;
            font-weight: 600;
            border: 2px solid #8a3342;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        .chat-btn:hover {
            background: #f3e8ea;
            transform: translateY(-2px);
        }
        @media (max-width: 1024px) {
            .gig-detail-flex {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .gig-right {
                position: static;
            }
            .main-content {
                padding: 0 1rem;
            }
        }
        @media (max-width: 640px) {
            .gig-image {
                height: 300px;
            }
            .gig-content {
                padding: 1.5rem;
            }
            .gig-title {
                font-size: 1.75rem;
            }
            .seller-info {
                padding: 1rem;
            }
            .reviews-section {
                padding: 1.5rem;
            }
            .order-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include '../navbar/navbar.php'; ?>

    <div class="main-content">
        <div class="breadcrumb">
            <a href="/index.php">Home</a> > <a href="/hire-freelancer/">Hire Freelancer</a> > <span><?php echo htmlspecialchars($gig['gig_title']); ?></span>
        </div>
        <div class="gig-detail-flex">
            <!-- Left Column -->
            <div class="gig-left">
                <div class="gig-image">
                    <img src="<?php echo $image; ?>" alt="Gig Image" class="active">
                    <img src="<?php echo $image; ?>" alt="Gig Image">
                    <img src="<?php echo $image; ?>" alt="Gig Image">
                    <div class="carousel-controls">
                        <button class="carousel-btn prev-btn"><i class="fas fa-chevron-left"></i></button>
                        <button class="carousel-btn next-btn"><i class="fas fa-chevron-right"></i></button>
                    </div>
                    <div class="carousel-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
                <div class="gig-content">
                    <div class="gig-title"><?php echo htmlspecialchars($gig['gig_title']); ?></div>
                    <div class="seller-info">
                        <div class="seller-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="seller-details">
                            <div class="seller-name"><?php echo htmlspecialchars($gig['seller_name']); ?></div>
                            <div class="seller-rating">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                                <span>4.9 (201 reviews)</span>
                            </div>
                        </div>
                    </div>
                    <div class="gig-description">
                        <?php echo nl2br(htmlspecialchars($gig['gig_description'])); ?>
                    </div>
                    <div class="gig-tags">
                        <?php
                        $tags = explode(',', $gig['tags']);
                        foreach ($tags as $tag) {
                            echo '<span class="tag">' . htmlspecialchars(trim($tag)) . '</span>';
                        }
                        ?>
                    </div>
                </div>
                <div class="reviews-section">
                    <div class="reviews-header">
                        <h2>Reviews</h2>
                        <div class="rating-summary">
                            <span class="avg-rating"><?php echo $avgRating; ?></span>
                            <div class="rating-stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= floor($avgRating)) {
                                        echo '★';
                                    } elseif ($i - $avgRating < 1 && $i - $avgRating > 0) {
                                        echo '★';
                                    } else {
                                        echo '☆';
                                    }
                                }
                                ?>
                            </div>
                            <span>(<?php echo $totalReviews; ?> reviews)</span>
                        </div>
                    </div>

                    <?php if ($totalReviews > 0): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-meta">
                                    <span class="reviewer-name"><?php echo htmlspecialchars($review['username']); ?></span>
                                    <div class="rating-stars">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            echo $i <= $review['rating'] ? '★' : '☆';
                                        }
                                        ?>
                                    </div>
                                    <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                </div>
                                <p class="review-text"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No reviews yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Right Column -->
            <div class="gig-right">
                <div class="order-card">
                    <h4>Order this service</h4>
                    <div class="gig-price">Rs <?php echo number_format($price_pkr); ?> PKR</div>
                    <ul class="order-features">
                        <li><i class="fas fa-check-circle" style="color:#8a3342;"></i> Fast delivery</li>
                        <li><i class="fas fa-check-circle" style="color:#8a3342;"></i> Quality work</li>
                        <li><i class="fas fa-check-circle" style="color:#8a3342;"></i> Secure payment</li>
                    </ul>
                    <a href="/payment/index.php" class="order-btn" style="text-decoration: none; display: block; text-align: center;"><i class="fas fa-shopping-cart"></i> Continue</a>
                    <form method="POST" action="/login/dashboard.php?page=messages" style="margin-top: 0.5rem;">
                        <input type="hidden" name="start_chat" value="1">
                        <input type="hidden" name="seller_id" value="<?php echo $gig['user_id']; ?>">
                        <button type="submit" class="chat-btn"><i class="fas fa-comments"></i> Chat with Seller</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include '../footer/footer.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.gig-image img');
            const dots = document.querySelectorAll('.dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            let currentIndex = 0;
            let isAnimating = false;

            function showImage(index, direction = 'next') {
                if (isAnimating) return;
                isAnimating = true;

                const currentImage = images[currentIndex];
                const nextImage = images[index];

                // Remove all classes first
                images.forEach(img => {
                    img.classList.remove('active', 'prev');
                });
                dots.forEach(dot => dot.classList.remove('active'));

                // Set up the animation
                if (direction === 'next') {
                    currentImage.classList.add('prev');
                    nextImage.style.transform = 'translateX(100%)';
                    nextImage.style.opacity = '0';
                    
                    // Force reflow
                    nextImage.offsetHeight;
                    
                    nextImage.style.transform = 'translateX(0)';
                    nextImage.style.opacity = '1';
                } else {
                    currentImage.style.transform = 'translateX(100%)';
                    nextImage.style.transform = 'translateX(-100%)';
                    nextImage.style.opacity = '0';
                    
                    // Force reflow
                    nextImage.offsetHeight;
                    
                    nextImage.style.transform = 'translateX(0)';
                    nextImage.style.opacity = '1';
                }

                nextImage.classList.add('active');
                dots[index].classList.add('active');
                currentIndex = index;

                // Reset animation flag after transition
                setTimeout(() => {
                    isAnimating = false;
                }, 500);
            }

            function nextImage() {
                const nextIndex = (currentIndex + 1) % images.length;
                showImage(nextIndex, 'next');
            }

            function prevImage() {
                const prevIndex = (currentIndex - 1 + images.length) % images.length;
                showImage(prevIndex, 'prev');
            }

            // Event listeners
            nextBtn.addEventListener('click', nextImage);
            prevBtn.addEventListener('click', prevImage);

            // Add click events to dots
            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    if (index === currentIndex) return;
                    const direction = index > currentIndex ? 'next' : 'prev';
                    showImage(index, direction);
                });
            });

            // Auto-advance slides every 5 seconds
            let autoSlideInterval = setInterval(nextImage, 5000);

            // Pause auto-slide on hover
            const carouselContainer = document.querySelector('.gig-image');
            carouselContainer.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
            });

            carouselContainer.addEventListener('mouseleave', () => {
                autoSlideInterval = setInterval(nextImage, 5000);
            });
        });
    </script>
</body>
</html>