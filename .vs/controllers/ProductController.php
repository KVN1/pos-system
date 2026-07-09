<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/ProductModel.php'; // Absolute path to CategoryModel

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    public function products() {
        require_once 'views/products.php';
    }

    public function index() {
        return $this->productModel->getAllProducts();
    }

    public function getAllProducts() {
        return $this->productModel->getAllProducts(); // Fetch all products from DB
    }

    public function getCategories() {
        return $this->productModel->getCategories();
    }

    public function addProduct() {
        if ($_SERVER["REQUEST_METHOD"] === "POST" && $_POST["action"] === "add") {
            $code = $_POST["code"];
            $description = $_POST["description"];
            $category = $_POST["category"];
            $stock = $_POST["stock"];
            $buy_price = $_POST["buy_price"];
            $sell_price = $_POST["sell_price"];
            $date_added = $_POST["date_added"];

            $this->productModel->insertProduct($code, $description, $category, $stock, $buy_price, $sell_price, $date_added);
            header("Location:http://localhost/POSu/Products");
            exit();
        }
    }
}

$productController = new ProductController();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST["action"] === "add") {
        $productController->addProduct();
    }
}

if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $product = $productController->productModel->getProductBySearch($search);

    if ($product) {
        echo "{$product['code']}|{$product['description']}|{$product['sell_price']}";
    } else {
        echo "not_found";
    }
}
