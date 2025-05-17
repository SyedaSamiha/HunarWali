<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fiverr Profile</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<header>
    <div class="navbar">
        <div class="nav_logo">
            <div class="logo">
                <a href="#">
                    <img src="image-removebg-preview.png" alt="Logo">
                </a>
            </div>
        </div>
        <ul class="nav-content">
            <li><a href="/Home page/index.html">DASHBOARD</a></li>
            <li><a href="#">MESSAGES</a></li>
            <li><a href="/Hire Freelancers/hire.html">FREELANCERS</a></li>
            <li><a href="/Gig creation/gig.html">CREATE GIG</a></li>
            <li><a href="/profile/index.html">PROFILE</a></li>
        </ul>
    </div>
</header>

<div style="height: 50px;"></div>
<div class="container">
    <div class="styled-box">
        <!-- Left Section (Profile) -->
        <div class="left-section">
            <?php if ($profile_data): ?>
                <img src="<?php echo $profile_data['profile_picture']; ?>" alt="Profile Picture" class="profile-img">
                <h2><?php echo $profile_data['display_name']; ?></h2>
                <p><?php echo $profile_data['profession']; ?></p>
                <button class="button">SHOW REVIEWS</button>
                <div class="buttoncontainer">
                    <button class="button">CONTACT ME</button>
                    <button class="button">EDIT PROFILE</button>
                </div>
                <div class="social-links">
                    <a href="#" class="fa fa-facebook"></a>
                    <a href="#" class="fa fa-twitter"></a>
                    <a href="#" class="fa fa-linkedin-square"></a>
                </div>
            <?php else: ?>
                <p>No profile data available.</p>
            <?php endif; ?>
        </div>

        <!-- Right Section (My Gigs) -->
        <div class="right-section">
            <h3 style="text-align: center;">My Gigs</h3>
            <div class="gig-cards" id="gig-cards-container">
                <?php if ($gig_result && $gig_result->num_rows > 0): ?>
                    <?php while ($gig = $gig_result->fetch_assoc()): ?>
                        <div class="gig-card">
                            <img src="<?php echo $gig['gig_image']; ?>" alt="Gig Image" class="gig-img">
                            <h4><?php echo $gig['title']; ?></h4>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No gigs found.</p>
                <?php endif; ?>
            </div>
            
            <button class="create-gig-button" id="create-gig-btn">+</button>
        </div>
    </div>
</div>

<!-- Hidden Gig Form -->
<div class="gig-form-container" id="gig-form-container" style="display: none;">
    <h3>Create a New Gig</h3>
    <form id="gig-form">
        <input type="text" id="gig-title" placeholder="Gig Title" required><br>
        <textarea id="gig-description" placeholder="Gig Description" required></textarea><br>
        <input type="text" id="gig-pricing" placeholder="Pricing" required><br>
        <input type="text" id="gig-tags" placeholder="Tags" required><br>
        <button type="submit">Create Gig</button>
    </form>
</div>

<script>
    // Fetch the number of gigs from PHP and assign it to gigCount
    let gigCount = <?php echo $gig_count; ?>;

    document.addEventListener("DOMContentLoaded", function() {
        const createGigButton = document.getElementById('create-gig-btn');
        const gigFormContainer = document.getElementById('gig-form-container');
        const gigCardsContainer = document.getElementById('gig-cards-container');

        // Show gig form when "+" button is clicked
        createGigButton.addEventListener('click', function() {
            gigFormContainer.style.display = 'block';
        });

        // Handle gig form submission
        document.getElementById('gig-form').addEventListener('submit', function(event) {
            event.preventDefault();

            const title = document.getElementById('gig-title').value;
            const description = document.getElementById('gig-description').value;
            const pricing = document.getElementById('gig-pricing').value;
            const tags = document.getElementById('gig-tags').value;

            // Create new gig card dynamically
            const newGigCard = document.createElement('div');
            newGigCard.classList.add('gig-card');
            newGigCard.innerHTML = `
                <img src="gig-placeholder.jpg" alt="Gig Image" class="gig-img">
                <h4>${title}</h4>
                <p>${description}</p>
                <p>Pricing: ${pricing}</p>
                <p>Tags: ${tags}</p>
            `;

            gigCardsContainer.appendChild(newGigCard);

            // Hide form and update gig count
            gigFormContainer.style.display = 'none';
            gigCount++;

            // Move the "+" button downward as more gigs are added
            createGigButton.style.bottom = `${20 + gigCount * 10}px`; // Adjust the button's position

            // Reset the form
            document
