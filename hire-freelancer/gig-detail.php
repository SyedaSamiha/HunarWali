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
$image = $gig['gig_images'] ? 'https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg' : 'https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg';
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .breadcrumb {
            font-size: 0.98rem;
            color: #888;
            margin-bottom: 1.5rem;
        }
        .breadcrumb a {
            color: #8a3342;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .gig-detail-flex {
            display: flex;
            gap: 2.5rem;
            align-items: flex-start;
        }
        .gig-left {
            flex: 2;
            min-width: 0;
        }
        .gig-image {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(44,62,80,0.08);
            overflow: hidden;
            margin-bottom: 1.5rem;
            height: 350px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .gig-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            position: absolute;
            opacity: 0;
            transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
            transform: translateX(100%);
        }
        .gig-image img.active {
            opacity: 1;
            transform: translateX(0);
        }
        .gig-image img.prev {
            transform: translateX(-100%);
            opacity: 0;
        }
        .carousel-controls {
            position: absolute;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 1rem;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }
        .carousel-btn {
            background: rgba(138, 51, 66, 0.8);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
        }
        .carousel-btn:hover {
            background: rgba(138, 51, 66, 1);
        }
        .carousel-dots {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
            z-index: 2;
        }
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: background 0.3s;
        }
        .dot.active {
            background: #8a3342;
        }
        .gig-title {
            color: #8a3342;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .seller-info {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            color: #444;
            margin-bottom: 1.2rem;
        }
        .seller-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.7rem;
            color: #8a3342;
        }
        .seller-details {
            display: flex;
            flex-direction: column;
        }
        .seller-name {
            font-weight: 600;
            color: #8a3342;
        }
        .seller-rating {
            color: #f7b731;
            font-size: 1rem;
        }
        .gig-description {
            color: #444;
            line-height: 1.8;
            font-size: 1.08rem;
            margin: 1.5rem 0;
            background: #fff;
            border-radius: 8px;
            padding: 1.2rem 1.5rem;
            box-shadow: 0 1px 6px rgba(44,62,80,0.06);
        }
        .gig-tags {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 1.2rem;
        }
        .tag {
            background: #f3e6e8;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            color: #8a3342;
            font-size: 0.95rem;
        }
        .reviews-section {
            margin-top: 2.5rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 6px rgba(44,62,80,0.06);
            padding: 1.5rem;
        }
        .reviews-section h3 {
            color: #8a3342;
            margin-bottom: 1rem;
        }
        .gig-right {
            flex: 1;
            min-width: 320px;
        }
        .order-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(44,62,80,0.10);
            padding: 2rem 1.5rem;
            position: sticky;
            top: 2rem;
        }
        .order-card h4 {
            color: #8a3342;
            margin-bottom: 1.2rem;
        }
        .gig-price {
            font-size: 2rem;
            color: #8a3342;
            font-weight: 700;
            margin-bottom: 1.2rem;
        }
        .order-features {
            margin-bottom: 1.5rem;
        }
        .order-features li {
            color: #444;
            margin-bottom: 0.5rem;
            list-style: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .order-btn {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            background: #8a3342;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
            margin-bottom: 0.5rem;
        }
        .order-btn:hover {
            background: #6b2834;
        }
        .chat-btn {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            background: #fff;
            color: #8a3342;
            font-size: 1.1rem;
            font-weight: 600;
            border: 2px solid #8a3342;
            cursor: pointer;
            transition: all 0.2s;
        }
        .chat-btn:hover {
            background: #f3e6e8;
        }
        @media (max-width: 900px) {
            .gig-detail-flex {
                flex-direction: column;
            }
            .gig-right {
                width: 100%;
                min-width: 0;
                margin-top: 2rem;
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
                    <img src="https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg" alt="Gig Image">
                    <img src="https://www.uxdesigninstitute.com/blog/wp-content/uploads/2022/03/103_what_does_a_ui_designer_do_image_blog.jpg" alt="Gig Image">
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
                <div class="gig-title"><?php echo htmlspecialchars($gig['gig_title']); ?></div>
                <div class="seller-info">
                    <div class="seller-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="seller-details">
                        <span class="seller-name"><?php echo htmlspecialchars($gig['seller_name']); ?></span>
                        <span class="seller-rating">
                            <i class="fas fa-star"></i> 4.9 (201 reviews)
                        </span>
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
                <div class="reviews-section">
                    <h3><i class="fas fa-comments"></i> Reviews</h3>
                    <p style="color:#888;">No reviews yet. (This is a placeholder for future reviews.)</p>
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