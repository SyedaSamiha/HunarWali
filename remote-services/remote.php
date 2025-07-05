<?php session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Final year project</title>
    <link rel="stylesheet" href="remote.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>
<body>
        <header>
        <?php include '../navbar/navbar.php'; ?>

            <section class="services-section">
                <h2>Remote Services</h2>
            
                <div style="height: 20px;"></div>
            
                <div class="service-container">
                    <div class="service-cards">
                        <!-- Service Card 1 -->
                        <a href="/remote-services/content-creation.php" class="service-card">
                            <div class="icon">
                                <i class="fas fa-pencil-alt"></i>
                            </div>
                            <h3>WRITING AND CONTENT CREATION</h3>
                            <p>Creating engaging and original content for various platforms.</p>
                        </a>
            
                        <!-- Service Card 2 -->
                        <a href="/remote-services/graphic-designer.php" class="service-card">
                            <div class="icon">
                                <i class="fa fa-paint-brush"></i>
                            </div>
                            <h3>GRAPHIC DESIGNER</h3>
                            <p>Designing visually appealing graphics for your needs.</p>
                        </a>
            
                        <!-- Service Card 3 -->
                        <a href="/remote-services/digital-marketing.php" class="service-card">
                            <div class="icon">
                                <i class="fa fa-bullhorn"></i>
                            </div>
                            <h3> DIGITAL MARKETING</h3>
                            <p>Marketing your brand through various digital platforms.</p>
                        </a>
            
                        <!-- Service Card 4 -->
                        <a href="/remote-services/webappdev.php" class="service-card">
                            <div class="icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h3>WEB DEVELOPMENT</h3>
                            <p>Building and designing user-friendly websites.</p>
                        </a>
                    </div>
            
                    <div class="service-cards">
                        <!-- Service Card 5 -->
                        <a href="/remote-services/video-animation.php" class="service-card">
                            <div class="icon">
                                <i class="fa fa-film"></i>
                            </div>
                            <h3> VIDEO AND ANIMATION</h3>
                            <p>Video production, Creating dynamic and engaging videos for your brand.</p>
                        </a>
            
                        <!-- Service Card 6 -->
                        <a href="/remote-services/online-education.php" class="service-card">
                            <div class="icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h3>ONLINE EDUCATION</h3>
                            <p>Online tutoring and more.</p>
                        </a>
            
                        <!-- Service Card 7 -->
                        <a href="/remote-services/webappdev.php" class="service-card">
                            <div class="icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3>APP DEVELOPMENT</h3>
                            <p>Building mobile applications for various platforms.</p>
                        </a>
                    </div>
                </div>
            </section>

            
            <?php include '../footer/footer.php'; ?>

    </header>
    </body>
    </html>