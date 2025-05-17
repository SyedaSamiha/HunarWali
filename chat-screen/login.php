<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    if (!empty($username)) {
        $_SESSION['username'] = $username;
        header('Location: chat-screen.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - P2P Chat</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f2f5;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            color: #075e54;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        input:focus {
            border-color: #075e54;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #075e54;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #128c7e;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>P2P Chat Login</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       placeholder="Enter your username">
            </div>
            <button type="submit">Start Chatting</button>
        </form>
    </div>
</body>
</html> 