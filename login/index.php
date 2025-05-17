<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="image">
        <div class="login-container">
            <div class="form-container">
                <h1>LOGIN</h1>
                <p>Please enter your login details</p>

                <!-- Login Form -->
                <form method="POST" action="login.php" id="login-form">
                    <input type="email" id="email" name="email" placeholder="EMAIL" required>
                    <input type="password" id="password" name="password" placeholder="PASSWORD" required>
                    <button type="submit" id="submit-button">LOGIN</button>
                </form>

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
                        const form = document.getElementById('login-form');
                        form.addEventListener('submit', function(e) {
                            const email = document.getElementById('email').value;
                            const password = document.getElementById('password').value;
                            
                            if (!email || !password) {
                                e.preventDefault();
                                const errorMessage = document.createElement('p');
                                errorMessage.className = 'animated-message';
                                errorMessage.textContent = 'Please fill in all fields.';
                                
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
                        color: #e74c3c;
                        font-size: 0.95rem;
                        font-weight: 500;
                        background-color: rgba(231, 76, 60, 0.1);
                        padding: 12px 20px;
                        border-radius: 8px;
                        margin: 10px 0;
                        text-align: center;
                        animation: fadeInOut 4s ease-in-out forwards;
                    }

                    /* Animation for fade-in and fade-out */
                    @keyframes fadeInOut {
                        0% {
                            opacity: 0;
                            transform: translateY(-10px);
                        }
                        20% {
                            opacity: 1;
                            transform: translateY(0);
                        }
                        80% {
                            opacity: 1;
                            transform: translateY(0);
                        }
                        100% {
                            opacity: 0;
                            transform: translateY(10px);
                        }
                    }
                </style>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>
