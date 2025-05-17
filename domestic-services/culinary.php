<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Culinary Services - Professional Cooking & Catering</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card h3 {
            color: #1b0530;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .feature-card p {
            color: #666;
        }

        .services {
            background: #1b0530;
            color: white;
            padding: 3rem 2rem;
            margin-bottom: 3rem;
        }

        .services h2 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .service-item {
            text-align: center;
            padding: 1.5rem;
        }

        .service-item i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .cta-button {
            display: inline-block;
            background: #ff6b6b;
            color: white;
            padding: 1rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .cta-button:hover {
            background: #ff5252;
        }

        .testimonials {
            padding: 3rem 0;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .testimonial-card p {
            font-style: italic;
            margin-bottom: 1rem;
        }

        .testimonial-card .author {
            font-weight: 500;
            color: #1b0530;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="hero">
        <h1>Exquisite Culinary Experiences</h1>
        <p>Elevate your dining experience with our professional culinary services. From intimate gatherings to grand celebrations, we bring passion, creativity, and excellence to every dish.</p>
    </div>

    <div class="container">
        <div class="features">
            <div class="feature-card">
                <h3>👨‍🍳 Expert Chefs</h3>
                <p>Our team of professional chefs brings years of experience and culinary expertise to create memorable dining experiences.</p>
            </div>
            <div class="feature-card">
                <h3>🥗 Premium Ingredients</h3>
                <p>We source only the finest, freshest ingredients to ensure exceptional quality and taste in every dish.</p>
            </div>
            <div class="feature-card">
                <h3>🍱 Customized Menus</h3>
                <p>Personalized meal plans and catering services tailored to your preferences and dietary requirements.</p>
            </div>
        </div>

        <div class="services">
            <h2>Our Services</h2>
            <div class="services-grid">
                <div class="service-item">
                    <i class="fas fa-utensils"></i>
                    <h3>Private Dining</h3>
                    <p>Intimate dining experiences in the comfort of your home</p>
                </div>
                <div class="service-item">
                    <i class="fas fa-birthday-cake"></i>
                    <h3>Event Catering</h3>
                    <p>Full-service catering for weddings, corporate events, and special occasions</p>
                </div>
                <div class="service-item">
                    <i class="fas fa-book"></i>
                    <h3>Cooking Classes</h3>
                    <p>Learn culinary skills from our expert chefs</p>
                </div>
                <div class="service-item">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Meal Planning</h3>
                    <p>Customized weekly meal plans and preparation</p>
                </div>
            </div>
        </div>

        <div class="testimonials">
            <h2 style="text-align: center; margin-bottom: 2rem; color: #1b0530;">What Our Clients Say</h2>
            <div class="testimonial-card">
                <p>"The culinary team exceeded our expectations. The food was not only delicious but beautifully presented. Our guests were impressed!"</p>
                <div class="author">- Sarah Johnson, Wedding Reception</div>
            </div>
            <div class="testimonial-card">
                <p>"The cooking class was informative and fun. I learned so many new techniques that I use in my daily cooking now."</p>
                <div class="author">- Michael Chen, Cooking Class Participant</div>
            </div>
        </div>

        <div style="text-align: center; margin: 3rem 0;">
            <a href="#contact" class="cta-button">Book Your Culinary Experience</a>
        </div>
    </div>
</body>
</html>