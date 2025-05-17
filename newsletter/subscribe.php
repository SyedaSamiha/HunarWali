<?php
// Simple file-based storage for demonstration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit;
    }

    // Save to a file (emails.txt)
    $file = 'emails.txt';
    // Prevent duplicate emails
    $existing = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (in_array($email, $existing)) {
        echo "You are already subscribed!";
        exit;
    }
    file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    echo "Thank you for subscribing!";
}
?>
