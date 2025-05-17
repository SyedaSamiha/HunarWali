<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="registration.css">
</head>
<body>
<?php 

?>
    <div class="image">
        <div class="registration-container">
            <div class="form-container">
                <h1>REGISTRATION</h1>
                <p>Please fill in the information to get yourself registered</p>
                
                <form method="POST" action="registration.php" id="registration-form" enctype="multipart/form-data">
                    <input type="text" name="username" id="username" placeholder="Name" required><br>
                    <input type="email" name="email" id="email" placeholder="Email" required><br>
                    <input type="password" name="password" id="password" placeholder="Password" required><br>
                    <input type="password" name="confirm_password" id="confirm-password" placeholder="Confirm Password" required><br>
                    
                    <!-- Gender Option -->
                    <select name="gender" id="gender" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; color: grey;">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select><br><br>
                    
                    <!-- Role (Freelancer or Client) -->
                    <select name="role" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; color: grey;">
                        <option value="freelancer">Freelancer</option>
                        <option value="client">Client</option>
                    </select><br><br>
                    
                    <!-- ID Card Upload Fields -->
                    <div class="id-upload-container">
                        <label for="id_front">Upload ID Card (Front):</label>
                        <input type="file" name="id_front" id="id_front" accept="image/*" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; margin-bottom: 10px;">
                        
                        <label for="id_back">Upload ID Card (Back):</label>
                        <input type="file" name="id_back" id="id_back" accept="image/*" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 16px; margin-bottom: 10px;">
                    </div>
                    
                    <button type="submit">Register</button>
                </form>
                
                <!-- Password Strength Message -->
                               <!-- Display error message from session -->
                <?php
session_start();
if (isset($_SESSION['message'])) {
    echo "<p class='animated-message'>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);  // Clear message after displaying it
}
?>

<!-- Add this to your HTML page -->
<script>
    window.onload = function() {
        const message = document.querySelector('.animated-message');
        if (message) {
            setTimeout(function() {
                message.style.display = 'none';  // Hide the message after 4 seconds
            }, 4000);
        }

        // Add form validation
        const form = document.getElementById('registration-form');
        form.addEventListener('submit', function(e) {
            const gender = document.getElementById('gender').value;
            const role = document.querySelector('select[name="role"]').value;
            const password = document.getElementById('password').value;
            
            // Check password length
            if (password.length < 8) {
                e.preventDefault();
                const errorMessage = document.createElement('p');
                errorMessage.className = 'animated-message';
                errorMessage.textContent = 'Password must be at least 8 characters long.';
                
                // Remove any existing error message
                const existingMessage = document.querySelector('.animated-message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add the new error message
                form.insertAdjacentElement('beforebegin', errorMessage);
                
                // Hide the message after 4 seconds
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 4000);
                return;
            }
            
            if (gender === 'male' && role === 'freelancer') {
                e.preventDefault();
                const errorMessage = document.createElement('p');
                errorMessage.className = 'animated-message';
                errorMessage.textContent = 'Sorry, male freelancers are not allowed to register.';
                
                // Remove any existing error message
                const existingMessage = document.querySelector('.animated-message');
                if (existingMessage) {
                    existingMessage.remove();
                }
                
                // Add the new error message
                form.insertAdjacentElement('beforebegin', errorMessage);
                
                // Hide the message after 4 seconds
                setTimeout(function() {
                    errorMessage.style.display = 'none';
                }, 4000);
            }
        });
    };
</script>

<!-- CSS for animation -->
<style>
    /* Styling for the message */
    .animated-message {
        color: red; /* Red color for error messages */
        font-size: 18px; /* Font size for the message */
        font-weight: bold;
        background-color: rgba(255, 0, 0, 0.1); /* Light red background */
        padding: 10px 20px;
        border-radius: 5px;
        max-width: 300px;
        margin: 10px auto; /* Center the message */
        text-align: center;
        opacity: 0;
        animation: fadeInOut 4s ease-in-out forwards; /* Apply animation */
    }

    /* Animation for fade-in and fade-out */
    @keyframes fadeInOut {
        0% {
            opacity: 0;
            transform: translateY(-20px); /* Start slightly above */
        }
        50% {
            opacity: 1;
            transform: translateY(0); /* Message in its original position */
        }
        100% {
            opacity: 0;
            transform: translateY(20px); /* Move down slightly before disappearing */
        }
    }

    /* ID Upload Container Styles */
    .id-upload-container {
        margin: 15px 0;
    }

    .id-upload-container label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: 500;
    }

    .id-upload-container input[type="file"] {
        background-color: #fff;
        cursor: pointer;
    }

    .id-upload-container input[type="file"]:hover {
        border-color: #666;
    }
</style>
                <div id="passwordStrengthMessage"></div>
            </div>
        </div>
    </div>
   
    <script src="registration.js"></script>
</body>
</html>
