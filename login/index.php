<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HunarWali</title>
    <link rel="stylesheet" href="modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="modern-login.js" defer></script>
</head>
<body>
    <div class="back-to-home">
        <a href="/"><i class="fas fa-arrow-left"></i> Back to Home</a>
    </div>
    
    <div class="image">
        <div class="login-container">
            <div class="form-container">
                <h1>LOGIN</h1>
                <p>Welcome back! Please enter your login details</p>

                <!-- Login Form -->
                <form method="POST" action="login.php" id="login-form">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <i class="fas fa-eye-slash toggle-password"></i>
                    </div>
                    <button type="submit" id="submit-button"><i class="fas fa-sign-in-alt"></i> LOGIN</button>
                </form>
                
                <div class="register-link">
                    Don't have an account? <a href="/registration/index.php">Register Now</a>
                </div>

                <!-- Display error message from session -->
                <?php
                session_start();
                if (isset($_SESSION['message'])) {
                    echo "<p class='animated-message'>" . $_SESSION['message'] . "</p>";
                    unset($_SESSION['message']);  // Clear message after displaying it
                }
                ?>

                <!-- The styling for animated-message is now in modern-style.css -->
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>
