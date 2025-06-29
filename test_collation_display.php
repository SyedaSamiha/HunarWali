<?php
// Test script to verify collation display logic
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Collation Display Test</h2>";

// Test cases for collation values
$test_cases = [
    'utf8mb4_general_ci',  // Normal case with underscore
    'utf8mb4',             // No underscore
    null,                  // Null value
    '',                    // Empty string
    'latin1_swedish_ci',   // Multiple underscores
    'binary'               // Another case without underscore
];

echo "<table border='1'>";
echo "<tr><th>Original Collation</th><th>Extracted Character Set</th></tr>";

foreach ($test_cases as $collation) {
    echo "<tr>";
    echo "<td>" . ($collation ?? 'NULL') . "</td>";
    
    // Original logic
    $original_result = $collation ? explode('_', $collation)[0] : 'N/A';
    
    // Fixed logic
    $fixed_result = $collation ? (strpos($collation, '_') !== false ? explode('_', $collation)[0] : $collation) : 'N/A';
    
    echo "<td>Original: " . $original_result . "<br>Fixed: " . $fixed_result . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Explanation of the Fix:</h3>";
echo "<p>The original code <code>explode('_', \$collation)[0]</code> would cause an error if the collation string doesn't contain an underscore.</p>";
echo "<p>The fixed code first checks if an underscore exists with <code>strpos(\$collation, '_') !== false</code> before attempting to explode the string.</p>";
echo "<p>If no underscore is found, it returns the entire collation string as the character set.</p>";
?>