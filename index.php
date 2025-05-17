<?php session_start();
include 'head.php';
 ?> 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final year project</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
        <header>
        <?php include 'navbar/navbar.php'; ?>

            <section class="hero-page1">
                <div class="hero-content">
                    <h1 id="heropagetext1">Passion is energy. Feel the <br> power that comes from focusing <br>on what excites you!</h1>
                    <div class="button-container">
                        <a href="/registration/index.php">
                            <button class="join-btn">
                                <i class="fas fa-user-plus"></i> JOIN US
                            </button>
                        </a>
                    </div>
                </div>
                <div class="scroll-indicator">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </section>

            <section class="hero-page2">
                <div class="hero-content">
                    <div class="icon-container">
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="hero-heading2"><strong>WHY CHOOSE<br>OUR PLATFORM?</strong></p>
                    <p class="hero-text2">
                        We connect women with opportunities to showcase their skills locally and globally.
                        Whether you're a freelancer, a skilled artisan, our platform empowers you to build financial independence right from your home.
                    </p>
                    <div class="features-grid">
                        <div class="feature-item">
                            <i class="fas fa-globe"></i>
                            <h3>Global Reach</h3>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-home"></i>
                            <h3>Work from Home</h3>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-hand-holding-usd"></i>
                            <h3>Financial Freedom</h3>
                        </div>
                    </div>
                </div>
            </section>

            <section class="hero-page3">
                <div class="container3">
                    <div class="left-side3">
                        
                        <h1>WHAT WE PROVIDE</h1>
                        <p>At Hunarwali, we provide a wide array of services to empower women, including Domestic Services like Cleaning, Beauty and Wellness, and Culinary Arts. We also offer Remote Services such as Content Creation, Graphic Design, and Digital Marketing, all designed to help you showcase your skills and thrive in both local and online workspaces.</p>
                        <div class="services-grid">
                            <div class="service-item">
                                <i class="fas fa-broom"></i>
                                <span>Cleaning</span>
                            </div>
                            <div class="service-item">
                                <i class="fas fa-spa"></i>
                                <span>Beauty</span>
                            </div>
                            <div class="service-item">
                                <i class="fas fa-utensils"></i>
                                <span>Culinary</span>
                            </div>
                        </div>
                    </div>
                    <div class="right-side3">
                        <img src="assets/shop.png" class="right-image">
                    </div>
                </div>  
            </section>

            <section class="hero-page4">
                <div class="hero-content">
                    <div class="animated-text">
                        <i class="fas fa-fire"></i> TRENDING SERVICES <span></span>
                    </div>
                    <div class="trending-grid">
                        <div class="trending-item">
                            <i class="fas fa-paint-brush"></i>
                            <h3>Art & Design</h3>
                        </div>
                        <div class="trending-item">
                            <i class="fas fa-laptop-code"></i>
                            <h3>Digital Services</h3>
                        </div>
                        <div class="trending-item">
                            <i class="fas fa-camera"></i>
                            <h3>Photography</h3>
                        </div>
                    </div>
                </div>
            </section>

            <section class="hero-page5">
                <div class="container5">
                    <div class="left-side5">
                        <div class="icon-container">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h1>ABOUT US</h1>
                        <p>Welcome to HunarWali, a dedicated freelance platform designed exclusively for empowering women. Our mission is to create opportunities for talented women to showcase their skills, connect with clients, and achieve financial independence-whether they're working from the comfort of their homes or offering on-site services.</p>
                        <div class="newsletter-section">
                            <h3>Stay Updated!</h3>
                            <p>Subscribe to our newsletter for the latest opportunities and updates.</p>
                            <form class="newsletter-form" action="subscribe.php" method="POST">
                                <div class="input-container">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" name="email" placeholder="Enter your email" required>
                                </div>
                                <button type="submit" class="subscribe-btn">
                                    <i class="fas fa-paper-plane"></i> Subscribe
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="right-side5">
                        <img src="assets/palmtrees.png" alt="Flower Shop" class="right-image">
                    </div>
                </div>
            </section>
            
            <div>
                
            </div>


            <?php include 'footer/footer.php'; ?>
            <script src="script.js"></script>
        </header>
    </body>
    </html>