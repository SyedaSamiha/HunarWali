<?php 
session_start();
include '../head.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - HunarWali</title>
    <link rel="stylesheet" href="how-it-works.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <?php include '../navbar/navbar.php'; ?>
        
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <div class="icon-container">
                    <i class="fas fa-cogs"></i>
                </div>
                <h1>How It Works</h1>
                <p>Discover how HunarWali connects talented women with amazing opportunities. Our platform makes it simple for both freelancers and clients to succeed.</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">1000+</span>
                        <span class="stat-label">Happy Freelancers</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">500+</span>
                        <span class="stat-label">Completed Projects</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">98%</span>
                        <span class="stat-label">Satisfaction Rate</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Process Overview -->
        <section class="process-overview">
            <div class="container">
                <h2>Simple 3-Step Process</h2>
                <div class="process-grid">
                    <div class="process-step">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3>Join & Create Profile</h3>
                        <p>Sign up and build your professional profile showcasing your skills and experience.</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Connect & Collaborate</h3>
                        <p>Browse services, connect with clients or freelancers, and start your journey.</p>
                    </div>
                    <div class="process-step">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Earn & Grow</h3>
                        <p>Complete projects, build your reputation, and achieve financial independence.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Freelancers Section -->
        <section class="for-freelancers">
            <div class="container">
                <div class="section-header">
                    <div class="icon-container">
                        <i class="fas fa-female"></i>
                    </div>
                    <h2>For Freelancers</h2>
                    <p>Turn your skills into income with our supportive platform</p>
                </div>
                
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">01</div>
                        <div class="step-content">
                            <h3>Create Your Profile</h3>
                            <p>Sign up and create a compelling profile that showcases your skills, experience, and portfolio. Add your best work samples and certifications.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Professional profile setup</li>
                                <li><i class="fas fa-check"></i> Portfolio upload</li>
                                <li><i class="fas fa-check"></i> Skill verification</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">02</div>
                        <div class="step-content">
                            <h3>List Your Services</h3>
                            <p>Create detailed service listings with pricing, delivery time, and what's included. Choose from domestic or remote services.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Service categorization</li>
                                <li><i class="fas fa-check"></i> Flexible pricing</li>
                                <li><i class="fas fa-check"></i> Custom packages</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">03</div>
                        <div class="step-content">
                            <h3>Receive Orders</h3>
                            <p>Clients will find your services and place orders. You'll receive notifications and can start working on projects immediately.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Instant notifications</li>
                                <li><i class="fas fa-check"></i> Order management</li>
                                <li><i class="fas fa-check"></i> Communication tools</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">04</div>
                        <div class="step-content">
                            <h3>Deliver & Earn</h3>
                            <p>Complete your work, deliver quality results, and receive payments. Build your reputation through positive reviews.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Secure payments</li>
                                <li><i class="fas fa-check"></i> Review system</li>
                                <li><i class="fas fa-check"></i> Repeat clients</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- For Clients Section -->
        <section class="for-clients">
            <div class="container">
                <div class="section-header">
                    <div class="icon-container">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2>For Clients</h2>
                    <p>Find talented women for your projects and support female empowerment</p>
                </div>
                
                <div class="steps-container">
                    <div class="step-card">
                        <div class="step-number">01</div>
                        <div class="step-content">
                            <h3>Browse Services</h3>
                            <p>Explore our wide range of services from domestic help to professional digital services. Filter by category, location, and budget.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Service categories</li>
                                <li><i class="fas fa-check"></i> Advanced filters</li>
                                <li><i class="fas fa-check"></i> Price comparison</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">02</div>
                        <div class="step-content">
                            <h3>Choose Your Freelancer</h3>
                            <p>Review profiles, portfolios, and ratings to find the perfect match for your project. Read reviews from previous clients.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Profile reviews</li>
                                <li><i class="fas fa-check"></i> Portfolio assessment</li>
                                <li><i class="fas fa-check"></i> Client testimonials</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">03</div>
                        <div class="step-content">
                            <h3>Place Your Order</h3>
                            <p>Select your preferred service package, provide project details, and place your order securely through our platform.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Secure ordering</li>
                                <li><i class="fas fa-check"></i> Project specifications</li>
                                <li><i class="fas fa-check"></i> Payment protection</li>
                            </ul>
                        </div>
                    </div>

                    <div class="step-card">
                        <div class="step-number">04</div>
                        <div class="step-content">
                            <h3>Receive Quality Work</h3>
                            <p>Stay connected with your freelancer throughout the project. Receive updates and approve the final work before payment.</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Progress tracking</li>
                                <li><i class="fas fa-check"></i> Quality assurance</li>
                                <li><i class="fas fa-check"></i> Satisfaction guarantee</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2>Why Choose HunarWali?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Secure Platform</h3>
                        <p>Your data and payments are protected with industry-standard security measures.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3>Trusted Community</h3>
                        <p>Join a community of verified freelancers and clients with transparent reviews.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Easy Communication</h3>
                        <p>Built-in messaging system for seamless communication between clients and freelancers.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3>Secure Payments</h3>
                        <p>Multiple payment options with escrow protection for your peace of mind.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>24/7 Support</h3>
                        <p>Our support team is always ready to help you with any questions or issues.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3>Growth Opportunities</h3>
                        <p>Build your business, expand your skills, and increase your earning potential.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="testimonials-section">
            <div class="container">
                <div class="section-header">
                    <div class="icon-container">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <h2>What Our Users Say</h2>
                    <p>Hear from the amazing women who have transformed their lives with HunarWali</p>
                </div>
                
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>"HunarWali gave me the opportunity to showcase my cooking skills and earn from home. I've never been happier!"</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>Fatima Khan</h4>
                                <span>Culinary Expert</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>"As a graphic designer, I found amazing clients through this platform. The support team is incredible!"</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>Aisha Ahmed</h4>
                                <span>Graphic Designer</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <i class="fas fa-quote-left quote-icon"></i>
                            <p>"I needed reliable cleaning services and found the perfect match. The quality and professionalism exceeded my expectations."</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>Sarah Johnson</h4>
                                <span>Happy Client</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Ready to Get Started?</h2>
                    <p>Join thousands of women who are already earning and growing with HunarWali</p>
                    <div class="cta-buttons">
                        <a href="../registration/index.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Join as Freelancer
                        </a>
                        <a href="../hire-freelancer/hire.php" class="btn btn-secondary">
                            <i class="fas fa-search"></i> Hire Talent
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <?php include '../footer/footer.php'; ?>
    </header>
    
    <script src="how-it-works.js"></script>
</body>
</html> 