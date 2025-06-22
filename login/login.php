<?php
session_start();  // Start the session

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Database connection
    $host = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "freelance_website";  // Database name
    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);  // Stop execution if DB error
    }

    // Prepare the SQL query to check if the email exists
    $stmt = $conn->prepare("SELECT id, username, password, role, gender, status FROM users WHERE email = ?");  // Added 'status' field to the query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch user data
        $stmt->bind_result($user_id, $username, $hashed_password, $role, $gender, $status);
        $stmt->fetch();

        if ($status === 'Blocked') {
            $_SESSION['message'] = "Your account has been blocked. Please contact support.";
            header("Location: http://localhost/Login/index.php");
            exit();
        }

        if ($status === 'Pending Approval') {
            $_SESSION['message'] = "Your account is pending approval. Please wait for admin verification.";
            header("Location: http://localhost/Login/index.php");
            exit();
        }

        if (password_verify($password, $hashed_password)) {
            // Password is correct, store user data in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['gender'] = $gender;

            // Redirect based on user role
            if ($role == 'admin') {
                header("Location: http://localhost/dashboard/admin.php");
            } else {
                // Both client and freelancer users go to main homepage
                header("Location: http://localhost/");
            }
            exit();
        } else {
            $_SESSION['message'] = "Incorrect password!";
            header("Location: http://localhost/Login/index.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Email not registered!";
        header("Location: http://localhost/Login/index.php");
        exit();
    }

    // Close DB connection
    $stmt->close();
    $conn->close();
}
?>