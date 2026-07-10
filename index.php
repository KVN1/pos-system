<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Parse and sanitize URL
$url = isset($_GET["url"]) 
    ? explode("/", filter_var(trim($_GET["url"], "/"), FILTER_SANITIZE_URL)) 
    : ["dashboard"];

// Load core controllers
require_once "controllers/UserController.php";
require_once "controllers/SalesController.php";
require_once "controllers/ProductController.php";
require_once "controllers/CategoryController.php";
require_once "controllers/DashboardController.php";
require_once "controllers/SalesReportController.php";
require_once "controllers/DiscountController.php";

// Instantiate controllers
$userController      = new UserController();
$salesController     = new SalesController();
$productController   = new ProductController();
$categoryController  = new CategoryController();
$dashboardController = new DashboardController();
$discountController  = new DiscountController();

// Auth check helper
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /user/login");
        exit;
    }
}

// Routing
switch (strtolower($url[0])) {

    case "user":
        if (isset($url[1])) {
            switch (strtolower($url[1])) {
                case "login":    $userController->show_login(); break;
                case "do_login": $userController->do_login(); break;
                case "register": $userController->show_register(); break;
                case "do_register": $userController->do_register(); break;
                case "check_username": $userController->check_username(); break;
                case "logout":   $userController->logout(); break;
                case "forgotpass":
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $userController->do_forgot_password();
                    } else {
                        $userController->show_forgot_password();
                    }
                    break;
                case "handle":
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        if (isset($_POST['activate'])) $userController->activate_user();
                        elseif (isset($_POST['deactivate'])) $userController->deactivate_user();
                    }
                    break;
                default: include "404.php"; break;
            }
        }
        break;

    case "dashboard":
        requireLogin();
        $dashboardController->index();
        break;

    case "add-sales-page":
        requireLogin();
        // Handle both GET (show page) and POST (process sale)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceedSale'])) {
            $salesController->processSale();
        } else {
            $salesController->addSalesPage();
        }
        break;

    case "products":
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            // Route to correct product method based on action
            switch ($_POST['action']) {
                case 'add':           $productController->addProduct(); break;
                case 'edit':          $productController->editProduct(); break;
                case 'delete':        $productController->deleteProduct(); break;
                case 'restore':       $productController->restoreProduct(); break;
                case 'reorder':       $productController->reorderStock(); break;
                case 'edit_batch':    $productController->editBatch(); break;
                case 'delete_batch':  $productController->archiveBatch(); break;
                case 'restore_batch': $productController->restoreBatch(); break;
                case 'return_damaged': $productController->returnDamagedItem(); break;
                default:              $productController->products(); break;
            }
        } elseif (isset($url[1])) {
            switch (strtolower($url[1])) {
                case 'add':    $productController->addProduct(); break;
                case 'delete': $productController->deleteProduct(); break;
                default:       include "404.php"; break;
            }
        } else {
            $productController->products();
        }
        break;

    case "categories":
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add':        $categoryController->create(); break;
                    case 'edit':       $categoryController->update(); break;
                    case 'delete':     $categoryController->deactivate(); break;
                    case 'deactivate': $categoryController->deactivate(); break;
                    case 'restore':    $categoryController->restore(); break;
                    default:           $categoryController->categories(); break;
                }
            } else {
                $categoryController->categories();
            }
        } else {
            $categoryController->categories();
        }
        break;

    case "sales":
        requireLogin();
        $salesController->salesReport();
        break;

    case "sales-report":
        requireLogin();
        $salesReportController = new SalesReportController();
        $salesReportController->index();
        break;

    case "discounts":
        requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $discountController->update();
        } else {
            $discounts = $discountController->index();
        }
        break;

    case "notifications":
        requireLogin();
        include "views/notifications.php";
        break;

    case "settings":
        requireLogin();
        require_once "models/SystemSettingsModel.php";
        $settingsModel = new SystemSettingsModel();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_code'])) {
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
            } elseif (isset($_POST['action'])) {
                // Handle product/category actions from settings page
                switch ($_POST['action']) {
                    case 'add':
                    case 'edit':
                    case 'delete':
                    case 'restore':
                        $productController->addProduct();
                        break;
                }
            }
        }
        include "views/settings.php";
        break;

    case "expenses":
        requireLogin();
        include "views/expenses.php";
        break;

    case "activity":
        requireLogin();
        include "views/Activity.php";
        break;

    case "usermanual":
        requireLogin();
        include "views/usermanual.php";
        break;

    case "admin":
        requireLogin();
        include "views/admin.php";
        break;

    case "return-item":
        requireLogin();
        include "views/return_item.php";
        break;

    case "return-sale":
        requireLogin();
        include "views/return_sale.php";
        break;

    case "search-expired":
        requireLogin();
        include "views/search-expired.php";
        break;

    case "change-password":
        requireLogin();
        include "views/change_password.php";
        break;

    case "checkout":
        requireLogin();
        $salesController->checkout();
        break;

    case "export-sales":
        requireLogin();
        include "views/exportSales.php";
        break;

    default:
        if (empty($url[0]) || $url[0] === '') {
            header("Location: /user/login");
            exit;
        }
        include "404.php";
        break;
}
