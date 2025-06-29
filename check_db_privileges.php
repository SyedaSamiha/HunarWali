<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database User Privileges Check</h2>";

// Include database connection
require_once 'config/database.php';

// Test database connection
if ($conn->ping()) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    exit;
}

// Get current user and host
$result = $conn->query("SELECT CURRENT_USER() as user");
$user_info = $result->fetch_assoc();
echo "<p>Current user: <strong>{$user_info['user']}</strong></p>";

// Check user privileges
echo "<h3>User Privileges:</h3>";
$result = $conn->query("SHOW GRANTS FOR CURRENT_USER()");
echo "<ul>";
while ($row = $result->fetch_row()) {
    echo "<li>" . htmlspecialchars($row[0]) . "</li>";
}
echo "</ul>";

// Check if user has INSERT privilege on the database
echo "<h3>Specific Privileges Check:</h3>";

// Get database name from connection
$result = $conn->query("SELECT DATABASE() as db");
$db_info = $result->fetch_assoc();
$database_name = $db_info['db'];

echo "<p>Current database: <strong>{$database_name}</strong></p>";

// Check specific privileges
$privileges_to_check = ['SELECT', 'INSERT', 'UPDATE', 'DELETE', 'CREATE', 'ALTER', 'DROP'];

echo "<table border='1'>";
echo "<tr><th>Privilege</th><th>Status</th></tr>";

foreach ($privileges_to_check as $privilege) {
    $result = $conn->query("SELECT IF(COUNT(*) > 0, 'Yes', 'No') as has_privilege 
                          FROM information_schema.user_privileges 
                          WHERE grantee = CONCAT('\'', SUBSTRING_INDEX(CURRENT_USER(),'@',1), '\'@\'', SUBSTRING_INDEX(CURRENT_USER(),'@',-1), '\'') 
                          AND privilege_type = '{$privilege}'");
    $privilege_info = $result->fetch_assoc();
    $status = $privilege_info['has_privilege'] === 'Yes' ? 'color: green' : 'color: red';
    echo "<tr><td>{$privilege}</td><td style='{$status}'>{$privilege_info['has_privilege']}</td></tr>";
}

echo "</table>";

// Test creating a temporary table to verify CREATE privilege
echo "<h3>Testing CREATE Privilege:</h3>";
try {
    $conn->query("CREATE TEMPORARY TABLE test_privileges (id INT)");
    echo "<p style='color: green;'>✓ Successfully created temporary table</p>";
    $conn->query("DROP TEMPORARY TABLE test_privileges");
    echo "<p style='color: green;'>✓ Successfully dropped temporary table</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Failed to create temporary table: " . $e->getMessage() . "</p>";
}

// Test INSERT privilege
echo "<h3>Testing INSERT Privilege:</h3>";
try {
    // Create a temporary table
    $conn->query("CREATE TEMPORARY TABLE test_insert (id INT, name VARCHAR(50))");
    
    // Try to insert data
    $result = $conn->query("INSERT INTO test_insert VALUES (1, 'Test')");
    if ($result) {
        echo "<p style='color: green;'>✓ Successfully inserted data into temporary table</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to insert data: " . $conn->error . "</p>";
    }
    
    // Drop the temporary table
    $conn->query("DROP TEMPORARY TABLE test_insert");
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

// Check MySQL version
$result = $conn->query("SELECT VERSION() as version");
$version_info = $result->fetch_assoc();
echo "<p>MySQL Version: <strong>{$version_info['version']}</strong></p>";

// Close the connection
$conn->close();
?>