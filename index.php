<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Parse and sanitize URL
$url = isset($_GET["url"]) 
    ? explode("/", filter_var(trim($_GET["url"], "/"), FILTER_SANITIZE_URL)) 
    : ["dashboard"];

// Load controllers
require_once "controllers/UserController.php";
require_once "controllers/SalesController.php";
require_once "controllers/ProductController.php";
require_once "controllers/CategoryController.php";
require_once "controllers/DashboardController.php";
require_once "controllers/SalesReportController.php";
require_once "controllers/DiscountController.php"; // Added for Discounts

// Instantiate controllers
$userController = new UserController();
$salesController = new SalesController();
$productController = new ProductController();
$categoryController = new CategoryController();
$dashboardController = new DashboardController();
$discountController = new DiscountController(); // Instantiate DiscountController

// Routing
switch (strtolower($url[0])) {
    case "user":
        if (isset($url[1])) {
            switch (strtolower($url[1])) {
                case "login":
                    $userController->show_login();
                    break;
                case "do_login":
                    $userController->do_login();
                    break;
                case "register":
                    $userController->show_register(); // <-- add this
                    break;
                case "do_register":
                    $userController->do_register();
                    break;
                    case "check_username":
    $userController->check_username();
    break;

                case "logout":
                    $userController->logout();
                    break;
                default:
                    include "404.php";
                    break;
            }
        }
        break;

    case "add-sales-page":
        $salesController->addSalesPage();
        break;

    case "products":
        if (isset($url[1])) {
            switch (strtolower($url[1])) {
                case "add":
                    $productController->addProduct();
                    break;
                case "delete":
                    $productController->deleteProduct();
                    break;
                default:
                    include "404.php";
                    break;
            }
        } else {
            $productController->products();
        }
        break;

    case "categories":
        $categoryController->categories();
        break;

    case "dashboard":
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['role'])) {
            header("Location: /user/login");
            exit;
        }
        $dashboardController->index();
        break;

    case "sales":
        if (!isset($_SESSION['role'])) {
            header("Location: /user/login");
            exit;
        }
        $salesController->salesReport();
        break;

    case "sales-report":
        $salesReportController = new SalesReportController();
        $salesReportController->index();
        break;

case "discounts":
    require_once "controllers/DiscountController.php";
    $discountController = new DiscountController();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $discountController->update();
    } else {
        // optional: load discounts if you want a dedicated page
        $discounts = $discountController->index();
    }
    break;



    case "notifications":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        require_once "controllers/ActivityLogController.php";
        include "views/notifications.php";
        break;

    case "settings":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        require_once "models/SystemSettingsModel.php";
        $settingsModel = new SystemSettingsModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_code'])) {
            $newCode = trim($_POST['verification_code']);
            if (preg_match('/^\d{6}$/', $newCode)) {
                $settingsModel->updateVerificationCode($newCode);
                $_SESSION['flash_message'] = "System verification code updated!";
                $_SESSION['flash_type'] = "success";
            } else {
                $_SESSION['flash_message'] = "Invalid code. Must be 6 digits.";
                $_SESSION['flash_type'] = "error";
            }
            header("Location: /settings");
            exit;
        }
        include "views/settings.php";
        break;

    case "expenses":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/expenses.php";
        break;

    case "activity":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/Activity.php";
        break;

    case "usermanual":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/usermanual.php";
        break;

    case "admin":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/admin.php";
        break;

    case "return-item":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        require_once "controllers/SalesController.php";
        include "views/return_item.php";
        break;

    case "return-sale":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/return_sale.php";
        break;

    case "search-expired":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/search-expired.php";
        break;

    case "change-password":
        if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
        include "views/change_password.php";
        break;

    case "checkout":
        $salesController->checkout();
        break;

    case "export-sales":
        include "views/exportSales.php";
        break;

        case "backup":
    if (!isset($_SESSION['user_id'])) { header("Location: /user/login"); exit; }
    include "backup.php";
    break;

    default:
        if (empty($url[0]) || $url[0] === '') {
            header("Location: /user/login");
            exit;
        }
        include "404.php";
        break;
}
