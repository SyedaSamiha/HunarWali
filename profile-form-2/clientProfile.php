<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /Login/login.php");
    exit();
}

// Include database connection
include('db_connection.php');

// Fetch the logged-in user's role from the session
$user_id = $_SESSION['user_id'];
$query = "SELECT role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

// Check if the user is a client
if ($role !== 'client') {
    echo "<script>alert('Only clients can access this form.'); window.location.href='profile.php';</script>";
    exit();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $display_name = mysqli_real_escape_string($conn, $_POST['display_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Default profile picture
    $profile_picture = "default.jpg"; // Set default profile picture

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture = $_FILES['profile_picture']['name']; // Use the uploaded file's name
        $upload_dir = 'uploads/';
        $upload_file = $upload_dir . basename($profile_picture);

        // Move the uploaded file to the designated directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_file)) {
            echo "Profile picture uploaded successfully.<br>";
        } else {
            echo "Error uploading the profile picture.<br>";
        }
    } else {
        // If no file was uploaded, keep the default picture
        echo "No profile picture uploaded. Using default image.<br>";
    }

    // Insert data into the client_profiles table
    $query = "INSERT INTO client_profiles (user_id, first_name, last_name, display_name, profile_picture, description)
              VALUES ('$user_id', '$first_name', '$last_name', '$display_name', '$profile_picture', '$description')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Profile created successfully.'); window.location.href='success.php';</script>";
    } else {
        echo "<script>alert('Error creating profile: " . mysqli_error($conn) . "'); window.location.href='clientform.php';</script>";
    }
}
?>
