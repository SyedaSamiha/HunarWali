<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ArtCraft Services - Creative Excellence</title>
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
            background: linear-gradient(rgba(27, 5, 48, 0.8), rgba(27, 5, 48, 0.8)), url('https://images.unsplash.com/photo-1513364776144-60967b0f800f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
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
            padding: 1rem;
            background: #f9f7f7;
            border-radius: 8px;
            text-align: center;
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
        <h1>ArtCraft Services</h1>
        <p>Where Creativity Meets Excellence</p>
    </div>

    <div class="container">
        <div class="features">
            <div class="feature-card">
                <h2>🎭 Creative Excellence</h2>
                <p>Our team of skilled artists brings years of experience and passion to every project. From traditional techniques to modern innovations, we create masterpieces that tell your story.</p>
            </div>
            <div class="feature-card">
                <h2>🛍️ Custom Creations</h2>
                <p>Every piece we create is uniquely tailored to your vision. Whether it's a personalized painting, custom home décor, or a special gift, we ensure your ideas come to life exactly as you imagine.</p>
            </div>
            <div class="feature-card">
                <h2>🎁 Perfect Gifts</h2>
                <p>Looking for that perfect gift? Our handcrafted pieces make memorable presents for any occasion. Each creation carries the warmth of personal touch and artistic dedication.</p>
            </div>
        </div>

        <div class="services">
            <h2>Our Services</h2>
            <ul class="service-list">
                <li class="service-item">Custom Paintings & Portraits</li>
                <li class="service-item">DIY Craft Workshops</li>
                <li class="service-item">Home Décor Design</li>
                <li class="service-item">Event Decoration</li>
                <li class="service-item">Art Classes</li>
                <li class="service-item">Gift Collections</li>
            </ul>
        </div>

        <div class="cta-section">
            <a href="#contact" class="cta-button">Start Your Creative Journey</a>
        </div>
    </div>
</body>
</html>








