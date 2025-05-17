<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Graphic Design Services</title>
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
            background: linear-gradient(135deg, #1b0530 0%, #4a1b6d 100%);
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

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
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

        .why-choose-us {
            background: white;
            padding: 3rem;
            border-radius: 10px;
            margin: 3rem 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .why-choose-us h2 {
            color: #1b0530;
            margin-bottom: 2rem;
            text-align: center;
        }

        .features-list {
            list-style: none;
            font-size: 1.1rem;
        }

        .features-list li {
            margin: 1rem 0;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .features-list li:hover {
            background: #e9ecef;
        }

        .cta-section {
            text-align: center;
            padding: 3rem;
            background: linear-gradient(135deg, #1b0530 0%, #4a1b6d 100%);
            color: white;
            border-radius: 10px;
            margin-top: 3rem;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 1rem;
            transition: background-color 0.3s ease;
        }

        .cta-button:hover {
            background: #ff5252;
        }
    </style>
</head>
<body>
    <div class="hero">
        <h1>Professional Graphic Design Services</h1>
        <p>Transform your ideas into stunning visual masterpieces with our expert graphic design team</p>
    </div>

    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <h3>Brand Identity</h3>
                <p>Create a memorable brand presence with custom logos, color schemes, and brand guidelines that reflect your unique identity.</p>
            </div>
            <div class="service-card">
                <h3>Marketing Materials</h3>
                <p>Design compelling brochures, flyers, and promotional materials that effectively communicate your message.</p>
            </div>
            <div class="service-card">
                <h3>Digital Graphics</h3>
                <p>Engaging social media graphics, web banners, and digital advertisements that capture attention and drive engagement.</p>
            </div>
        </div>

        <div class="why-choose-us">
            <h2>Why Choose Us</h2>
            <ul class="features-list">
                <li>🎨 Creative & Experienced Designers - Our team brings years of industry expertise to every project</li>
                <li>💡 Innovative Concepts - We push boundaries to create unique and memorable designs</li>
                <li>⚡ Quick Turnaround Time - Efficient delivery without compromising on quality</li>
                <li>🎯 Customized Solutions - Tailored designs that perfectly match your brand vision</li>
                <li>🤝 Collaborative Process - We work closely with you to ensure your satisfaction</li>
                <li>📌 Let's create something amazing together</li>
            </ul>
        </div>

        <div class="cta-section">
            <h2>Ready to Transform Your Brand?</h2>
            <p>Let's discuss how we can help bring your vision to life</p>
            <a href="#contact" class="cta-button">Get Started Now</a>
        </div>
    </div>
</body>
</html>