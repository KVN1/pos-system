
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$url = isset($_GET["url"]) ? explode("/", filter_var(trim($_GET["url"], "/"), FILTER_SANITIZE_URL)) : ["dashboard"];

require_once "controllers/UserController.php";
require_once "controllers/SalesController.php";
require_once "controllers/ProductController.php";
require_once "controllers/CategoryController.php";


$userController = new UserController();
$salesController = new SalesController();
$productController = new ProductController();
$categoryController = new CategoryController();

if ($url[0] == "user" && isset($url[1]) && $url[1] == "login") {
    $userController->show_login();
} 
else if ($url[0] == "add-sales") {
    $salesController->addSales();
}

else if ($url[0] == "products" && isset($url[1]) && $url[1] == "add") {
    $productController->addProduct();
}
else if ($url[0] == "products" && isset($url[1]) && $url[1] == "delete") {
    $productController->deleteProduct();
}


else if ($url[0] == "Products") {
    $productController->products();
} 
else if ($url[0] == "Categories") {
    $categoryController->categories();
}  
else if ($url[0] == "user" && isset($url[1]) && $url[1] == "do_login") {
    $userController->do_login();
} 
else if ($url[0] == "user" && isset($url[1]) && $url[1] == "logout") {
    $userController->logout();
} 
else if ($url[0] == "dashboard") {
    if (!isset($_SESSION['role'])) {
        header("Location: /POSu/user/login");
        exit;
    }
    $userController->dashboard();
}


// Sales Routes (Only Admin Can View)
else if ($url[0] == "sales") {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: /POSu/user/login");
        exit;
    }

    $salesController->index(); // Use an existing method like this if you have it
}



// 404 Page (For undefined routes)
else {
    include "404.php";
    exit;
}
?>
