<?php
require_once 'Database.php';

class ProductModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function searchProducts($keyword) {
        $query = "SELECT * FROM products WHERE name LIKE :keyword OR description LIKE :keyword OR code LIKE :keyword";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['keyword' => '%' . $keyword . '%']);
        return $stmt->fetchAll();
    }
}
?>
