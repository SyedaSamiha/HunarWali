<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News & Updates - HunarWali</title>
    <link rel="stylesheet" href="news-updates.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <?php include '../navbar/navbar.php'; ?>

        <!-- News & Updates Section -->
        <div class="updates-container">
            <header>
                <h1>Latest Updates</h1>
                <p>We're super excited to finally share all these amazing new features we've been working on!</p>
            </header>

            <div class="news-content">
                <!-- Featured News -->
                <div class="featured-news">
                    <img src="sa.png" alt="Big News">
                    <h2>Don't miss our BIG news!</h2>
                    <p>We're super excited to finally tell you about all these epic features we've worked on during the last months. These improvements are designed to make your experience even better.</p>
                    <a href="#" class="button">
                        Discover what's new
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- New Features -->
                <div class="new-releases">
                    <h3>New Releases</h3>
                    <p>Check out our amazing new features:</p>

                    <div class="features">
                        <!-- Feature 1 -->
                        <div class="feature">
                            <i class="fas fa-clock fa-2x" style="color: #f57c00;"></i>
                            <h4>Timer</h4>
                            <p>Pomodoro time! Now you can time your work sessions and stay laser focused.</p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="feature">
                            <i class="fas fa-chart-line fa-2x" style="color: #f57c00;"></i>
                            <h4>Measure</h4>
                            <p>Easily keep track of your work time and goals with our easy measuring tool.</p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="feature">
                            <i class="fas fa-sliders-h fa-2x" style="color: #f57c00;"></i>
                            <h4>Slider</h4>
                            <p>Use the slider to update everyone in your team about the project progress.</p>
                        </div>

                        <!-- Feature 4 -->
                        <div class="feature">
                            <i class="fas fa-users fa-2x" style="color: #f57c00;"></i>
                            <h4>Users</h4>
                            <p>Subscribe roles and define what each person on your team can access.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include '../footer/footer.php'; ?>
</body>

</html>