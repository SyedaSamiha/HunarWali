<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video & Animation Services</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .hero {
            background: linear-gradient(135deg, #2b1055, #7597de);
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

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        .service-card h3 {
            color: #2b1055;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .service-card p {
            color: #666;
        }

        .why-choose-us {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
        }

        .why-choose-us h2 {
            color: #2b1055;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }

        .features-list {
            list-style: none;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .features-list li {
            font-size: 1.1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cta-section {
            text-align: center;
            margin-top: 3rem;
            padding: 3rem;
            background: linear-gradient(135deg, #2b1055, #7597de);
            color: white;
            border-radius: 10px;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: white;
            color: #2b1055;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            margin-top: 1rem;
            transition: transform 0.3s ease;
        }

        .cta-button:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Video & Animation Services</h1>
        <p>Transform your ideas into captivating visual stories with our professional video and animation services.</p>
    </div>

    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <h3>🎬 Corporate Videos</h3>
                <p>Professional corporate videos that showcase your brand's story, values, and mission to engage your audience.</p>
            </div>
            <div class="service-card">
                <h3>🎨 2D Animation</h3>
                <p>Engaging 2D animations that bring your concepts to life with smooth motion and creative storytelling.</p>
            </div>
            <div class="service-card">
                <h3>🎭 3D Animation</h3>
                <p>Stunning 3D animations that create immersive experiences and detailed visual representations.</p>
            </div>
            <div class="service-card">
                <h3>📱 Social Media Videos</h3>
                <p>Short-form videos optimized for social media platforms to boost engagement and brand awareness.</p>
            </div>
            <div class="service-card">
                <h3>🎥 Product Showcases</h3>
                <p>Dynamic product videos that highlight features and benefits to drive sales and customer interest.</p>
            </div>
            <div class="service-card">
                <h3>🎬 Motion Graphics</h3>
                <p>Eye-catching motion graphics that communicate complex information in an engaging way.</p>
            </div>
        </div>

        <div class="why-choose-us">
            <h2>Why Choose Us</h2>
            <ul class="features-list">
                <li>🎬 Creative & Experienced Team</li>
                <li>💰 Affordable & Transparent Pricing</li>
                <li>🎞️ Customized Animations</li>
                <li>⏱️ Quick Turnaround Time</li>
                <li>🎨 Modern Visual Style</li>
                <li>📱 Multi-platform Optimization</li>
                <li>🤝 Dedicated Project Manager</li>
                <li>✨ High-Quality Output</li>
            </ul>
        </div>

        <div class="cta-section">
            <h2>Ready to Bring Your Vision to Life?</h2>
            <p>Let's create stunning videos and animations that will make your brand stand out.</p>
            <a href="#contact" class="cta-button">Start Your Project</a>
        </div>
    </div>
</body>
</html>