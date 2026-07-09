<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/ActivityLogController.php'; // Include activity log

// Handle GET request for username check
if (isset($_GET['url']) && $_GET['url'] === 'check_username') {
    $controller = new UserController();
    $controller->check_username();
    exit;
}



class UserController {
    private $userModel;
    private $activityLog;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->activityLog = new ActivityLogController(); // Instantiate activity log
    }

    // Show login page
    public function show_login() {
        require_once __DIR__ . '/../views/login.php';
    }

// Process login authentication
public function do_login() {
    if ($_SERVER["REQUEST_METHOD"] !== "POST") return;

    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    $user = $this->userModel->getUserByUsername($username);

    // Generic error message
    $genericError = "Invalid username or password.";

    if (!$user || $user['status'] !== 'Active') {
        $_SESSION['login_error'] = $genericError;
        header("Location: /user/login");
        exit;
    }

    // Check if account is currently locked
    // Check if account is currently locked
if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
    $remainingSeconds = strtotime($user['lock_until']) - time();
    
    $_SESSION['login_error'] = "Account locked. Try again in <span id='countdown'>$remainingSeconds</span> seconds.";
    $_SESSION['lock_remaining'] = $remainingSeconds; // store for JS

    header("Location: /user/login");
    exit;
}

// Check if account is currently locked
if (!empty($user['lock_until']) && strtotime($user['lock_until']) > time()) {
    $remainingSeconds = strtotime($user['lock_until']) - time();
    
    $_SESSION['login_error'] = "Account locked. Try again in <span id='countdown'>$remainingSeconds</span> seconds.";
    $_SESSION['lock_remaining'] = $remainingSeconds;

    header("Location: /user/login");
    exit;
}

// Check password
if (!password_verify($password, $user['password'])) {
    $failed = ($user['failed_attempts'] ?? 0) + 1;

    if ($failed >= 3) {
        // Lock account for 5 minutes
        $lockMinutes = 5;
        $lockUntil = date("Y-m-d H:i:s", strtotime("+$lockMinutes minutes"));
        $this->userModel->lockAccount($user['user_id'], $failed, $lockUntil);

        $_SESSION['login_error'] = "Too many failed attempts. Account locked for $lockMinutes minutes.";
    } else {
        $this->userModel->updateFailedAttempts($user['user_id'], $failed);
        $remaining = 3 - $failed;
        $_SESSION['login_error'] = "Incorrect username or password. $remaining attempt(s) left.";
    }

    header("Location: /user/login");
    exit;
}

// Successful login — reset failed attempts
$this->userModel->resetLoginAttempts($user['user_id']);


    // Set session
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    header("Location: /dashboard");
    exit;
}


    // Show registration page
    public function show_register() {
        require_once __DIR__ . '/../user/register';
    }

    // Process registration
// Process registration
public function do_register() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
        $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
        $username = htmlspecialchars(trim($_POST['username'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $password = $_POST['password'];
        $pattern = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/";

        // Required fields check
        if (empty($firstName) || empty($lastName) || empty($username) || empty($password) || empty($confirmPassword) || empty($role)) {
            $_SESSION['register_error'] = "All fields are required.";
            header("Location: /index.php?url=user/register");
            exit;
        }

        // Role validation
        if (!in_array($role, ['admin', 'cashier'])) {
            $_SESSION['register_error'] = "Invalid role selected.";
            header("Location: /index.php?url=user/register");
            exit;
        }

        // Admin Secret Key check
        if ($role === 'admin') { 
            $adminKey = trim($_POST['admin_key'] ?? '');
            if ($adminKey !== '112345') {
                $_SESSION['register_error'] = "Invalid Admin Secret Key.";
                header("Location: /index.php?url=user/register");
                exit;
            }
        }

        if ($this->userModel->usernameExists($username)) {
                $_SESSION['register_error'] = "Username already exists. Please choose another.";
                header("Location: /index.php?url=user/register");
                exit;
            }


        // Password matching
        if ($password !== $confirmPassword) {
            $_SESSION['register_error'] = "Passwords do not match.";
            header("Location: /index.php?url=user/register");
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Register user
        if ($this->userModel->registerUser($firstName, $lastName, $username, $hashedPassword, $role)) {
            $_SESSION['success'] = "Registration successful. Please log in.";
            header("Location: /index.php?url=user/login");
            exit;
        } else {
            $_SESSION['register_error'] = "Something went wrong. Please try again.";
            header("Location: /index.php?url=user/register");
            exit;
        }
         }

        if (!preg_match($pattern, $password)) {
            $_SESSION['register_error'] = "Password must be at least 8 characters long and include uppercase, lowercase, number, and symbol.";
            header("Location: /user/register");
            exit();
        }


}


    // Dashboard
    public function dashboard() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }
        require_once __DIR__ . '/../views/dashboard.php';
    }

    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: /user/login");
        exit;
    }

    // Show forgot password page
    public function show_forgot_password() {
        require_once __DIR__ . '/../user/forgotpass';
    }

    // Handle forgot password
    public function do_forgot_password() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = htmlspecialchars(trim($_POST['username'] ?? ''));
            $code = htmlspecialchars(trim($_POST['code'] ?? ''));

            if (empty($username)) {
                $_SESSION['forgot_error'] = "Please enter your username.";
                header("Location: /user/forgotpass");
                exit;
            }

            $user = $this->userModel->getUserByUsername($username);

            if (!$user) {
                $_SESSION['forgot_error'] = "Username not found.";
                header("Location: /user/forgotpass");
                exit;
            }

            if ($code !== '112345') {
                $_SESSION['forgot_error'] = "Invalid verification code.";
                header("Location: /user/forgotpass");
                exit;
            }

            $_SESSION['reset_username'] = $username;
            header("Location: /views/change_password.php");
            exit;
        }
    }

    // Activate user account
    public function activate_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            if ($userId && $this->userModel->updateUserStatus($userId, 'Active')) {
                // Log the action
                if (isset($_SESSION['user_id'])) {
                    $this->activityLog->addLog($_SESSION['user_id'], 'Activate User', "Activated user ID: $userId");
                }

                $_SESSION['message'] = "User activated successfully.";
            } else {
                $_SESSION['error'] = "Failed to activate user.";
            }
            header("Location: /views/settings.php");
            exit;
        }
    }

public function check_username() {
    // Get username from GET request
    $username = trim($_GET['username'] ?? '');
    
    // Check if it exists
    $exists = $this->userModel->usernameExists($username);
    
    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode(['exists' => $exists]);
    exit;
}



    // Deactivate user account
    public function deactivate_user() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id'] ?? null;
            if ($userId && $this->userModel->updateUserStatus($userId, 'Inactive')) {
                // Log the action
                if (isset($_SESSION['user_id'])) {
                    $this->activityLog->addLog($_SESSION['user_id'], 'Deactivate User', "Deactivated user ID: $userId");
                }

                $_SESSION['message'] = "User deactivated successfully.";
            } else {
                $_SESSION['error'] = "Failed to deactivate user.";
            }
            header("Location: /views/settings.php");
            exit;
        }
    }
} // <-- end of class

// -----------------------------
// Router code (must be OUTSIDE class)
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $controller = new UserController();

    switch ($_POST['action']) {
        case 'deactivate_user':
            $controller->deactivate_user();
            break;
        case 'activate_user':
            $controller->activate_user();
            break;
        case 'do_login':
            $controller->do_login();
            break;
        case 'do_register':
            $controller->do_register();
            break;
        case 'do_forgot_password':
            $controller->do_forgot_password();
            break;

        case 'check_username':
    $controller->check_username();
    break;

        default:
            header("Location: /views/settings.php");
            exit;
    }
}
?>
