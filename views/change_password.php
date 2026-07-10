<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../models/UserModel.php';

$userModel = new UserModel();

if (!isset($_SESSION['reset_username'])) {
    header("Location: forgotpass.php");
    exit;
}

$username = $_SESSION['reset_username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($newPassword) || empty($confirmPassword)) {
        $error = "Please fill in all fields.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        if ($userModel->updatePassword($username, $newPassword)) {
            unset($_SESSION['reset_username']);
            $_SESSION['success'] = "Password updated successfully.";
            header("Location: login.php");
            exit;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        input[type=password] {
            width: 100%;
            padding: 8px;
            margin: 6px 0 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type=submit] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        input[type=submit]:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<form method="POST">
    <h3 style="text-align:center;">Change Password</h3>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <label>New Password:</label>
    <input type="password" name="new_password" required>

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required>

    <input type="submit" value="Update Password">
</form>

</body>
</html>
