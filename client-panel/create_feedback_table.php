<?php
require_once '../config/database.php';

try {
    // Create feedback table if it doesn't exist
    $createTableQuery = "CREATE TABLE IF NOT EXISTS feedback (
        id INT PRIMARY KEY AUTO_INCREMENT,
        order_id INT NOT NULL,
        rating INT NOT NULL,
        review TEXT NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME,
        FOREIGN KEY (order_id) REFERENCES orders(id)
    )";

    if ($conn->query($createTableQuery)) {
        echo "Feedback table created successfully or already exists.";
    } else {
        throw new Exception("Error creating feedback table: " . $conn->error);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 
 