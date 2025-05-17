<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Healthcare Services</title>
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
            background: linear-gradient(rgba(27, 5, 48, 0.9), rgba(27, 5, 48, 0.9)), url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
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
        <p>Health and well-being come first! Our dedicated nursing services provide compassionate, expert care for patients in need. Whether it's home care, post-surgical recovery, elderly assistance, or specialized medical support, our trained nurses ensure comfort, safety, and quality healthcare in the comfort of your home.</p>
    </div>

    <div class="container">
        <div class="features-section">
            <h1>WHY CHOOSE US</h1>
            <ul class="features-list">
                <li>
                    <span class="emoji">👩‍⚕️</span>
                    Qualified & Experienced Nurses
                </li>
                <li>
                    <span class="emoji">🏡</span>
                    Personalized Home Care
                </li>
                <li>
                    <span class="emoji">❤️</span>
                    Patient-Centered Approach
                </li>
            </ul>
        </div>

        <div class="section">
            <h2>Our Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>👴 Elderly Care</h3>
                    <p>Comprehensive care for seniors including daily living assistance, medication management, and companionship.</p>
                </div>
                <div class="service-card">
                    <h3>🏥 Post-Surgical Care</h3>
                    <p>Expert recovery support after surgery, including wound care, rehabilitation, and health monitoring.</p>
                </div>
                <div class="service-card">
                    <h3>💊 Medication Management</h3>
                    <p>Professional assistance with medication schedules, administration, and monitoring.</p>
                </div>
                <div class="service-card">
                    <h3>🏠 Home Health Care</h3>
                    <p>Complete healthcare services delivered in the comfort of your home.</p>
                </div>
            </div>
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
                    <textarea id="message" rows="5" placeholder="Tell us about your healthcare needs"></textarea>
                </div>
                <button class="btn">Request Consultation</button>
            </div>
        </div>
    </div>
</body>
</html>