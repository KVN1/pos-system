<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/UserModel.php'; // adjust the path if needed
$userModel = new UserModel();

require_once '../models/SystemSettingsModel.php';
$settingsModel = new SystemSettingsModel();
$verificationCode = $settingsModel->getVerificationCode();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $code = htmlspecialchars(trim($_POST['code'] ?? ''));

    // Check if username field is empty
    if (empty($username)) {
        $_SESSION['forgot_error'] = "Please enter your username.";
        header("Location: forgotpass.php");
        exit;
    }

    // Check if username exists
    if (!$userModel->usernameExists($username)) {
        $_SESSION['forgot_error'] = "Username not found.";
        header("Location: forgotpass.php");
        exit;
    }

// Check if code matches the DB value
if ($code !== $verificationCode) {
    $_SESSION['forgot_error'] = "Invalid 6-digit code.";
    header("Location: forgotpass.php");
    exit;
}


    // Success: redirect to change password page
    $_SESSION['reset_username'] = $username;
    header("Location: change_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Infinite POS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('/POSu/images/login.jpg') no-repeat center center/cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .forgot-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 350px;
        }

        h3 {
            text-align: center;
            color: #333;
        }

        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-top: 10px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        input[type="submit"] {
            width: 100%;
            background: #88976C;
            color: white;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        input[type="submit"]:hover {
            background: #6b7b56;
        }

        .error {
            color: #D9534F;
            font-size: 14px;
            text-align: center;
            margin-bottom: 10px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #555;
            font-weight: bold;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <form action="forgotpass.php" method="POST">
            <h3>Forgot Password</h3>

            <?php if (isset($_SESSION['forgot_error'])): ?>
                <p class="error"><?= $_SESSION['forgot_error']; unset($_SESSION['forgot_error']); ?></p>
            <?php endif; ?>

            <label for="username">Enter Username:</label>
            <input type="text" name="username" id="username" required>

            <label for="code">Enter 6-digit Code:</label>
            <input type="number" name="code" id="code">

            <input type="submit" value="Next">
        </form>

        <a href="/POSu/user/login" class="back-link">Back to Login</a>
    </div>
</body>
</html>
