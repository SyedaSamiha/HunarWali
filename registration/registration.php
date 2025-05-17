<?php
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
        die('Error uploading front of ID card.');
    }
    if (!in_array($_FILES['id_front']['type'], $allowedTypes)) {
        die('Front ID card must be an image (jpg, jpeg, png, gif).');
    }
    if ($_FILES['id_front']['size'] > $maxFileSize) {
        die('Front ID card image is too large (max 2MB).');
    }
    $frontExt = pathinfo($_FILES['id_front']['name'], PATHINFO_EXTENSION);
    $frontFileName = uniqid('front_', true) . '.' . $frontExt;
    $frontPath = 'uploads/' . $frontFileName;
    if (!move_uploaded_file($_FILES['id_front']['tmp_name'], $uploadDir . $frontFileName)) {
        die('Failed to save front of ID card.');
    }

    // Handle back ID
    if (!isset($_FILES['id_back']) || $_FILES['id_back']['error'] !== UPLOAD_ERR_OK) {
        die('Error uploading back of ID card.');
    }
    if (!in_array($_FILES['id_back']['type'], $allowedTypes)) {
        die('Back ID card must be an image (jpg, jpeg, png, gif).');
    }
    if ($_FILES['id_back']['size'] > $maxFileSize) {
        die('Back ID card image is too large (max 2MB).');
    }
    $backExt = pathinfo($_FILES['id_back']['name'], PATHINFO_EXTENSION);
    $backFileName = uniqid('back_', true) . '.' . $backExt;
    $backPath = 'uploads/' . $backFileName;
    if (!move_uploaded_file($_FILES['id_back']['tmp_name'], $uploadDir . $backFileName)) {
        die('Failed to save back of ID card.');
    }

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($role) || empty($gender)) {
        die("Please fill all fields.");
    }

    // Password mismatch check
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    if($role == "freelancer" && $gender == "male"){
        die("Sorry, we don't support male freelancers at the moment.");
    }

    // Password strength check
    if (strlen($password) < 8) {
        die("Password must be at least 8 characters long.");
    }

    // Email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // DB Connection
    $host = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "freelance_website";

    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check for existing email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        die("Email is already registered.");
    }

    // Hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, gender, id_card_front, id_card_back) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $username, $email, $hashed, $role, $gender, $frontPath, $backPath);

    if ($stmt->execute()) {
        header("Location:/Login/index.php" );
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
