#!/bin/bash

# Script to run all test files in sequence
echo "Running all test scripts..."
echo "============================="

# Set the path to PHP executable
PHP_BIN="/Applications/XAMPP/xamppfiles/bin/php"

# Set the path to the htdocs directory
HTDOCS_DIR="/Applications/XAMPP/xamppfiles/htdocs"

# Function to run a test script and display a separator
run_test() {
    echo "\n\n============================="
    echo "Running $1..."
    echo "============================="
    "$PHP_BIN" "$HTDOCS_DIR/$1"
    echo "\n============================="
    echo "Finished $1"
    echo "============================="
}

# Run each test script
run_test "test_fix_plain.php"
run_test "test_custom_order_status_fix.php"
run_test "test_all_fixes.php"

echo "\n\nAll tests completed!"