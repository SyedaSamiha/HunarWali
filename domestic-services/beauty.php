<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beauty & Wellness Services</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #fafafa;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            background: linear-gradient(rgba(27, 5, 48, 0.8), rgba(27, 5, 48, 0.8)), url('https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .services-section {
            background-color: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .services-section h2 {
            color: rgb(27, 5, 48);
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .features-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 0;
        }

        .feature-item {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
        }

        .feature-item li {
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .cta-button {
            display: inline-block;
            background-color: rgb(27, 5, 48);
            color: white;
            padding: 1rem 2rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 2rem;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: rgb(40, 8, 70);
        }

        .emoji {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Beauty & Wellness Services</h1>
        <p>Self-care isn't a luxury, it's a necessity! 🌿 Our premium beauty and wellness services help you look and feel your best with expert skincare, haircare, and relaxation treatments.</p>
    </div>

    <div class="container">
        <div class="services-section">
            <h2>💖 WHY CHOOSE US?</h2>
            <ul class="features-list">
                <div class="feature-item">
                    <div class="emoji">💅</div>
                    <li>Professional & Skilled Experts</li>
                    <p>Our team consists of certified professionals with years of experience in the beauty industry.</p>
                </div>
                <div class="feature-item">
                    <div class="emoji">💆‍♂️</div>
                    <li>Relaxing & Rejuvenating Treatments</li>
                    <p>Experience ultimate relaxation with our premium spa and wellness treatments.</p>
                </div>
                <div class="feature-item">
                    <div class="emoji">⏳</div>
                    <li>Personalized Self-Care Plans</li>
                    <p>Customized beauty and wellness programs tailored to your specific needs.</p>
                </div>
                <div class="feature-item">
                    <div class="emoji">🎀</div>
                    <li>Affordable & Luxurious Experience</li>
                    <p>Premium services at competitive prices, ensuring you get the best value for your investment.</p>
                </div>
            </ul>
            <div style="text-align: center;">
                <a href="#book-now" class="cta-button">Book Your Appointment</a>
            </div>
        </div>
    </div>
</body>
</html>