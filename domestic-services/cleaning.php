<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Cleaning Services</title>
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
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero-section {
            background: linear-gradient(rgba(27, 5, 48, 0.9), rgba(27, 5, 48, 0.9)), url('https://images.unsplash.com/photo-1581578731548-c64695cc6952?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero-section p {
            font-size: 1.25rem;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
        }

        .features-section {
            background-color: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .features-section h1 {
            color: rgb(27, 5, 48);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }

        .features-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 0;
        }

        .features-list li {
            font-size: 1.25rem;
            padding: 1.5rem;
            background-color: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .features-list li:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .emoji {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .section {
            background-color: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .section h2 {
            color: rgb(27, 5, 48);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .service-card {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .service-card h3 {
            color: rgb(27, 5, 48);
            margin-bottom: 1rem;
        }

        .pricing-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        .pricing-table th,
        .pricing-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .pricing-table th {
            background-color: rgb(27, 5, 48);
            color: white;
        }

        .pricing-table tr:hover {
            background-color: #f8f9fa;
        }

        .contact-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgb(27, 5, 48);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .btn {
            background-color: rgb(27, 5, 48);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: rgb(40, 8, 70);
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <p>A clean space is a happy and healthy space! Our top-notch cleaning services ensure your home, office, or business stays fresh, sanitized, and sparkling clean. From deep cleaning to regular maintenance, we tailor our services to meet your needs, providing a hassle-free and hygienic environment.</p>
    </div>

    <div class="container">
        <div class="features-section">
            <h1>WHY CHOOSE US</h1>
            <ul class="features-list">
                <li>
                    <span class="emoji">🧽</span>
                    Experienced Cleaners
                </li>
                <li>
                    <span class="emoji">⏳</span>
                    Flexible Scheduling
                </li>
                <li>
                    <span class="emoji">💰</span>
                    Affordable & Transparent Pricing
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>🏠 Residential Cleaning</h3>
                    <p>Comprehensive cleaning services for your home, including dusting, vacuuming, mopping, and sanitizing all surfaces.</p>
                </div>
                <div class="service-card">
                    <h3>🏢 Commercial Cleaning</h3>
                    <p>Professional cleaning solutions for offices, retail spaces, and commercial properties.</p>
                </div>
                <div class="service-card">
                    <h3>✨ Deep Cleaning</h3>
                    <p>Thorough cleaning of hard-to-reach areas, including baseboards, windows, and appliances.</p>
                </div>
                <div class="service-card">
                    <h3>🧹 Move In/Out Cleaning</h3>
                    <p>Specialized cleaning services for moving transitions, ensuring your space is spotless.</p>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>Pricing Plans</h2>
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>Service Type</th>
                        <th>Duration</th>
                        <th>Price Range</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Cleaning</td>
                        <td>2-3 hours</td>
                        <td>$100-$150</td>
                    </tr>
                    <tr>
                        <td>Deep Cleaning</td>
                        <td>4-6 hours</td>
                        <td>$200-$300</td>
                    </tr>
                    <tr>
                        <td>Move In/Out</td>
                        <td>6-8 hours</td>
                        <td>$300-$400</td>
                    </tr>
                    <tr>
                        <td>Commercial Cleaning</td>
                        <td>Custom</td>
                        <td>Contact for Quote</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2>Contact Us</h2>
            <div class="contact-form">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" placeholder="Your name">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" placeholder="Your email">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" rows="5" placeholder="Tell us about your cleaning needs"></textarea>
                </div>
                <button class="btn">Send Message</button>
            </div>
        </div>
    </div>
</body>
</html>