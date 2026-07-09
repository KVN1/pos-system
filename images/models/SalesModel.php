<?php
require_once 'database.php';

class SalesModel {
    private $db;

    public function __construct() {
        $database = new Database(); // Create Database instance
        $this->db = $database->getConnection(); // Get PDO connection
    }

    // Get all sales
    public function getAllSales() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM sales");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);  // Return as associative array
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return [];
        }
    }

    // Get a sale by ID
    public function getSaleById($id) {
        $sql = "SELECT * FROM sales WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);  // Return sale data as associative array
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return null;
        }
    }

    // Add a sale to the database
    public function addSale($user_id, $total_amount, $payment_method) {
        $sql = "INSERT INTO sales (user_id, total_amount, payment_method, created_at) VALUES (:user_id, :total_amount, :payment_method, NOW())";
        $stmt = $this->db->prepare($sql);

        // Bind parameters to the SQL statement using bindValue
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->bindValue(":total_amount", $total_amount, PDO::PARAM_STR);
        $stmt->bindValue(":payment_method", $payment_method, PDO::PARAM_STR);

        try {
            // Execute the statement and check for success
            if ($stmt->execute()) {
                // Return the last inserted ID
                return $this->db->lastInsertId();
            } else {
                return false;  // In case of failure
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;  // Return false on error
        }
    }

    // Add items to the sale
    public function addSaleItem($sale_id, $product_id, $quantity, $subtotal) {
        $sql = "INSERT INTO sale_items (sale_id, product_id, quantity, subtotal) VALUES (:sale_id, :product_id, :quantity, :subtotal)";
        $stmt = $this->db->prepare($sql);

        // Bind parameters to the SQL statement
        $stmt->bindValue(":sale_id", $sale_id, PDO::PARAM_INT);
        $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);
        $stmt->bindValue(":subtotal", $subtotal, PDO::PARAM_STR);

        try {
            // Execute the statement
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }

    // Delete a sale by ID
    public function deleteSale($id) {
        $sql = "DELETE FROM sales WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
