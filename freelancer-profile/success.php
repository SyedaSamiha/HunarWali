<?php
session_start();

// Display success message if redirected after form submission
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <style>
        /* Simple CSS animation for success message */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        .success-container {
            text-align: center;
            margin-top: 100px;
            animation: fadeIn 2s ease-in-out;
        }

        .success-message {
            font-size: 2em;
            color: green;
        }

        .success-animation {
            font-size: 3em;
            color: #f39c12;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .redirect-link {
            display: block;
            margin-top: 20px;
            font-size: 1.2em;
            color: #2980b9;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="success-container">
    <div class="success-message">
        Your profile has been created successfully!
    </div>
    <div class="success-animation">🎉</div>

    <!-- Provide a link to redirect to the profile page or homepage after a few seconds -->
    <a href="profile.php" class="redirect-link">Go to your Profile</a>
</div>

<script>
    // Automatically redirect to the profile page after 5 seconds
    setTimeout(function() {
        window.location.href = "profile.php";
    }, 5000);
</script>

</body>
</html>
