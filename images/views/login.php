<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Infinite POS</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f4; /* Light gray for a modern touch */
        }

        .login-container {
            display: flex;
            width: 800px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Left Section - Logo & Greeting */
        .login-left {
            width: 50%;
            background: #222; /* Dark gray/black */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .login-left img {
            width: 150px;
            margin-bottom: 20px;
        }

        .login-left h2 {
            font-size: 22px;
            font-weight: bold;
        }

        /* Right Section - Login Form */
        .login-right {
            width: 50%;
            padding: 40px;
            text-align: center;
        }

        .login-right h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            font-size: 14px;
            font-weight: bold;
            color: #444;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            background: #f9f9f9;
        }

        .input-group input:focus {
            border-color: #222;
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #222; /* Black/Gray */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #444; /* Slightly lighter gray */
        }

        .error {
            color: #D9534F;
            margin-bottom: 10px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Section -->
        <div class="login-left">
            <img src="/images/logo.png" alt="Infinite POS Logo">
            <h2>Welcome to Infinite POS!</h2>
        </div>

        <!-- Right Section -->
        <div class="login-right">
            <h2>Login</h2>

            <?php if (!empty($_SESSION['error'])): ?>
                <p class="error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
            <?php endif; ?>

            <form action="/user/do_login" method="post">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
