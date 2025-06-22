<?php
// Simple test script to verify user details functionality
require_once '../config/database.php';

echo "<h2>Testing User Details Functionality</h2>";

// Get a sample user with ID card images
$query = "SELECT id, username, email, role, gender, status, id_card_front, id_card_back FROM users WHERE id_card_front IS NOT NULL OR id_card_back IS NOT NULL LIMIT 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo "<h3>Found user with ID cards:</h3>";
    echo "<ul>";
    echo "<li>ID: " . $user['id'] . "</li>";
    echo "<li>Username: " . $user['username'] . "</li>";
    echo "<li>Email: " . $user['email'] . "</li>";
    echo "<li>Role: " . $user['role'] . "</li>";
    echo "<li>Status: " . $user['status'] . "</li>";
    echo "<li>Front ID Card: " . ($user['id_card_front'] ? $user['id_card_front'] : 'Not uploaded') . "</li>";
    echo "<li>Back ID Card: " . ($user['id_card_back'] ? $user['id_card_back'] : 'Not uploaded') . "</li>";
    echo "</ul>";
    
    echo "<h3>Test Links:</h3>";
    echo "<p><a href='view_user_details.php?id=" . $user['id'] . "' target='_blank'>View User Details Page</a></p>";
    echo "<p><a href='admin.php' target='_blank'>Admin Dashboard</a></p>";
    
    // Test image paths
    if ($user['id_card_front']) {
        $front_path = "../" . $user['id_card_front'];
        echo "<h3>Front ID Card Image Test:</h3>";
        if (file_exists($front_path)) {
            echo "<p>✅ Front ID card image exists at: " . $front_path . "</p>";
            echo "<img src='" . $front_path . "' alt='Front ID Card' style='max-width: 200px; border: 1px solid #ccc;'>";
        } else {
            echo "<p>❌ Front ID card image not found at: " . $front_path . "</p>";
        }
    }
    
    if ($user['id_card_back']) {
        $back_path = "../" . $user['id_card_back'];
        echo "<h3>Back ID Card Image Test:</h3>";
        if (file_exists($back_path)) {
            echo "<p>✅ Back ID card image exists at: " . $back_path . "</p>";
            echo "<img src='" . $back_path . "' alt='Back ID Card' style='max-width: 200px; border: 1px solid #ccc;'>";
        } else {
            echo "<p>❌ Back ID card image not found at: " . $back_path . "</p>";
        }
    }
    
} else {
    echo "<p>No users found with ID card images.</p>";
    
    // Show all users
    $all_users = $conn->query("SELECT id, username, email, role, status FROM users LIMIT 5");
    echo "<h3>Sample Users:</h3>";
    echo "<ul>";
    while ($user = $all_users->fetch_assoc()) {
        echo "<li>ID: " . $user['id'] . " - " . $user['username'] . " (" . $user['email'] . ") - " . $user['role'] . " - " . $user['status'] . "</li>";
    }
    echo "</ul>";
}

$conn->close();
?> 