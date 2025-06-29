<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Collation and Character Set Check</h2>";

// Include database connection
require_once 'config/database.php';

// Test database connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Get database name from connection
$result = $conn->query("SELECT DATABASE() as db");
$db_info = $result->fetch_assoc();
$database_name = $db_info['db'];

echo "<p>Current database: <strong>{$database_name}</strong></p>";

// Check database character set and collation
$result = $conn->query("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
                       FROM information_schema.SCHEMATA 
                       WHERE SCHEMA_NAME = '{$database_name}'");
$db_charset_info = $result->fetch_assoc();

echo "<h3>Database Character Set and Collation:</h3>";
echo "<ul>";
echo "<li>Character Set: <strong>{$db_charset_info['DEFAULT_CHARACTER_SET_NAME']}</strong></li>";
echo "<li>Collation: <strong>{$db_charset_info['DEFAULT_COLLATION_NAME']}</strong></li>";
echo "</ul>";

// Check tables character set and collation
echo "<h3>Tables Character Set and Collation:</h3>";
$result = $conn->query("SHOW TABLES");

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Table</th><th>Character Set</th><th>Collation</th></tr>";
    
    while ($row = $result->fetch_row()) {
        $table_name = $row[0];
        $table_info_result = $conn->query("SHOW TABLE STATUS WHERE Name = '{$table_name}'");
        $table_info = $table_info_result->fetch_assoc();
        
        echo "<tr>";
        echo "<td>{$table_name}</td>";
        echo "<td>{$table_info['Collation'] ? (strpos($table_info['Collation'], '_') !== false ? explode('_', $table_info['Collation'])[0] : $table_info['Collation']) : 'N/A'}</td>";
        echo "<td>{$table_info['Collation']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No tables found in the database.</p>";
}

// Check specifically for orders table
echo "<h3>Orders Table Check:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Orders table exists</p>";
    
    // Check orders table structure
    $result = $conn->query("DESCRIBE orders");
    echo "<h4>Orders Table Structure:</h4>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check orders table columns character set and collation
    $result = $conn->query("SELECT COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME 
                           FROM information_schema.COLUMNS 
                           WHERE TABLE_SCHEMA = '{$database_name}' 
                           AND TABLE_NAME = 'orders' 
                           AND CHARACTER_SET_NAME IS NOT NULL");
    
    if ($result->num_rows > 0) {
        echo "<h4>Orders Table Text Columns Character Set and Collation:</h4>";
        echo "<table border='1'>";
        echo "<tr><th>Column</th><th>Character Set</th><th>Collation</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['COLUMN_NAME']}</td>";
            echo "<td>{$row['CHARACTER_SET_NAME']}</td>";
            echo "<td>{$row['COLLATION_NAME']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p>No text columns found in the orders table.</p>";
    }
    
    // Check for any potential issues with the orders table
    echo "<h4>Potential Issues Check:</h4>";
    $issues_found = false;
    
    // Check if all required columns exist
    $required_columns = ['client_id', 'freelancer_id', 'amount', 'description', 'delivery_time', 'status'];
    $missing_columns = [];
    
    $result = $conn->query("DESCRIBE orders");
    $existing_columns = [];
    while ($row = $result->fetch_assoc()) {
        $existing_columns[] = $row['Field'];
    }
    
    foreach ($required_columns as $column) {
        if (!in_array($column, $existing_columns)) {
            $missing_columns[] = $column;
        }
    }
    
    if (!empty($missing_columns)) {
        $issues_found = true;
        echo "<p style='color: red;'>✗ Missing required columns: " . implode(', ', $missing_columns) . "</p>";
    } else {
        echo "<p style='color: green;'>✓ All required columns exist</p>";
    }
    
    // Check if the table has any data
    $result = $conn->query("SELECT COUNT(*) as count FROM orders");
    $count_info = $result->fetch_assoc();
    echo "<p>Number of records in orders table: <strong>{$count_info['count']}</strong></p>";
    
    // Test a simple INSERT statement
    echo "<h4>Testing Simple INSERT Statement:</h4>";
    
    // Start transaction to avoid permanent changes
    $conn->begin_transaction();
    
    try {
        $result = $conn->query("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status) 
                              VALUES (1, 2, 100.50, 'Test order', 3, 'pending')");
        
        if ($result) {
            echo "<p style='color: green;'>✓ Simple INSERT successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Simple INSERT failed: " . $conn->error . "</p>";
        }
        
        // Rollback to avoid saving test data
        $conn->rollback();
        echo "<p>Transaction rolled back - test data not saved</p>";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>✗ Orders table does not exist</p>";
}

// Close the connection
$conn->close();
?>