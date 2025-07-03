<?php session_start();
include 'head.php';
 ?> 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HunarWali - Empowering Women Through Skills</title>
    <link rel="stylesheet" href="index-new.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include 'navbar/navbar.php'; ?>
    </header>
    
    <main>
        <!-- Hero Section -->
            <section class="hero-section">
                <div class="hero-background">
                    <div class="overlay"></div>
                </div>
                <div class="hero-content">
                    <div class="hero-left">
                        <div class="hero-tagline">Empowering Women Through Skills</div>
                        <h1>Unlock Your Potential & <span class="highlight">Earn From Your Skills</span></h1>
                        <p>HunarWali connects talented women with amazing opportunities. Our platform makes it simple for both freelancers and clients to succeed in today's digital economy.</p>
                        
                        <div class="hero-cta">
                            <a href="/registration/index.php" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Get Started
                            </a>
                            <a href="/how-it-works/index.php" class="btn btn-secondary">
                                <i class="fas fa-play-circle"></i> How It Works
                            </a>
                        </div>
                        
                        <div class="hero-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>No Membership Fee</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Secure Payments</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>24/7 Support</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="hero-right">
                        <div class="stats-card">
                            <div class="stats-header">
                                <i class="fas fa-chart-line"></i>
                                <h3>Platform Statistics</h3>
                            </div>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                                    <span class="stat-number">1000+</span>
                                    <span class="stat-label">Happy Freelancers</span>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon"><i class="fas fa-tasks"></i></div>
                                    <span class="stat-number">500+</span>
                                    <span class="stat-label">Completed Projects</span>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon"><i class="fas fa-smile"></i></div>
                                    <span class="stat-number">98%</span>
                                    <span class="stat-label">Satisfaction Rate</span>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-icon"><i class="fas fa-globe"></i></div>
                                    <span class="stat-number">20+</span>
                                    <span class="stat-label">Service Categories</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="scroll-indicator">
                    <span>Scroll to explore</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
            </section>

            <!-- Why Choose Our Platform Section -->
            <section class="features-section" id="features">
                <div class="container">
                    <div class="section-header">
                        <div class="icon-container">
                            <i class="fas fa-star"></i>
                        </div>
                        <h2>Why Choose Our Platform?</h2>
                        <p>We connect women with opportunities to showcase their skills locally and globally.
                        Whether you're a freelancer or a skilled artisan, our platform empowers you to build financial independence right from your home.</p>
                    </div>
                    
                    <div class="features-wrapper">
                        <div class="features-grid">
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <h3>Global Reach</h3>
                                <p>Connect with clients from around the world and expand your business beyond local boundaries.</p>
                                <div class="feature-hover">
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <h3>Work from Home</h3>
                                <p>Enjoy the flexibility of working from the comfort of your home while managing your own schedule.</p>
                                <div class="feature-hover">
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-hand-holding-usd"></i>
                                </div>
                                <h3>Financial Freedom</h3>
                                <p>Earn competitive income by monetizing your skills and talents on your own terms.</p>
                                <div class="feature-hover">
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <h3>Secure Platform</h3>
                                <p>Your data and payments are protected with industry-standard security measures.</p>
                                <div class="feature-hover">
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-handshake"></i>
                                </div>
                                <h3>Trusted Community</h3>
                                <p>Join a community of verified freelancers and clients with transparent reviews.</p>
                                <div class="feature-hover">
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                            <div class="feature-card">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3>Growth Opportunities</h3>
                                <p>Build your business, expand your skills, and increase your earning potential.</p>
                                <div class="feature-hover">
                                    <a href="#" class="feature-link">Learn More <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="features-summary">
                            <div class="summary-card">
                                <h3>Join thousands of women already succeeding on our platform</h3>
                                <p>HunarWali provides all the tools and support you need to turn your skills into a thriving business. Our platform is designed specifically for women who want to achieve financial independence while maintaining work-life balance.</p>
                                <div class="summary-stats">
                                    <div class="summary-stat">
                                        <span class="stat-number">94%</span>
                                        <span class="stat-label">User Satisfaction</span>
                                    </div>
                                    <div class="summary-stat">
                                        <span class="stat-number">30+</span>
                                        <span class="stat-label">Service Categories</span>
                                    </div>
                                </div>
                                <a href="/registration/index.php" class="btn btn-primary">Get Started Today</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- What We Provide Section -->
            <section class="services-section" id="services">
                <div class="container">
                    <div class="section-header">
                        <div class="icon-container">
                            <i class="fas fa-gift"></i>
                        </div>
                        <h2>What We Provide</h2>
                        <p>At Hunarwali, we provide a wide array of services to empower women, including Domestic Services like Cleaning, Beauty and Wellness, and Culinary Arts. We also offer Remote Services such as Content Creation, Graphic Design, and Digital Marketing, all designed to help you showcase your skills and thrive in both local and online workspaces.</p>
                    </div>
                    
                    <div class="services-container">
                        <div class="services-content">
                            <div class="services-tabs">
                                <button class="tab-btn active" data-tab="domestic">Domestic Services</button>
                                <button class="tab-btn" data-tab="remote">Remote Services</button>
                                <button class="tab-btn" data-tab="support">Support & Growth</button>
                            </div>
                            
                            <div class="tab-content active" id="domestic">
                                <h3>Domestic Services</h3>
                                <p>Our domestic services connect skilled women with local clients who need assistance with day-to-day tasks and specialized services.</p>
                                <ul class="service-list">
                                    <li><i class="fas fa-check-circle"></i> Professional Cleaning</li>
                                    <li><i class="fas fa-check-circle"></i> Beauty & Wellness</li>
                                    <li><i class="fas fa-check-circle"></i> Culinary Arts</li>
                                    <li><i class="fas fa-check-circle"></i> Childcare Services</li>
                                    <li><i class="fas fa-check-circle"></i> Home Organization</li>
                                    <li><i class="fas fa-check-circle"></i> Event Planning</li>
                                </ul>
                                <a href="/domestic-services/domestic.php" class="btn btn-primary">Explore Domestic Services</a>
                            </div>
                            
                            <div class="tab-content" id="remote">
                                <h3>Remote Services</h3>
                                <p>Our remote services allow women to work from anywhere, connecting with clients globally through digital platforms.</p>
                                <ul class="service-list">
                                    <li><i class="fas fa-check-circle"></i> Digital Marketing</li>
                                    <li><i class="fas fa-check-circle"></i> Content Creation</li>
                                    <li><i class="fas fa-check-circle"></i> Graphic Design</li>
                                    <li><i class="fas fa-check-circle"></i> Virtual Assistance</li>
                                    <li><i class="fas fa-check-circle"></i> Web Development</li>
                                    <li><i class="fas fa-check-circle"></i> Online Tutoring</li>
                                </ul>
                                <a href="/remote-services/remote.php" class="btn btn-primary">Explore Remote Services</a>
                            </div>
                            
                            <div class="tab-content" id="support">
                                <h3>Support & Growth</h3>
                                <p>We provide comprehensive resources to help women develop their skills and grow their businesses on our platform.</p>
                                <ul class="service-list">
                                    <li><i class="fas fa-check-circle"></i> Skills Training</li>
                                    <li><i class="fas fa-check-circle"></i> Business Mentorship</li>
                                    <li><i class="fas fa-check-circle"></i> Community Forums</li>
                                    <li><i class="fas fa-check-circle"></i> Marketing Support</li>
                                    <li><i class="fas fa-check-circle"></i> Financial Guidance</li>
                                    <li><i class="fas fa-check-circle"></i> Networking Events</li>
                                </ul>
                                <a href="/help-center/index.php" class="btn btn-primary">Access Resources</a>
                            </div>
                        </div>
                        
                        <div class="services-image">
                            <img src="assets/quality-guaranteed.png" alt="Services Showcase" class="right-image">
                            <div class="image-overlay">
                                <div class="service-highlight">
                                    <div class="highlight-icon">
                                        <i class="fas fa-award"></i>
                                    </div>
                                    <div class="highlight-content">
                                        <h4>Quality Guaranteed</h4>
                                        <p>All services are verified and quality-checked</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <!-- About Us Section -->
            <section class="about-section" id="about">
                <div class="container">
                    <div class="section-header">
                        <div class="icon-container">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2>About Us</h2>
                        <p>Welcome to HunarWali, a dedicated freelance platform designed exclusively for empowering women</p>
                    </div>
                    
                    <div class="about-wrapper">
                        <div class="about-content">
                            <div class="about-story">
                                <div class="story-header">
                                    <div class="story-icon">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <h3>Our Story</h3>
                                </div>
                                <p>HunarWali was founded with a simple yet powerful vision: to create a platform where women can transform their skills into sustainable livelihoods. We recognized that many talented women face barriers to traditional employment, yet possess incredible skills that could thrive in the freelance marketplace.</p>
                                <p>Since our launch, we've helped thousands of women across the country build successful freelance careers, connecting them with clients who value their expertise and professionalism.</p>
                            </div>
                            
                            <div class="about-values">
                                <div class="values-grid">
                                    <div class="value-card">
                                        <div class="value-icon">
                                            <i class="fas fa-hand-holding-heart"></i>
                                        </div>
                                        <h4>Empowerment</h4>
                                        <p>We believe in the power of economic independence to transform lives.</p>
                                    </div>
                                    
                                    <div class="value-card">
                                        <div class="value-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h4>Community</h4>
                                        <p>We foster a supportive network where women can learn and grow together.</p>
                                    </div>
                                    
                                    <div class="value-card">
                                        <div class="value-icon">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <h4>Trust</h4>
                                        <p>We create a safe, transparent environment for both freelancers and clients.</p>
                                    </div>
                                    
                                    <div class="value-card">
                                        <div class="value-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <h4>Growth</h4>
                                        <p>We're committed to helping women continuously develop their skills and businesses.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="about-sidebar">
                            <div class="mission-card">
                                <h3>Our Mission</h3>
                                <p>Our mission is to create opportunities for talented women to showcase their skills, connect with clients, and achieve financial independence—whether they're working from the comfort of their homes or offering on-site services.</p>
                                <ul class="mission-list">
                                    <li><i class="fas fa-check-circle"></i> Empower women through skills</li>
                                    <li><i class="fas fa-check-circle"></i> Create economic opportunities</li>
                                    <li><i class="fas fa-check-circle"></i> Build supportive communities</li>
                                </ul>
                            </div>
                            
                            <div class="newsletter-card">
                                <div class="newsletter-header">
                                    <div class="newsletter-icon">
                                        <i class="fas fa-envelope-open-text"></i>
                                    </div>
                                    <h3>Stay Connected</h3>
                                </div>
                                <p>Subscribe to our newsletter for the latest opportunities, success stories, and platform updates.</p>
                                <div class="form-group">
                                    <button type="button" class="btn btn-primary" id="openSubscribeModal">
                                        <i class="fas fa-paper-plane"></i> Subscribe
                                    </button>
                                </div>
                                
                                <!-- Newsletter Modal -->
                                <div class="modal" id="subscribeModal">
                                    <div class="modal-content">
                                        <span class="close-modal">&times;</span>
                                        <h3>Subscribe to Our Newsletter</h3>
                                        <p>Stay updated with our latest opportunities and success stories.</p>
                                        <form id="newsletterModalForm">
                                            <div class="input-container modal-input">
                                                <i class="fas fa-envelope"></i>
                                                <input type="email" id="modalEmail" placeholder="Enter your email" required>
                                            </div>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-paper-plane"></i> Subscribe
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Toast Notification -->
                                <div id="toast" class="toast-notification">Subscribed to newsletter successfully!</div>
                             </div>
                         </div>
                     </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- About image section removed as requested -->
                </div>
            </section>
            
            <!-- CTA Section -->
            <section class="cta-section" id="join-us">
                <div class="container">
                    <div class="cta-wrapper">
                        <div class="cta-content">
                            <h2>Ready to Transform Your Skills into Success?</h2>
                            <p>Join our thriving community of talented women who are turning their passion into profit and building successful careers on their own terms.</p>
                            <div class="cta-features">
                                <div class="cta-feature">
                                    <div class="feature-icon">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Launch Your Career</h4>
                                        <p>Start earning within days, not months</p>
                                    </div>
                                </div>
                                <div class="cta-feature">
                                    <div class="feature-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Secure Payments</h4>
                                        <p>Get paid on time, every time</p>
                                    </div>
                                </div>
                                <div class="cta-feature">
                                    <div class="feature-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Grow Your Business</h4>
                                        <p>Access tools to scale your services</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cta-buttons">
                                <a href="/registration/index.php" class="btn btn-primary">
                                    <i class="fas fa-user-plus"></i> Join as Freelancer
                                </a>
                                <a href="/hire-freelancer/hire.php" class="btn btn-secondary">
                                    <i class="fas fa-search"></i> Hire Talent
                                </a>
                            </div>
                        </div>
                        <div class="cta-badge-container">
                            <div class="cta-badge">
                                <div class="badge-content">
                                    <span class="badge-number">5000+</span>
                                    <span class="badge-text">Successful Projects</span>
                                </div>
                            </div>
                        </div>
                        <!-- CTA image with palm trees removed as requested -->
                    </div>
                </div>
            </section>

    </main>

    <footer>
        <?php include 'footer/footer.php'; ?>
    </footer>
    
    <script src="script.js"></script>
    <script>
        // Modal functionality
        const modal = document.getElementById('subscribeModal');
        const openModalBtn = document.getElementById('openSubscribeModal');
        const closeModalBtn = document.querySelector('.close-modal');
        const toast = document.getElementById('toast');
        
        // Open modal
        openModalBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
        });
        
        // Close modal when clicking X
        closeModalBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Form submission
        document.getElementById('newsletterModalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('modalEmail').value;
            
            // AJAX request to subscribe.php
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'newsletter/subscribe.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Close modal
                    modal.style.display = 'none';
                    
                    // Show toast notification
                    toast.className = 'toast-notification show';
                    
                    // Hide toast after 3 seconds
                    setTimeout(function() {
                        toast.className = toast.className.replace('show', '');
                    }, 3000);
                    
                    // Reset form
                    document.getElementById('newsletterModalForm').reset();
                }
            };
            xhr.send('email=' + encodeURIComponent(email));
        });
    </script>
</body>
</html>