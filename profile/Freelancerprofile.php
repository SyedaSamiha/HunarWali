<?php
session_start(); // Start the session to manage login state

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if user is not logged in
    exit();
}

// Database connection
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "freelance_website"; // Your database name
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch freelancer profile data
$profile_query = "SELECT * FROM freelancer_profiles WHERE user_id = ?";
$profile_stmt = $conn->prepare($profile_query);
$profile_stmt->bind_param("i", $user_id);
$profile_stmt->execute();
$profile_result = $profile_stmt->get_result();

// Check if profile data exists
if ($profile_result && $profile_result->num_rows > 0) {
    $profile_data = $profile_result->fetch_assoc();
} else {
    $profile_data = null; // If no profile is found, set to null
    echo "Profile data not found."; // Debugging output
}

// Fetch gig data related to freelancer
$gig_query = "SELECT * FROM gigs WHERE freelancer_id = ?";
$gig_stmt = $conn->prepare($gig_query);
$gig_stmt->bind_param("i", $user_id);
$gig_stmt->execute();
$gig_result = $gig_stmt->get_result();

// Check if gig data exists
if ($gig_result && $gig_result->num_rows > 0) {
    $gig_count = $gig_result->num_rows;
} else {
    $gig_count = 0; // If no gigs are found, set gig count to 0
    echo "No gigs found."; // Debugging output
}

// Debugging: Check if there is any data
echo "<pre>";
var_dump($profile_data);  // Check profile data
var_dump($gig_result);    // Check gig result
echo "</pre>";

// Close the database connections
$profile_stmt->close();
$gig_stmt->close();
$conn->close();

// Include the HTML layout and pass the profile and gigs data
include('index.html');
?>
