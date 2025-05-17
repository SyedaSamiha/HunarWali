<?php
session_start();
include '../navbar/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center - Hunarwali</title>
    <link rel="stylesheet" href="help-center.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="help-center-container">
        <div class="hero-section">
            <h1>How can we help you?</h1>
            <div class="search-container">
                <input type="text" placeholder="Search for help...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>

        <div class="help-categories">
            <div class="category-card">
                <i class="fas fa-user-circle"></i>
                <h3>Account & Profile</h3>
                <p>Manage your account settings and profile information</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="category-card">
                <i class="fas fa-briefcase"></i>
                <h3>Freelancing</h3>
                <p>Learn about creating gigs and managing your services</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="category-card">
                <i class="fas fa-handshake"></i>
                <h3>Hiring</h3>
                <p>Find and hire the perfect freelancer for your project</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>

            <div class="category-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Payments & Pricing</h3>
                <p>Information about payment methods and pricing</p>
                <a href="#" class="learn-more">Learn More →</a>
            </div>
        </div>

        <div class="popular-topics">
            <h2>Popular Topics</h2>
            <div class="topics-grid">
                <div class="topic-item">
                    <h4>How to create a gig?</h4>
                    <p>Step-by-step guide to create your first service offering</p>
                </div>
                <div class="topic-item">
                    <h4>Payment Security</h4>
                    <p>Learn about our secure payment system</p>
                </div>
                <div class="topic-item">
                    <h4>Profile Verification</h4>
                    <p>How to verify your profile and build trust</p>
                </div>
                <div class="topic-item">
                    <h4>Dispute Resolution</h4>
                    <p>Understanding our dispute resolution process</p>
                </div>
            </div>
        </div>

        <div class="contact-support">
            <h2>Still Need Help?</h2>
            <p>Our support team is here to assist you</p>
            <a href="/contactus/contact.php" class="contact-button">Contact Support</a>
        </div>
    </div>

    <?php include '../footer/footer.php'; ?>
</body>
</html>
