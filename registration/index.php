<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - HunarWali</title>
    <link rel="stylesheet" href="modern-style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom JavaScript -->
    <script src="modern-registration.js" defer></script>
</head>
<body>
<?php 

?>
    <div class="image">
        <!-- Back to Home Link -->
        <div class="back-to-home">
            <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to Home</a>
        </div>
        
        <div class="registration-container">
            <div class="form-container">
                <h1>REGISTRATION</h1>
                <p>Please fill in the information to get yourself registered</p>
                
                <form method="POST" action="registration.php" id="registration-form" enctype="multipart/form-data">
                    <!-- Username with icon -->
                    <div class="input-group">
                        <input type="text" name="username" id="username" placeholder="Full Name" required>
                        <i class="fas fa-user"></i>
                    </div>
                    
                    <!-- Email with icon -->
                    <div class="input-group">
                        <input type="email" name="email" id="email" placeholder="Email Address" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                    
                    <!-- Password with icon and toggle -->
                    <div class="input-group">
                        <input type="password" name="password" id="password" placeholder="Password (min. 8 characters)" required>
                        <i class="fas fa-lock"></i>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                    
                    <!-- Confirm Password with icon and toggle -->
                    <div class="input-group">
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                        <i class="fas fa-lock"></i>
                        <i class="fas fa-eye toggle-confirm-password"></i>
                    </div>
                    
                    <!-- Gender Option with icon -->
                    <div class="input-group">
                        <select name="gender" id="gender" required>
                            <option value="" disabled selected>Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <i class="fas fa-venus-mars"></i>
                    </div>
                    
                    <!-- Role with icon -->
                    <div class="input-group">
                        <select name="role" id="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="freelancer">Freelancer</option>
                            <option value="client">Client</option>
                        </select>
                        <i class="fas fa-briefcase"></i>
                    </div>
                    
                    <!-- ID Card Upload Fields -->
                    <div class="id-upload-container">
                        <label for="id_card_front"><i class="fas fa-id-card"></i> Upload ID Card (Front):</label>
                        <input type="file" name="id_front" id="id_card_front" accept="image/*" required>
                        <div id="id-front-preview" class="id-preview"></div>
                        
                        <label for="id_card_back"><i class="fas fa-id-card"></i> Upload ID Card (Back):</label>
                        <input type="file" name="id_back" id="id_card_back" accept="image/*" required>
                        <div id="id-back-preview" class="id-preview"></div>
                    </div>
                    
                    <button type="submit" id="submit-button">Register Now</button>
                </form>
                
                <!-- Login Link -->
                <div class="login-link">
                    Already have an account? <a href="../login/">Login here</a>
                </div>
                
                <!-- Error Message Container -->
                <div class="error-message-container">
                    <!-- Display error message from session -->
                    <?php
                    session_start();
                    if (isset($_SESSION['message'])) {
                        echo "<p class='animated-message'><i class='fas fa-exclamation-circle'></i> " . $_SESSION['message'] . "</p>";
                        unset($_SESSION['message']);  // Clear message after displaying it
                    }
                    ?>
                </div>
                
                <!-- Password Strength Message -->
                <div id="passwordStrengthMessage"></div>
            </div>
        </div>
    </div>
</body>
</html>
