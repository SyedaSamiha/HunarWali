<?php
require_once 'config/database.php';

// Test function to check if messages can be sent and retrieved
function testMessagesSystem($conn) {
    echo "<h2>Testing Messages System</h2>";
    
    // First, let's find valid user IDs from the database
    $user_query = "SELECT id FROM users LIMIT 2";
    $user_result = $conn->query($user_query);
    
    if ($user_result->num_rows < 2) {
        echo "<p style='color:red'>Error: Need at least 2 users in the database for testing</p>";
        return false;
    }
    
    $users = $user_result->fetch_all(MYSQLI_ASSOC);
    $test_sender_id = $users[0]['id'];
    $test_receiver_id = $users[1]['id'];
    
    echo "<p>Using existing users with IDs: $test_sender_id and $test_receiver_id</p>";
    $test_message = "This is a test message sent at " . date('Y-m-d H:i:s');
    
    echo "<p>Attempting to send a message from user $test_sender_id to user $test_receiver_id</p>";
    
    // Insert a test message
    $insert_query = "INSERT INTO messages (sender_id, receiver_id, message, created_at) 
                     VALUES (?, ?, ?, NOW())";
    $insert_stmt = $conn->prepare($insert_query);
    
    if (!$insert_stmt) {
        echo "<p style='color:red'>Error preparing statement: " . $conn->error . "</p>";
        return false;
    }
    
    $insert_stmt->bind_param('iis', $test_sender_id, $test_receiver_id, $test_message);
    
    if (!$insert_stmt->execute()) {
        echo "<p style='color:red'>Error executing statement: " . $insert_stmt->error . "</p>";
        return false;
    }
    
    $message_id = $conn->insert_id;
    echo "<p style='color:green'>Message inserted successfully with ID: $message_id</p>";
    
    // Retrieve the message to verify
    $select_query = "SELECT * FROM messages WHERE id = ?";
    $select_stmt = $conn->prepare($select_query);
    
    if (!$select_stmt) {
        echo "<p style='color:red'>Error preparing select statement: " . $conn->error . "</p>";
        return false;
    }
    
    $select_stmt->bind_param('i', $message_id);
    
    if (!$select_stmt->execute()) {
        echo "<p style='color:red'>Error executing select statement: " . $select_stmt->error . "</p>";
        return false;
    }
    
    $result = $select_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $message = $result->fetch_assoc();
        echo "<p style='color:green'>Message retrieved successfully:</p>";
        echo "<pre>" . print_r($message, true) . "</pre>";
        
        // Check if messages exist between these users
        $check_query = "SELECT * FROM messages WHERE 
                       (sender_id = ? AND receiver_id = ?) OR 
                       (sender_id = ? AND receiver_id = ?)";
        $check_stmt = $conn->prepare($check_query);
        
        if (!$check_stmt) {
            echo "<p style='color:red'>Error preparing check statement: " . $conn->error . "</p>";
        } else {
            $check_stmt->bind_param('iiii', $test_sender_id, $test_receiver_id, $test_receiver_id, $test_sender_id);
            
            if (!$check_stmt->execute()) {
                echo "<p style='color:red'>Error executing check statement: " . $check_stmt->error . "</p>";
            } else {
                $result = $check_stmt->get_result();
                
                echo "<p>Found " . $result->num_rows . " messages between these users</p>";
                
                if ($result->num_rows > 0) {
                    echo "<table border='1'>";
                    echo "<tr><th>ID</th><th>Sender</th><th>Receiver</th><th>Message</th><th>Created At</th></tr>";
                    
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['sender_id'] . "</td>";
                        echo "<td>" . $row['receiver_id'] . "</td>";
                        echo "<td>" . $row['message'] . "</td>";
                        echo "<td>" . $row['created_at'] . "</td>";
                        echo "</tr>";
                    }
                    
                    echo "</table>";
                }
            }
        }
        
        return true;
    } else {
        echo "<p style='color:red'>Failed to retrieve the message</p>";
        return false;
    }
}

// Run the test
testMessagesSystem($conn);
?>