<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/database.php';

// Check if gig details and user are in session
if (!isset($_SESSION['gig_details']) || !isset($_SESSION['user_id'])) {
    header("Location: /index.php");
    exit;
}

$gig = $_SESSION['gig_details'];
$buyer_id = $_SESSION['user_id'];
$payment_method = isset($_GET['method']) ? $_GET['method'] : 'Unknown';

// Simulate payment success (replace with actual payment gateway integration)
$payment_success = true; // Placeholder for payment gateway response
$transaction_id = "TXN-" . time(); // Simulated transaction ID
$date_time = date('Y-m-d H:i:s');

if ($payment_success) {
    // Insert order into orders table
    $query = "INSERT INTO orders (buyer_id, seller_id, gig_id, price, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        error_log("Order insertion prepare failed: " . $conn->error);
        $message = "Error preparing order insertion.";
        $payment_success = false;
    } else {
        $stmt->bind_param('iiids', $buyer_id, $gig['seller_id'], $gig['gig_id'], $gig['gig_pricing'], $status);
        $status = 'Order Placed';
        if ($stmt->execute()) {
            // Send demo message from buyer to seller
            $demo_message = "Order has been placed";
            $message_query = "INSERT INTO messages (sender_id, receiver_id, message, created_at, is_read) VALUES (?, ?, ?, NOW(), ?)";
            $message_stmt = $conn->prepare($message_query);
            if (!$message_stmt) {
                error_log("Message insertion prepare failed: " . $conn->error);
                $message = "Error preparing message insertion.";
                $payment_success = false;
            } else {
                $receiver_id = $gig['seller_id'];
                $is_read = 0;
                $message_stmt->bind_param('iisi', $buyer_id, $receiver_id, $demo_message, $is_read);
                if (!$message_stmt->execute()) {
                    error_log("Message insertion failed: " . $message_stmt->error);
                    $message = "Error sending message: " . $message_stmt->error;
                    $payment_success = false;
                }
                $message_stmt->close();
            }
            $stmt->close();

            // Clear session data after successful order
            unset($_SESSION['gig_details']);
            $message = "Order placed successfully with $payment_method!";
        } else {
            error_log("Order insertion failed: " . $stmt->error);
            $message = "Error placing order: " . $stmt->error;
            $payment_success = false;
        }
    }
} else {
    $message = "Payment failed. Please try again.";
    $payment_success = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link rel="stylesheet" href="confirmation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include '../navbar/navbar.php'; ?>

    <main>
        <div class="container">
            <div class="confirmation-box">
                <?php if ($payment_success): ?>
                    <div class="payment-status success">
                        <div class="icon-container">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" />
                                <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8" />
                            </svg>
                        </div>
                        <h1>Payment Successful!</h1>
                        <p>Your transaction has been processed successfully</p>
                    </div>
                <?php else: ?>
                    <div class="payment-status failure">
                        <div class="icon-container">
                            <i class="fas fa-times" style="font-size: 2rem; color: #dc3545;"></i>
                        </div>
                        <h1>Payment Failed!</h1>
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>

                <div class="transaction-details">
                    <h2>Transaction Details</h2>
                    <div class="detail-row">
                        <span>Payment Method:</span>
                        <span id="payment-method"><?php echo htmlspecialchars($payment_method); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Transaction ID:</span>
                        <span id="transaction-id"><?php echo htmlspecialchars($transaction_id); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Amount:</span>
                        <span id="amount">Rs. <?php echo number_format($gig['gig_pricing']); ?> PKR</span>
                    </div>
                    <div class="detail-row">
                        <span>Date & Time:</span>
                        <span id="datetime"><?php echo htmlspecialchars($date_time); ?></span>
                    </div>
                </div>

                <div class="receipt-info">
                    <p>A receipt has been sent to your email address.</p>
                </div>

                <div class="action-buttons">
                    <a href="../index.php" class="btn secondary">Back to Home</a>
                    <?php if ($payment_success): ?>
                        <a href="/login/dashboard.php?page=orders" class="btn primary">View Orders</a>
                    <?php else: ?>
                        <a href="/payment/index.php" class="btn primary">Try Again</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include '../footer/footer.php'; ?>

    <script src="confirmation.js"></script>

    <!-- Receipt Modal -->
    <div id="receipt-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="close-btn" id="close-receipt">×</span>
            <h2>Payment Receipt</h2>
            <div class="receipt-details">
                <p><strong>Payment Method:</strong> <span id="receipt-method"><?php echo htmlspecialchars($payment_method); ?></span></p>
                <p><strong>Transaction ID:</strong> <span id="receipt-txid"><?php echo htmlspecialchars($transaction_id); ?></span></p>
                <p><strong>Amount:</strong> <span id="receipt-amount">Rs. <?php echo number_format($gig['gig_pricing']); ?> PKR</span></p>
                <p><strong>Date & Time:</strong> <span id="receipt-datetime"><?php echo htmlspecialchars($date_time); ?></span></p>
            </div>
            <div class="receipt-footer">
                <em>Thank you for your payment!</em>
            </div>
        </div>
    </div>

    <script>
        // Simple modal functionality (assuming confirmation.js handles this)
        document.getElementById('close-receipt').addEventListener('click', function() {
            document.getElementById('receipt-modal').style.display = 'none';
        });

        document.querySelector('.btn.primary').addEventListener('click', function(e) {
            if (!<?php echo json_encode($payment_success); ?>) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>