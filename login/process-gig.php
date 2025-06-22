<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Ensure session cookie path is set to root
ini_set('session.cookie_path', '/');

// Debug: Check if user_id is set
if (!isset($_SESSION['user_id'])) {
    error_log("process-gig.php: user_id not set in session. Session data: " . print_r($_SESSION, true));
    header("Location: login.php");
    exit();
}

require_once('../config/database.php');

// Check database connection
if (!$conn) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $gig_title = isset($_POST['gig_title']) ? trim($_POST['gig_title']) : '';
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $sub_service_id = isset($_POST['sub_service_id']) ? (int)$_POST['sub_service_id'] : null;
    $gig_description = isset($_POST['gig_description']) ? trim($_POST['gig_description']) : '';
    $gig_pricing = isset($_POST['gig_pricing']) ? trim($_POST['gig_pricing']) : '';

    // Basic validation
    if (empty($gig_title) || empty($service_id) || empty($gig_description) || empty($gig_pricing)) {
        header("Location: gig-creation.php?error=missing_fields");
        exit();
    }

    // Check if image was uploaded
    if (!isset($_FILES['gig_image']) || $_FILES['gig_image']['error'] !== UPLOAD_ERR_OK) {
        header("Location: gig-creation.php?error=image_required");
        exit();
    }

    // Handle image upload
    $image_path = null;
    if (isset($_FILES['gig_image']) && $_FILES['gig_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/gigs/';
        
        // Debug: Check if directory exists or can be created
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                error_log("process-gig.php: Failed to create directory: $upload_dir");
                header("Location: gig-creation.php?error=dir_creation_failed");
                exit();
            }
        }
        
        // Debug: Check if directory is writable
        if (!is_writable($upload_dir)) {
            error_log("process-gig.php: Directory not writable: $upload_dir");
            header("Location: gig-creation.php?error=dir_not_writable");
            exit();
        }
        
        $file_extension = strtolower(pathinfo($_FILES['gig_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_extension, $allowed_extensions)) {
            header("Location: gig-creation.php?error=invalid_file_type");
            exit();
        }
        
        $new_filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        $check = getimagesize($_FILES['gig_image']['tmp_name']);
        if ($check === false) {
            header("Location: gig-creation.php?error=not_an_image");
            exit();
        }
        
        // Debug: Check if temporary file exists
        if (!file_exists($_FILES['gig_image']['tmp_name'])) {
            error_log("process-gig.php: Temporary file does not exist: " . $_FILES['gig_image']['tmp_name']);
            header("Location: gig-creation.php?error=temp_file_missing");
            exit();
        }
        
        if (!move_uploaded_file($_FILES['gig_image']['tmp_name'], $upload_path)) {
            error_log("process-gig.php: Failed to move uploaded file to: $upload_path");
            header("Location: gig-creation.php?error=upload_failed");
            exit();
        }
        
        $image_path = 'uploads/gigs/' . $new_filename;
    }

    // Insert gig into database
    $query = "INSERT INTO gigs (user_id, service_id, sub_service_id, gig_title, gig_description, gig_pricing, gig_images, created_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiissss", $user_id, $service_id, $sub_service_id, $gig_title, $gig_description, $gig_pricing, $image_path);

    if ($stmt->execute()) {
        header("Location: dashboard.php?page=my-services&success=1");
        exit();
    } else {
        header("Location: gig-creation.php?error=database_error");
        exit();
    }
} else {
    header("Location: gig-creation.php");
    exit();
}
?>