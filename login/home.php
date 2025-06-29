<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once __DIR__ . '/../config/database.php';

// Get user information
$user_id = $_SESSION['user_id'];

// Get total chats (unique conversations)
$chat_query = "SELECT COUNT(DISTINCT CASE 
    WHEN sender_id = ? THEN receiver_id 
    ELSE sender_id 
END) as total_chats 
FROM messages 
WHERE sender_id = ? OR receiver_id = ?";
$chat_stmt = $conn->prepare($chat_query);
if ($chat_stmt === false) {
    die("Error preparing chat query: " . $conn->error);
}
$chat_stmt->bind_param("iii", $user_id, $user_id, $user_id);
$chat_stmt->execute();
$chat_result = $chat_stmt->get_result();
$total_chats = $chat_result->fetch_assoc()['total_chats'];

// Get total orders
$order_query = "SELECT COUNT(*) as total_orders FROM orders WHERE buyer_id = ?";
$order_stmt = $conn->prepare($order_query);
if ($order_stmt === false) {
    die("Error preparing order query: " . $conn->error);
}
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$total_orders = $order_result->fetch_assoc()['total_orders'];

// Get total earnings
$earnings_query = "SELECT COALESCE(SUM(price), 0) as total_earnings FROM orders WHERE seller_id = ?";
$earnings_stmt = $conn->prepare($earnings_query);
if ($earnings_stmt === false) {
    die("Error preparing earnings query: " . $conn->error);
}
$earnings_stmt->bind_param("i", $user_id);
$earnings_stmt->execute();
$earnings_result = $earnings_stmt->get_result();
$total_earnings = $earnings_result->fetch_assoc()['total_earnings'];

// Get total services - Fix the table name to match your database
$services_query = "SELECT COUNT(*) as total_services FROM gigs WHERE user_id = ?";
$services_stmt = $conn->prepare($services_query);
if ($services_stmt === false) {
    die("Error preparing services query: " . $conn->error);
}
$services_stmt->bind_param("i", $user_id);
$services_stmt->execute();
$services_result = $services_stmt->get_result();
$total_services = $services_result->fetch_assoc()['total_services'];
?>
<div class="welcome-section">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <div class="user-info">
            <span class="badge bg-primary">Online</span>
        </div>
    </div>
</div>

<?php if ($total_services == 0): ?>
<!-- No Services CTA -->
<div class="card bg-light border-0 mb-4">
    <div class="card-body text-center p-5">
        <div class="mb-4">
            <i class="fas fa-rocket fa-4x text-primary"></i>
        </div>
        <h3 class="card-title mb-3">Start Your Journey!</h3>
        <p class="card-text mb-4">You haven't created any services yet. Showcase your skills and start attracting clients by creating your first service.</p>
        <a href="dashboard.php?page=gig-creation" class="btn btn-primary btn-lg px-5">
            <i class="fas fa-plus-circle me-2"></i>Create Your First Service
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Dashboard Cards -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="icon-container">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="card-title">Total Chats</h5>
                <p class="card-text display-6"><?php echo $total_chats; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="icon-container">
                    <i class="fas fa-tasks"></i>
                </div>
                <h5 class="card-title">Total Orders</h5>
                <p class="card-text display-6"><?php echo $total_orders; ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="icon-container">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <h5 class="card-title">Total Earnings</h5>
                <p class="card-text display-6">PKR <?php echo number_format($total_earnings, 2); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title mb-4">Recent Activity</h5>
        <ul class="list-group list-group-flush">
            <?php
            // Get recent messages
            $recent_messages_query = "SELECT m.*, u.username 
                                    FROM messages m 
                                    JOIN users u ON (m.sender_id = u.id OR m.receiver_id = u.id) 
                                    WHERE (m.sender_id = ? OR m.receiver_id = ?) 
                                    AND u.id != ? 
                                    ORDER BY m.created_at DESC 
                                    LIMIT 3";
            $recent_messages_stmt = $conn->prepare($recent_messages_query);
            $recent_messages_stmt->bind_param("iii", $user_id, $user_id, $user_id);
            $recent_messages_stmt->execute();
            $recent_messages = $recent_messages_stmt->get_result();

            while ($message = $recent_messages->fetch_assoc()) {
                $time_ago = time_elapsed_string($message['created_at']);
                echo '<li class="list-group-item">';
                echo '<i class="fas fa-comment me-2 text-primary"></i> New message from ' . htmlspecialchars($message['username']);
                echo '<span class="float-end text-muted">' . $time_ago . '</span>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>
</div>

<?php
// Helper function to format time elapsed
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->d > 0) {
        return $diff->d . ' days ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hours ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minutes ago';
    } else {
        return 'just now';
    }
}
?>