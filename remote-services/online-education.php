<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Education Services</title>
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
        <h1>Online Education Services</h1>
        <p>Unlock knowledge from anywhere with our top-notch online tutoring services! Whether you're a student looking for academic support, a professional enhancing your skills, or a parent seeking the best education for your child, our expert tutors provide personalized, interactive, and engaging lessons tailored to your needs.</p>
    </div>

    <div class="container">
        <div class="services-grid">
            <div class="service-card">
                <h3>📚 Academic Tutoring</h3>
                <p>Personalized one-on-one tutoring sessions covering all subjects from elementary to university level, helping students excel in their studies.</p>
            </div>
            <div class="service-card">
                <h3>🎓 Test Preparation</h3>
                <p>Comprehensive preparation for standardized tests, entrance exams, and professional certifications with proven success strategies.</p>
            </div>
            <div class="service-card">
                <h3>💻 Technical Skills</h3>
                <p>Expert-led courses in programming, data science, and other technical skills to advance your career in the digital age.</p>
            </div>
            <div class="service-card">
                <h3>🌍 Language Learning</h3>
                <p>Interactive language courses with native speakers to help you master new languages through immersive learning experiences.</p>
            </div>
            <div class="service-card">
                <h3>🎨 Creative Arts</h3>
                <p>Learn music, art, and creative writing from professional artists and educators in a supportive online environment.</p>
            </div>
            <div class="service-card">
                <h3>📊 Business Skills</h3>
                <p>Professional development courses in leadership, management, and business skills to enhance your career prospects.</p>
            </div>
        </div>

        <div class="why-choose-us">
            <h2>Why Choose Us</h2>
            <ul class="features-list">
                <li>👨‍🏫 Qualified & Experienced Tutors</li>
                <li>🖥️ Flexible Learning Options</li>
                <li>🎯 Personalized Study Plans</li>
                <li>💰 Affordable & Transparent Pricing</li>
                <li>📱 Interactive Learning Platform</li>
                <li>⏰ 24/7 Learning Support</li>
                <li>📊 Progress Tracking</li>
                <li>🤝 Dedicated Student Success Team</li>
            </ul>
        </div>

        <div class="cta-section">
            <h2>Start Your Learning Journey Today</h2>
            <p>Join thousands of successful students who have transformed their lives through our online education platform.</p>
            <a href="#contact" class="cta-button">Get Started Now</a>
        </div>
    </div>
</body>
</html>