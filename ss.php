<?php
session_start();  // Start session to access session data

// Check if the user is logged in (i.e., session variables are set)
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!<br>";
    header("Location: /HunarWalii/homepage/index.php");  // Using relative path from web root
    exit();  // Stop further execution
} else {
    // If logged in, display session data
    echo "User logged in!<br>";
    echo "Session Data:<br>";
    var_dump($_SESSION);  // Print the session variables (user_id, username, role)

    // Display the username of the logged-in user
    echo "Welcome, " . $_SESSION['username'];  // Display username
}
?>
