<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    // Show login page
    public function show_login() {
        require_once 'views/login.php';
    }

    // Process login authentication
public function do_login() {
    echo "do_login() function reached.<br>"; // Debugging message
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo "POST request detected.<br>";
        
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $password = trim($_POST['password'] ?? '');

        echo "Username: $username<br>";
        echo "Password entered (hashed check skipped for now).<br>";

        $user = $this->userModel->checkCredentials($username, $password);
        if ($user) {
            echo "User found in database.<br>";

            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("refresh:2;url=/POSu/dashboard");
            exit;
        } else {
            echo "Invalid credentials. Redirecting back...";
            header("refresh:2;url=/POSu/user/login");
            exit;
        }
    } else {
        echo "Request method is not POST.";
    }
}





    public function dashboard() {
        if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
            header("Location: /POSu/user/login");
            exit;
        }
        
        require_once 'views/dashboard.php';
    }


    // Logout user
    public function logout() {
        session_unset(); // Clear session variables
        session_destroy(); // Destroy session
        header("Location: /POSu/user/login");
        exit;
    }
}
?>
