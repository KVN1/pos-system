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
        $dashboardController->index();
        break;

    case "sales":
        if (!isset($_SESSION['role'])) {
            header("Location: /POSu/user/login");
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



    default:
        include "404.php";
        break;
}
