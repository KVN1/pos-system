<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/database.php'; // Absolute path to database connection

class CategoryModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Get all categories
    public function getCategories() {
        $sql = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add a new category
    public function addCategory($name) {
        $sql = "INSERT INTO categories (category_name) VALUES (:name)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['name' => $name]);
    }

    // Edit a category
    public function updateCategory($id, $name) {
        $sql = "UPDATE categories SET category_name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'name' => $name]);
    }

    // Delete a category
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
