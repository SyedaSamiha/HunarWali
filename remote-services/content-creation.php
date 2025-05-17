<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Creation Services</title>
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
        <h1>Content Creation Services</h1>
        <p>Transform your digital presence with compelling content that engages, informs, and converts your audience.</p>
    </div>

    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <h3>📝 Blog Writing</h3>
                <p>Engaging, SEO-optimized blog posts that establish your authority and drive organic traffic to your website.</p>
            </div>
            <div class="service-card">
                <h3>📱 Social Media Content</h3>
                <p>Strategic social media posts that build brand awareness and foster meaningful engagement with your audience.</p>
            </div>
            <div class="service-card">
                <h3>🎥 Video Production</h3>
                <p>Professional video content that tells your brand story and captures your audience's attention.</p>
            </div>
            <div class="service-card">
                <h3>🎨 Graphic Design</h3>
                <p>Eye-catching visuals and infographics that communicate your message effectively and enhance brand recognition.</p>
            </div>
            <div class="service-card">
                <h3>📊 Content Strategy</h3>
                <p>Comprehensive content planning and strategy to ensure your content aligns with your business goals.</p>
            </div>
            <div class="service-card">
                <h3>📈 SEO Optimization</h3>
                <p>Content optimization to improve search engine rankings and increase organic visibility.</p>
            </div>
        </div>

        <div class="why-choose-us">
            <h2>Why Choose Us</h2>
            <ul class="features-list">
                <li>🖋️ Creative & Engaging Writing</li>
                <li>📸 Visual Storytelling</li>
                <li>📣 Social Media Expertise</li>
                <li>📊 SEO-Optimized Content</li>
                <li>📝 Personalized Branding</li>
                <li>🎯 Data-Driven Approach</li>
                <li>⏱️ Timely Delivery</li>
                <li>🤝 Dedicated Support</li>
            </ul>
        </div>

        <div class="cta-section">
            <h2>Ready to Elevate Your Content?</h2>
            <p>Let's create content that resonates with your audience and drives results.</p>
            <a href="#contact" class="cta-button">Get Started Today</a>
        </div>
    </div>
</body>
</html>