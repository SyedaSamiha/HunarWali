<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web & App Development Services</title>
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
        <h1>Web & App Development Services</h1>
        <p>Transform your digital presence with cutting-edge web and mobile applications that drive business growth and user engagement.</p>
    </div>

    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <h3>🌐 Custom Website Development</h3>
                <p>Tailored websites built with modern technologies to showcase your brand and engage your audience effectively.</p>
            </div>
            <div class="service-card">
                <h3>📱 Mobile App Development</h3>
                <p>Native and cross-platform mobile applications that deliver exceptional user experiences across all devices.</p>
            </div>
            <div class="service-card">
                <h3>🛍️ E-commerce Solutions</h3>
                <p>Powerful online stores with secure payment gateways and intuitive shopping experiences.</p>
            </div>
            <div class="service-card">
                <h3>🔧 Web Application Development</h3>
                <p>Scalable web applications that streamline your business processes and enhance productivity.</p>
            </div>
            <div class="service-card">
                <h3>📊 Progressive Web Apps</h3>
                <p>Fast, reliable, and engaging PWAs that combine the best of web and mobile applications.</p>
            </div>
            <div class="service-card">
                <h3>🔄 API Integration</h3>
                <p>Seamless integration of third-party services and APIs to extend your application's functionality.</p>
            </div>
        </div>

        <div class="why-choose-us">
            <h2>Why Choose Us</h2>
            <ul class="features-list">
                <li>💡 Custom-Built Solutions</li>
                <li>📱 Mobile-Friendly & Responsive</li>
                <li>🔧 Ongoing Support & Maintenance</li>
                <li>⚡ High Performance & Speed</li>
                <li>🔒 Secure & Scalable</li>
                <li>🎯 User-Centric Design</li>
                <li>📈 SEO Optimized</li>
                <li>🤝 Dedicated Team</li>
            </ul>
        </div>

        <div class="cta-section">
            <h2>Ready to Build Your Digital Solution?</h2>
            <p>Let's create a powerful web or mobile application that drives your business forward.</p>
            <a href="#contact" class="cta-button">Start Your Project</a>
        </div>
    </div>
</body>
</html>