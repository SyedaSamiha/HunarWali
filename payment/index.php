<?php
session_start();
require_once '../config/database.php';

// Check if gig details are in session
if (!isset($_SESSION['gig_details']) || !isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

$gig = $_SESSION['gig_details'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Methods</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        header {
            flex-shrink: 0;
        }
        
        main {
            flex: 1;
            max-width: 1200px;
            margin: 2rem auto;
            text-align: center;
            padding: 0 1rem;
        }
        
        .main-title {
            color: #8a3342;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .main-subtitle {
            color: #444;
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .gig-info {
            margin-bottom: 2rem;
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 1px 6px rgba(44,62,80,0.06);
        }
        .methods {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        .method-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(44,62,80,0.10);
            padding: 1.5rem;
            text-align: center;
            width: 200px;
        }
        .payment-img {
            width: 100px;
            height: 50px;
            margin-bottom: 1rem;
        }
        .method-btn {
            width: 100%;
            padding: 0.8rem;
            border-radius: 8px;
            background: #8a3342;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .method-btn:hover {
            background: #6b2834;
        }
        
        footer {
            flex-shrink: 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <header>
        <?php include '../navbar/navbar.php'; ?>
    </header>
    <main>
        <h2 class="main-title">OUR PAYMENT METHODS</h2>
        <h3 class="main-subtitle">Choose your preferred payment option</h3>
        <div class="gig-info">
            <h4>Paying for: <?php echo htmlspecialchars($gig['gig_title']); ?></h4>
            <p>Price: Rs <?php echo number_format($gig['gig_pricing']); ?> PKR</p>
            <p>Seller: <?php echo htmlspecialchars($gig['seller_name']); ?></p>
        </div>
        <div class="methods">
            <div class="method-card">
                <img src="Easypaisa-logo.png" alt="EasyPaisa Logo" class="payment-img">
                <button class="method-btn" onclick="window.location.href='confirmation.php?method=EasyPaisa'">EASYPAISA</button>
            </div>
            <div class="method-card">
                <img src="jazz-cash-logo-png_seeklogo-343031.png" alt="JazzCash Logo" class="payment-img">
                <button class="method-btn" onclick="window.location.href='confirmation.php?method=JazzCash'">JAZZCASH</button>
            </div>
        </div>
    </main>
    <?php include '../footer/footer.php'; ?>
</body>
</html>