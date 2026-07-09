<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';

class ProductModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Enable PDO exceptions for better error handling
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    // Fetch all products
    public function getAllProducts() {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY date_added DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return [];
        }
    }

    // Insert a new product
public function insertProduct($code, $description, $category, $stock, $unit, $buy_price, $sell_price, $date_added, $expiry) {
    try {
        $stmt = $this->conn->prepare("INSERT INTO products (code, description, category, stock, unit, buy_price, sell_price, date_added, expiry) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$code, $description, $category, $stock, $unit, $buy_price, $sell_price, $date_added, $expiry]);
    } catch (PDOException $e) {
        error_log("Error inserting product: " . $e->getMessage());
        return false;  // Return false on error
    }
}

    // Get all product categories
    public function getCategories() {
        try {
            $query = "SELECT category_name FROM categories"; // Fetch categories correctly
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];  // Return an empty array on error
        }
    }

    // Search for a product by code or description
    public function getProductBySearch($search) {
        try {
            $stmt = $this->conn->prepare("SELECT code, description, sell_price FROM products WHERE code = ? OR description LIKE ?");
            $stmt->execute([$search, "%" . $search . "%"]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching for product: " . $e->getMessage());
            return null;  // Return null if an error occurs
        }
    }

    // Delete a product by ID
    public function deleteProduct($id) {
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;  // Return false if deletion fails
        }
    }

    // Update product details
    public function updateProduct($id, $code, $description, $category, $stock, $unit, $buy_price, $sell_price, $expiry) {
        try {
            $query = "UPDATE products SET code = ?, description = ?, category = ?, stock = ?, unit = ?, buy_price = ?, sell_price = ?, expiry = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$code, $description, $category, $stock, $unit, $buy_price, $sell_price, $expiry, $id]);
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;  // Return false if the update fails
        }
    }

    // Get stock quantity for a product by code
    public function getStockByProductCode($product_code) {
        try {
            $stmt = $this->conn->prepare("SELECT stock FROM products WHERE code = ?");
            $stmt->execute([$product_code]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ? $product['stock'] : 0; // Return 0 if product not found
        } catch (PDOException $e) {
            error_log("Error fetching stock for product: " . $e->getMessage());
            return 0;  // Return 0 in case of error
        }
    }

    // Update product stock
    public function updateProductStock($product_code, $new_stock) {
        try {
            $stmt = $this->conn->prepare("UPDATE products SET stock = ? WHERE code = ?");
            return $stmt->execute([$new_stock, $product_code]);
        } catch (PDOException $e) {
            error_log("Error updating product stock: " . $e->getMessage());
            return false;  // Return false on error
        }
    }
}
?>
