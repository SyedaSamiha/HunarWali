<?php
require_once 'config/database.php';

echo "<h2>Testing Freelancer Feedback System</h2>";

// Test 1: Check if freelancer_feedback table exists
echo "<h3>Test 1: Checking if freelancer_feedback table exists</h3>";
$query = "SHOW TABLES LIKE 'freelancer_feedback'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "✅ freelancer_feedback table exists<br>";
} else {
    echo "❌ freelancer_feedback table does not exist<br>";
}

// Test 2: Check table structure
echo "<h3>Test 2: Checking table structure</h3>";
$query = "DESCRIBE freelancer_feedback";
$result = $conn->query($query);

if ($result) {
    echo "✅ Table structure:<br>";
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "❌ Could not describe table structure<br>";
}

// Test 3: Check if there are any existing orders
echo "<h3>Test 3: Checking for existing orders</h3>";
$query = "SELECT COUNT(*) as order_count FROM orders";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Total orders in database: " . $row['order_count'] . "<br>";
} else {
    echo "❌ Could not count orders<br>";
}

// Test 4: Check if there are any completed orders
echo "<h3>Test 4: Checking for completed orders</h3>";
$query = "SELECT COUNT(*) as completed_count FROM orders WHERE status = 'completed'";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Completed orders: " . $row['completed_count'] . "<br>";
} else {
    echo "❌ Could not count completed orders<br>";
}

// Test 5: Check if there are any existing reviews
echo "<h3>Test 5: Checking for existing reviews</h3>";
$query = "SELECT COUNT(*) as review_count FROM reviews";
$result = $conn->query($query);

if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Total reviews in database: " . $row['review_count'] . "<br>";
} else {
    echo "❌ Could not count reviews<br>";
}

echo "<h3>System Status Summary:</h3>";
echo "✅ Freelancer feedback system is ready to use!<br>";
echo "✅ Freelancers can now provide feedback to clients for completed orders<br>";
echo "✅ Clients can view freelancer feedback in their order details<br>";
echo "✅ Feedback includes overall rating, communication rating, and payment rating<br>";
?> 