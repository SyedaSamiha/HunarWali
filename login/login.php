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
    $stmt = $conn->prepare("SELECT id, username, password, role, gender FROM users WHERE email = ?");  // Added 'gender' field to the query
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch user data
        $stmt->bind_result($user_id, $username, $hashed_password, $role, $gender);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Password is correct, store user data in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['gender'] = $gender;

            // Redirect based on user role
            if ($role == 'freelancer') {
                header("Location: http://localhost/login/dashboard.php");
            } elseif ($role == 'client') {
                header("Location: http://localhost/client-panel/index.php");
            } elseif ($role == 'admin') {
                header("Location: http://localhost/dashboard/admin.php");
            } else {
                // Fallback redirection if role is not recognized
                header("Location: http://localhost/index.php");
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