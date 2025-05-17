<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Decorative Art Services - Creative Excellence</title>
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
            background: linear-gradient(rgba(27, 5, 48, 0.8), rgba(27, 5, 48, 0.8)), url('https://images.unsplash.com/photo-1579783902614-a3fb3927b6a5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.8;
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

        .pricing-section {
            background-color: white;
            padding: 3rem;
            border-radius: 10px;
            margin: 3rem 0;
        }

        .pricing-section h2 {
            color: rgb(27, 5, 48);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
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
            background-color: #f9f7f7;
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
        <p>Art has the power to bring life, beauty, and personality to any space! 🖼️ Our decorative art services offer handcrafted designs, custom wall art, home décor pieces, and artistic installations that enhance your surroundings with elegance and creativity. Whether you're decorating your home, office, or event space, we bring artistic excellence to every piece.</p>
    </div>

    <div class="container">
        <div class="features">
            <div class="feature-card">
                <h2>🎨 Unique & Handcrafted Designs</h2>
                <p>Each piece is meticulously crafted by our skilled artists, ensuring one-of-a-kind creations that reflect your personal style and vision.</p>
            </div>
            <div class="feature-card">
                <h2>🏡 Perfect for Homes & Businesses</h2>
                <p>Transform any space with our versatile art solutions, from residential interiors to commercial spaces, creating memorable environments.</p>
            </div>
            <div class="feature-card">
                <h2>⚡ Creative & Professional Artists</h2>
                <p>Our team of experienced artists combines traditional techniques with modern innovation to deliver exceptional results.</p>
            </div>
        </div>

        <div class="services">
            <h2>Our Services</h2>
            <ul class="service-list">
                <li class="service-item">Custom Wall Art & Murals</li>
                <li class="service-item">Handcrafted Home Décor</li>
                <li class="service-item">Event Space Decoration</li>
                <li class="service-item">Corporate Art Solutions</li>
                <li class="service-item">Art Restoration</li>
                <li class="service-item">Custom Gift Art</li>
            </ul>
        </div>

        <div class="pricing-section">
            <h2>Pricing Plans</h2>
            <table class="pricing-table">
                <thead>
                    <tr>
                        <th>Service Type</th>
                        <th>Description</th>
                        <th>Price Range</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Custom Wall Art</td>
                        <td>Hand-painted canvas or mural</td>
                        <td>$200-$1000</td>
                    </tr>
                    <tr>
                        <td>Home Décor Pieces</td>
                        <td>Custom sculptures and installations</td>
                        <td>$150-$800</td>
                    </tr>
                    <tr>
                        <td>Event Decoration</td>
                        <td>Full venue artistic transformation</td>
                        <td>$500-$2000</td>
                    </tr>
                    <tr>
                        <td>Corporate Art</td>
                        <td>Business space customization</td>
                        <td>Contact for Quote</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="cta-section">
            <a href="#contact" class="cta-button">Start Your Artistic Journey</a>
        </div>
    </div>
</body>
</html>