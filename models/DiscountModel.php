<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/database.php';

class DiscountModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getAllDiscounts() {
        $stmt = $this->conn->prepare("SELECT * FROM discounts WHERE status='active'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDiscountByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM discounts WHERE name = :name LIMIT 1");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDiscount($id, $percentage) {
        $stmt = $this->conn->prepare("UPDATE discounts SET percentage = :percentage WHERE id = :id");
        return $stmt->execute(['percentage' => $percentage, 'id' => $id]);
    }
}
