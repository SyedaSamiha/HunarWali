<?php
error_reporting(E_ALL); // Report all types of errors
ini_set('display_errors', 1); // Display errors on the page

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /Login/login.php");
    exit();
}

// Include database connection
include('db_connection.php');

// Fetch the logged-in user's gender from the session
$gender = $_SESSION['gender'];

// Check if the user has selected "Freelancer" and if they are female
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Form submitted<br>"; // Debugging line

    if ($gender !== "female") {
        // Restrict non-female users from registering as a freelancer
        echo "<script>alert('Only female users can register as a freelancer.'); window.location.href='freelancerProfile.php';</script>";
        exit();
    }

    // Get form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $display_name = mysqli_real_escape_string($conn, $_POST['display_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $profession = mysqli_real_escape_string($conn, $_POST['profession']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $user_id = $_SESSION['user_id'];

    // Handle profile picture upload
    $profile_picture = "default.jpg"; // Default profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = $_FILES['profile_picture']['name'];
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($profile_picture);

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
            echo "Profile picture uploaded successfully.<br>";
        } else {
            echo "Error uploading the profile picture.<br>";
        }
    } else {
        echo "No file uploaded or file error.<br>";
    }

    // SQL Insert Query
    $query = "INSERT INTO freelancer_profiles (user_id, first_name, last_name, display_name, profile_picture, description, profession, skills)
              VALUES ('$user_id', '$first_name', '$last_name', '$display_name', '$profile_picture', '$description', '$profession', '$skills')";

    echo "SQL Query: $query<br>"; // Debugging line

    if (mysqli_query($conn, $query)) {
        // Redirect to success page after successful form submission
        header("Location: success.php");
        exit(); // Stop further execution
    } else {
        echo "<script>alert('Error creating profile.'); window.location.href='freelancerProfile.php';</script>";
    }
}
?>
