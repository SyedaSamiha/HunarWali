<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Hunarwali</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <header>
        <?php include '../navbar/navbar.php'; ?>
    </header>
    
    <main>
        <section class="contact-section">
            <!-- Decorative elements removed -->

            <div class="contact-container">
                <div class="contact-form">
                    <h1>Contact Us</h1>
                    <h2>How You Can Reach Us</h2>
                    <form>
                        <input type="text" placeholder="Your Name" required>
                        <input type="email" placeholder="Your Email" required>
                        <textarea placeholder="Your Message" required></textarea>
                        <button type="submit">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
                
                <div class="contact-info">
                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <span class="info-title">Phone</span>
                            <span class="info-detail">+123456789</span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <span class="info-title">Email</span>
                            <span class="info-detail">info@hunarwali.com</span>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <span class="info-title">Location</span>
                            <span class="info-detail">12 Example Street</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- <img src="yellow-flowers.png" alt="" class="decor-flowers"> -->
    <?php include '../footer/footer.php'; ?>
</body>

</html>