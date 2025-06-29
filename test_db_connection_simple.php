<?php
// Simple database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'freelance_website';

echo "<h2>Simple Database Connection Test</h2>";

try {
    // Try to connect without selecting a database first
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>Connected to MySQL server successfully!</p>";
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE '$database'");
    if ($result->num_rows > 0) {
        echo "<p>Database '$database' exists.</p>";
        
        // Now try to select the database
        if ($conn->select_db($database)) {
            echo "<p>Successfully selected database '$database'.</p>";
            
            // Check if orders table exists
            $result = $conn->query("SHOW TABLES LIKE 'orders'");
            if ($result->num_rows > 0) {
                echo "<p>Table 'orders' exists.</p>";
                
                // Show table structure
                $result = $conn->query("DESCRIBE orders");
                echo "<h3>Table Structure:</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['Field'] . "</td>";
                    echo "<td>" . $row['Type'] . "</td>";
                    echo "<td>" . $row['Null'] . "</td>";
                    echo "<td>" . $row['Key'] . "</td>";
                    echo "<td>" . $row['Default'] . "</td>";
                    echo "<td>" . $row['Extra'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Table 'orders' does not exist.</p>";
            }
        } else {
            echo "<p>Failed to select database '$database'. Error: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Database '$database' does not exist.</p>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>