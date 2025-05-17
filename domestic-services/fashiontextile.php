<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fashion & Textile Services</title>
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
            background-color: #f9f7f7;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            background: linear-gradient(rgba(27, 5, 48, 0.8), rgba(27, 5, 48, 0.8)), url('https://images.unsplash.com/photo-1445205170230-053b83016050?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
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
            margin: 3rem 0;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card h2 {
            color: rgb(27, 5, 48);
            margin-bottom: 1rem;
            font-size: 1.8rem;
        }

        .feature-card p {
            color: #666;
            font-size: 1.1rem;
        }

        .services {
            background-color: white;
            padding: 3rem 2rem;
            border-radius: 10px;
            margin: 3rem 0;
        }

        .services h2 {
            color: rgb(27, 5, 48);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }

        .service-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .service-item {
            font-size: 1.2rem;
            padding: 1.5rem;
            background: #f9f7f7;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .service-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .cta-section {
            text-align: center;
            padding: 3rem 0;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: rgb(27, 5, 48);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.2rem;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background-color: rgb(40, 8, 70);
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Fashion & Textile Excellence</h1>
        <p>Welcome to our world of fashion and textile artistry! We believe that fashion is more than just clothing—it's a powerful expression of your unique personality and style. Our expert team of designers and craftsmen are dedicated to bringing your fashion dreams to life with precision, creativity, and passion.</p>
    </div>

    <div class="container">
        <div class="features">
            <div class="feature-card">
                <h2>👗 Custom & Trendy Designs</h2>
                <p>Stay ahead of fashion trends with our innovative designs. We create unique pieces that reflect your personal style while keeping up with the latest fashion movements.</p>
            </div>
            <div class="feature-card">
                <h2>🧶 Premium Quality Fabrics</h2>
                <p>Experience luxury with our carefully selected materials. We source only the finest fabrics to ensure your garments are not just beautiful but also comfortable and durable.</p>
            </div>
            <div class="feature-card">
                <h2>📏 Perfect Fit & Tailoring</h2>
                <p>Enjoy garments that fit you perfectly. Our expert tailors pay attention to every detail, ensuring a flawless fit that enhances your natural silhouette.</p>
            </div>
        </div>

        <div class="services">
            <h2>Our Services</h2>
            <ul class="service-list">
                <li class="service-item">Custom Clothing Design</li>
                <li class="service-item">Textile Pattern Creation</li>
                <li class="service-item">Professional Alterations</li>
                <li class="service-item">Fashion Consultation</li>
                <li class="service-item">Bridal Wear Design</li>
                <li class="service-item">Sustainable Fashion</li>
            </ul>
        </div>

        <div class="cta-section">
            <a href="#contact" class="cta-button">Start Your Fashion Journey</a>
        </div>
    </div>
</body>
</html>