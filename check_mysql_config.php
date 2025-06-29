<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>MySQL Server Configuration Check</h2>";

// Include database connection
require_once 'config/database.php';

// Test database connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Get MySQL version
$result = $conn->query("SELECT VERSION() as version");
$version_info = $result->fetch_assoc();
echo "<p>MySQL Version: <strong>{$version_info['version']}</strong></p>";

// Get MySQL server variables
echo "<h3>MySQL Server Variables:</h3>";

$important_variables = [
    'max_allowed_packet',
    'max_connections',
    'wait_timeout',
    'interactive_timeout',
    'character_set_server',
    'collation_server',
    'innodb_buffer_pool_size',
    'innodb_log_file_size',
    'sql_mode'
];

echo "<table border='1'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

foreach ($important_variables as $variable) {
    $result = $conn->query("SHOW VARIABLES LIKE '{$variable}'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<tr><td>{$row['Variable_name']}</td><td>{$row['Value']}</td></tr>";
    }
}

echo "</table>";

// Check SQL mode
$result = $conn->query("SELECT @@sql_mode as sql_mode");
$sql_mode_info = $result->fetch_assoc();
echo "<p>SQL Mode: <strong>{$sql_mode_info['sql_mode']}</strong></p>";

// Check max_allowed_packet
$result = $conn->query("SELECT @@max_allowed_packet as max_allowed_packet");
$packet_info = $result->fetch_assoc();
$max_allowed_packet_mb = round($packet_info['max_allowed_packet'] / (1024 * 1024), 2);
echo "<p>Max Allowed Packet: <strong>{$max_allowed_packet_mb} MB</strong></p>";

// Check for any active locks
echo "<h3>Active Locks:</h3>";
$result = $conn->query("SHOW OPEN TABLES WHERE In_use > 0");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Database</th><th>Table</th><th>In_use</th><th>Name_locked</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Database']}</td>";
        echo "<td>{$row['Table']}</td>";
        echo "<td>{$row['In_use']}</td>";
        echo "<td>{$row['Name_locked']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No active locks found.</p>";
}

// Check for any active processes
echo "<h3>Active Processes:</h3>";
$result = $conn->query("SHOW PROCESSLIST");
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>User</th><th>Host</th><th>DB</th><th>Command</th><th>Time</th><th>State</th><th>Info</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Id']}</td>";
        echo "<td>{$row['User']}</td>";
        echo "<td>{$row['Host']}</td>";
        echo "<td>{$row['db']}</td>";
        echo "<td>{$row['Command']}</td>";
        echo "<td>{$row['Time']}</td>";
        echo "<td>{$row['State']}</td>";
        echo "<td>{$row['Info']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No active processes found.</p>";
}

// Check for any errors in the MySQL error log
echo "<h3>MySQL Error Log:</h3>";
echo "<p>Note: This may not be available depending on server configuration.</p>";

// Try to get the error log file location
$result = $conn->query("SHOW VARIABLES LIKE 'log_error'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<p>Error log file: <strong>{$row['Value']}</strong></p>";
    echo "<p>Please check this file for any relevant error messages.</p>";
} else {
    echo "<p>Could not determine error log file location.</p>";
}

// Check for any issues with the orders table
echo "<h3>Orders Table Check:</h3>";
$result = $conn->query("SHOW TABLES LIKE 'orders'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Orders table exists</p>";
    
    // Check table status
    $result = $conn->query("SHOW TABLE STATUS LIKE 'orders'");
    $table_status = $result->fetch_assoc();
    
    echo "<table border='1'>";
    echo "<tr><th>Property</th><th>Value</th></tr>";
    echo "<tr><td>Engine</td><td>{$table_status['Engine']}</td></tr>";
    echo "<tr><td>Row Format</td><td>{$table_status['Row_format']}</td></tr>";
    echo "<tr><td>Rows</td><td>{$table_status['Rows']}</td></tr>";
    echo "<tr><td>Avg Row Length</td><td>{$table_status['Avg_row_length']}</td></tr>";
    echo "<tr><td>Data Length</td><td>{$table_status['Data_length']}</td></tr>";
    echo "<tr><td>Max Data Length</td><td>{$table_status['Max_data_length']}</td></tr>";
    echo "<tr><td>Index Length</td><td>{$table_status['Index_length']}</td></tr>";
    echo "<tr><td>Auto Increment</td><td>{$table_status['Auto_increment']}</td></tr>";
    echo "<tr><td>Create Time</td><td>{$table_status['Create_time']}</td></tr>";
    echo "<tr><td>Update Time</td><td>{$table_status['Update_time']}</td></tr>";
    echo "<tr><td>Check Time</td><td>{$table_status['Check_time']}</td></tr>";
    echo "<tr><td>Collation</td><td>{$table_status['Collation']}</td></tr>";
    echo "<tr><td>Checksum</td><td>{$table_status['Checksum']}</td></tr>";
    echo "<tr><td>Create Options</td><td>{$table_status['Create_options']}</td></tr>";
    echo "<tr><td>Comment</td><td>{$table_status['Comment']}</td></tr>";
    echo "</table>";
    
    // Check for any issues with the table
    echo "<h4>Table Check:</h4>";
    $result = $conn->query("CHECK TABLE orders");
    $check_result = $result->fetch_assoc();
    
    if ($check_result['Msg_text'] === 'OK') {
        echo "<p style='color: green;'>✓ Table check: {$check_result['Msg_text']}</p>";
    } else {
        echo "<p style='color: red;'>✗ Table check: {$check_result['Msg_text']}</p>";
    }
    
    // Test a simple prepared statement
    echo "<h4>Testing Prepared Statement:</h4>";
    
    // Start transaction to avoid permanent changes
    $conn->begin_transaction();
    
    try {
        $stmt = $conn->prepare("INSERT INTO orders (client_id, freelancer_id, amount, description, delivery_time, status) VALUES (?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            echo "<p style='color: red;'>✗ Prepare failed: " . $conn->error . "</p>";
            
            // Try to identify the specific issue
            echo "<h4>Troubleshooting:</h4>";
            echo "<p>Checking for specific issues with the prepared statement...</p>";
            
            // Check if the table structure matches the prepared statement
            $result = $conn->query("DESCRIBE orders");
            $columns = [];
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row;
            }
            
            $required_columns = [
                'client_id' => 'i',
                'freelancer_id' => 'i',
                'amount' => 'd',
                'description' => 's',
                'delivery_time' => 'i',
                'status' => 's'
            ];
            
            $missing_columns = [];
            $incorrect_columns = [];
            
            foreach ($required_columns as $column => $type) {
                if (!isset($columns[$column])) {
                    $missing_columns[] = $column;
                } else {
                    $column_type = $columns[$column]['Type'];
                    $expected_type = '';
                    
                    switch ($type) {
                        case 'i':
                            $expected_type = 'int';
                            break;
                        case 'd':
                            $expected_type = 'decimal';
                            break;
                        case 's':
                            $expected_type = 'varchar|text';
                            break;
                    }
                    
                    if (!preg_match("/^{$expected_type}/i", $column_type)) {
                        $incorrect_columns[] = "$column (expected $expected_type, got $column_type)";
                    }
                }
            }
            
            if (!empty($missing_columns)) {
                echo "<p style='color: red;'>✗ Missing columns: " . implode(', ', $missing_columns) . "</p>";
            }
            
            if (!empty($incorrect_columns)) {
                echo "<p style='color: red;'>✗ Incorrect column types: " . implode(', ', $incorrect_columns) . "</p>";
            }
            
            if (empty($missing_columns) && empty($incorrect_columns)) {
                echo "<p>Table structure appears to be correct. The issue may be related to the MySQL server configuration or a bug in the PHP code.</p>";
            }
            
        } else {
            echo "<p style='color: green;'>✓ Prepare successful</p>";
            
            // Sample data
            $client_id = 1;
            $freelancer_id = 2;
            $amount = 100.50;
            $description = "Test order description";
            $delivery_time = 3;
            $status = "pending";
            
            echo "<p>Binding parameters: client_id=$client_id, freelancer_id=$freelancer_id, amount=$amount, description='$description', delivery_time=$delivery_time, status=$status</p>";
            
            $stmt->bind_param("iidssi", $client_id, $freelancer_id, $amount, $description, $delivery_time, $status);
            
            if ($stmt->execute()) {
                echo "<p style='color: green;'>✓ Execute successful</p>";
            } else {
                echo "<p style='color: red;'>✗ Execute failed: " . $stmt->error . "</p>";
            }
            
            $stmt->close();
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

echo "<h3>Recommendations:</h3>";
echo "<p>Based on the information above, here are some recommendations:</p>";
echo "<ul>";
echo "<li>If the orders table does not exist, run the <code>create_orders_table.php</code> script to create it.</li>";
echo "<li>If the orders table exists but has issues, run the <code>fix_orders_table.php</code> script to recreate it with the correct structure.</li>";
echo "<li>If the MySQL server configuration has issues, consult the MySQL documentation for recommended settings.</li>";
echo "<li>If the prepared statement fails, check the error message for specific issues.</li>";
echo "</ul>";
?>