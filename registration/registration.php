<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $role = $_POST["role"];
    $gender = $_POST["gender"];

    // File upload handling
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Handle front ID
    if (!isset($_FILES['id_front']) || $_FILES['id_front']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = 'Error uploading front of ID card.';
        header("Location: index.php");
        exit();
    }
    if (!in_array($_FILES['id_front']['type'], $allowedTypes)) {
        $_SESSION['message'] = 'Front ID card must be an image (jpg, jpeg, png, gif).';
        header("Location: index.php");
        exit();
    }
    if ($_FILES['id_front']['size'] > $maxFileSize) {
        $_SESSION['message'] = 'Front ID card image is too large (max 2MB).';
        header("Location: index.php");
        exit();
    }
    $frontExt = pathinfo($_FILES['id_front']['name'], PATHINFO_EXTENSION);
    $frontFileName = uniqid('front_', true) . '.' . $frontExt;
    $frontPath = 'uploads/' . $frontFileName;
    if (!move_uploaded_file($_FILES['id_front']['tmp_name'], $uploadDir . $frontFileName)) {
        $_SESSION['message'] = 'Failed to save front of ID card.';
        header("Location: index.php");
        exit();
    }

    // Handle back ID
    if (!isset($_FILES['id_back']) || $_FILES['id_back']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = 'Error uploading back of ID card.';
        header("Location: index.php");
        exit();
    }
    if (!in_array($_FILES['id_back']['type'], $allowedTypes)) {
        $_SESSION['message'] = 'Back ID card must be an image (jpg, jpeg, png, gif).';
        header("Location: index.php");
        exit();
    }
    if ($_FILES['id_back']['size'] > $maxFileSize) {
        $_SESSION['message'] = 'Back ID card image is too large (max 2MB).';
        header("Location: index.php");
        exit();
    }
    $backExt = pathinfo($_FILES['id_back']['name'], PATHINFO_EXTENSION);
    $backFileName = uniqid('back_', true) . '.' . $backExt;
    $backPath = 'uploads/' . $backFileName;
    if (!move_uploaded_file($_FILES['id_back']['tmp_name'], $uploadDir . $backFileName)) {
        $_SESSION['message'] = 'Failed to save back of ID card.';
        header("Location: index.php");
        exit();
    }

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role) || empty($gender)) {
        $_SESSION['message'] = "Please fill all fields.";
        header("Location: index.php");
        exit();
    }

    // Password mismatch check
    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        header("Location: index.php");
        exit();
    }

    if($role == "freelancer" && $gender == "male"){
        $_SESSION['message'] = "Sorry, we don't support male freelancers at the moment.";
        header("Location: index.php");
        exit();
    }

    // Password strength check
    if (strlen($password) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        header("Location: index.php");
        exit();
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        header("Location: index.php");
        exit();
    }

    // DB Connection
    $host = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "freelance_website";

    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        $_SESSION['message'] = "Connection failed: " . $conn->connect_error;
        header("Location: index.php");
        exit();
    }

    // Check for existing email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['message'] = "Email already registered.";
        header("Location: index.php");
        exit();
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, gender, id_card_front, id_card_back, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending Approval')");
    $stmt->bind_param("sssssss", $username, $email, $hashed, $role, $gender, $frontPath, $backPath);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! Please wait for admin approval before logging in.";
        header("Location:/Login/index.php" );
        exit();
    } else {
        $_SESSION['message'] = "Error: " . $stmt->error;
        header("Location: index.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "Invalid request.";
    header("Location: index.php");
    exit();
}
?>
