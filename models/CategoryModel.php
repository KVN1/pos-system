<?php
require_once __DIR__ . '/../database.php';

class CategoryModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get only active categories
    public function getActiveCategories() {
        $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all categories including inactive (optional for archive view)
    public function getAllCategories() {
        $sql = "SELECT * FROM categories ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new category
    public function addCategory($name) {
        $sql = "INSERT INTO categories (category_name, status) VALUES (:name, 'active')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['name' => $name]);
    }

    // Edit a category
public function updateCategory($id, $name) {

    // 1. Get OLD name first
    $sqlOld = "SELECT category_name FROM categories WHERE id = :id";
    $stmtOld = $this->conn->prepare($sqlOld);
    $stmtOld->execute(['id' => $id]);
    $oldName = $stmtOld->fetchColumn();

    // 2. Update category table
    $sql = "UPDATE categories SET category_name = :name WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute(['id' => $id, 'name' => $name]);

    // 3. Update products using old category name
    $sql2 = "UPDATE products SET category = :name WHERE category = :oldName";
    $stmt2 = $this->conn->prepare($sql2);
    $stmt2->execute(['name' => $name, 'oldName' => $oldName]);

    return true;
}


    // Deactivate a category
    public function deactivateCategory($id) {
        $sql = "UPDATE categories SET status = 'inactive' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Restore a category
    public function restoreCategory($id) {
        $sql = "UPDATE categories SET status = 'active' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Get only deactivated categories
    public function getDeactivatedCategories() {
        $sql = "SELECT * FROM categories WHERE status = 'inactive' ORDER BY category_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function getProductsByCategoryName($categoryName) {
    try {
        $stmt = $this->conn->prepare("
            SELECT * FROM products 
            WHERE TRIM(LOWER(category)) = TRIM(LOWER(?)) 
              AND status = 'active'
            ORDER BY description ASC
        ");
        $stmt->execute([$categoryName]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Products for [$categoryName]: " . print_r($products, true));
        return $products;
    } catch (PDOException $e) {
        error_log("Error fetching products by category: " . $e->getMessage());
        return [];
    }
}


}
?>
