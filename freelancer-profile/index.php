<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Check if user already has a profile
include('db_connection.php');
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM freelancer_profiles WHERE user_id = '$user_id' LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // Redirect to profile page if the user already has a profile
    header("Location: profile.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancer Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container" id="personal-form">
        <h2 class="form-title">Personal Information</h2>
        <form action="freelancerProfile.php" method="POST" enctype="multipart/form-data">

            <!-- Full Name -->
            <label class="form-label" for="first-name">Full Name* <span class="private-text">PRIVATE</span></label>
            <div style="height: 30px;"></div>
            <div class="name-container">
                <input type="text" name="first_name" id="first-name" placeholder="FIRST NAME" required>
                <input type="text" name="last_name" id="last-name" placeholder="LAST NAME.." required>
            </div>

            <!-- Display Name -->
            <label class="form-label" for="display-name">Display Name*</label>
            <input type="text" name="display_name" id="display-name" placeholder="TYPE YOUR DISPLAY NAME" required>

            <!-- Profile Picture -->
            <label class="form-label" for="profile-picture">Profile Picture*</label>
            <div class="profile-picture-container">
                <label for="profile-upload">
                    <img src="profile.png" alt="Profile Picture" class="profile-picture" id="profile-preview">
                </label>
                <input type="file" name="profile_picture" id="profile-upload" accept="image/*" hidden required>
            </div>

            <!-- Description -->
            <label class="form-label" for="description">Description*</label>
            <textarea name="description" id="description" placeholder="Tell us about your professional background" required></textarea>

            <!-- Profession -->
            <label class="form-label" for="profession">Your Profession*</label>
            <select name="profession" class="profession-select" required>
                <option value="CLEANING">CLEANING</option>
                <option value="ART AND CRAFT">ART AND CRAFT</option>
                <option value="BEAUTY AND WELLNESS">BEAUTY AND WELLNESS</option>
                <option value="FASHION AND TEXTILE">FASHION AND TEXTILE</option>
                <option value="HEALTH AND CARE">HEALTH AND CARE</option>
                <option value="DECORATIVE ART">DECORATIVE ART</option>
                <option value="WRITING AND CONTENT CREATION">WRITING AND CONTENT CREATION</option>
                <option value="GRAPHIC DESIGNER">GRAPHIC DESIGNER</option>
                <option value="DIGITAL MARKETING">DIGITAL MARKETING</option>
                <option value="ONLINE EDUCATION">ONLINE EDUCATION</option>
                <option value="WEB AND APP DEVELOPMENT">WEB AND APP DEVELOPMENT</option>
                <option value="VIDEO AND ANIMATION">VIDEO AND ANIMATION</option>
            </select>

            <!-- Skills -->
            <label class="form-label" for="skills">Skills*</label>
            <textarea name="skills" id="skills" class="skills-input" placeholder="TYPE SKILLS" required></textarea>

            <button class="continue-button" type="submit">SUBMIT!</button>
        </form>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
