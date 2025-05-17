<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Remove duplicate session start since it's already started in dashboard.php
// session_start();

// Remove duplicate session cookie path setting
// ini_set('session.cookie_path', '/');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit();
}

// Include database connection
require_once('../config/database.php');

// Debug log
error_log("Process Gig Update - POST data: " . print_r($_POST, true));
error_log("Process Gig Update - FILES data: " . print_r($_FILES, true));

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Process Gig Update - Not a POST request");
    echo "<script>window.location.href = 'dashboard.php?page=my-services';</script>";
    exit();
}

// Validate required fields
$required_fields = ['gig_id', 'gig_title', 'service_id', 'sub_service_id', 'gig_description', 'gig_pricing'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty($_POST[$field])) {
        error_log("Process Gig Update - Missing required field: " . $field);
        echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $_POST['gig_id'] . "&error=missing_fields';</script>";
        exit();
    }
}

$gig_id = $_POST['gig_id'];
$user_id = $_SESSION['user_id'];
$gig_title = $_POST['gig_title'];
$service_id = $_POST['service_id'];
$sub_service_id = $_POST['sub_service_id'];
$gig_description = $_POST['gig_description'];
$gig_pricing = $_POST['gig_pricing'];

// Debug log
error_log("Process Gig Update - Processing gig ID: " . $gig_id . " for user: " . $user_id);

// Verify that the gig belongs to the user
$verify_query = "SELECT * FROM gigs WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($verify_query);
$stmt->bind_param("ii", $gig_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("Process Gig Update - Gig not found or doesn't belong to user");
    echo "<script>window.location.href = 'dashboard.php?page=my-services';</script>";
    exit();
}

// Handle image upload if a new image was provided
$image_path = null;
if (isset($_FILES['gig_image']) && $_FILES['gig_image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['gig_image'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        error_log("Process Gig Update - Invalid file type: " . $file['type']);
        echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $gig_id . "&error=invalid_file_type';</script>";
        exit();
    }
    
    // Validate that it's actually an image
    if (!getimagesize($file['tmp_name'])) {
        error_log("Process Gig Update - File is not a valid image");
        echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $gig_id . "&error=not_an_image';</script>";
        exit();
    }
    
    // Create upload directory if it doesn't exist
    $upload_dir = '../uploads/gigs/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            error_log("Process Gig Update - Failed to create upload directory");
            echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $gig_id . "&error=dir_creation_failed';</script>";
            exit();
        }
    }
    
    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        error_log("Process Gig Update - Upload directory is not writable");
        echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $gig_id . "&error=dir_not_writable';</script>";
        exit();
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        error_log("Process Gig Update - Failed to move uploaded file");
        echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $gig_id . "&error=upload_failed';</script>";
        exit();
    }
    
    $image_path = 'uploads/gigs/' . $filename;
    error_log("Process Gig Update - New image uploaded: " . $image_path);
}

// Update the gig in the database
if ($image_path) {
    // Update with new image
    $update_query = "UPDATE gigs SET 
        gig_title = ?, 
        service_id = ?, 
        sub_service_id = ?, 
        gig_description = ?, 
        gig_pricing = ?, 
        image = ? 
        WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("siisdsii", $gig_title, $service_id, $sub_service_id, $gig_description, $gig_pricing, $image_path, $gig_id, $user_id);
} else {
    // Update without changing image
    $update_query = "UPDATE gigs SET 
        gig_title = ?, 
        service_id = ?, 
        sub_service_id = ?, 
        gig_description = ?, 
        gig_pricing = ? 
        WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("siisdii", $gig_title, $service_id, $sub_service_id, $gig_description, $gig_pricing, $gig_id, $user_id);
}

// Debug log before update
error_log("Process Gig Update - Executing update query: " . $update_query);
error_log("Process Gig Update - Parameters: " . print_r([$gig_title, $service_id, $sub_service_id, $gig_description, $gig_pricing, $gig_id, $user_id], true));

if ($stmt->execute()) {
    error_log("Process Gig Update - Update successful");
    echo "<script>window.location.href = 'dashboard.php?page=my-services';</script>";
} else {
    error_log("Process Gig Update - Update failed: " . $stmt->error);
    echo "<script>window.location.href = 'dashboard.php?page=gig-edit&id=" . $gig_id . "&error=update_failed';</script>";
}
exit(); 