<?php
require_once 'ProductModel.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    // Fetch all products
    public function fetchProducts() {
        $products = $this->productModel->getAllProducts();
        // Output HTML
        foreach ($products as $product) {
            echo "<div class='product-item'>
                    <span class='product-name'>{$product['name']}</span>
                    <span class='product-price'>{$product['sell_price']}</span>
                  </div>";
        }
    }

    // Search products by keyword
    public function searchProducts($keyword) {
        $products = $this->productModel->searchProducts($keyword);
        // Output HTML
        foreach ($products as $product) {
            echo "<div class='product-item'>
                    <span class='product-name'>{$product['name']}</span>
                    <span class='product-price'>{$product['sell_price']}</span>
                  </div>";
        }
    }
}

// Handle request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $controller = new ProductController();

    // Fetch all products
    if ($_GET['action'] === 'fetchProducts') {
        $controller->fetchProducts();
    }

    // Search products by keyword
    elseif ($_GET['action'] === 'searchProducts' && isset($_GET['query'])) {
        $controller->searchProducts($_GET['query']);
    }


}
if (isset($_POST['setDiscount'])) {
    $discountName = $_POST['setDiscount'];
    $discount = $discountModel->getDiscountByName($discountName);
    $_SESSION['discount'] = $discount['percentage'];
    $_SESSION['discountName'] = $discount['name'];
}


?>
