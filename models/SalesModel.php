<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/database.php';

class SalesModel {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // PDO connection
    }

    // Insert into sales (main sale record)
    public function addSale($user_id, $totalAmount, $paymentMethod, $cashGiven, $discount) {
        $changeAmount = $cashGiven - ($totalAmount - $discount);

        $query = "INSERT INTO sales 
            (user_id, total_amount, payment_method, cash_given, change_amount, discount, sale_date) 
            VALUES (:user_id, :total_amount, :payment_method, :cash_given, :change_amount, :discount, NOW())";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':total_amount', round($totalAmount, 2));
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->bindValue(':cash_given', round($cashGiven, 2));
        $stmt->bindValue(':change_amount', round($changeAmount, 2));
        $stmt->bindValue(':discount', round($discount, 2));

        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Insert product-level items
    public function addSaleItem($sale_id, $product_code, $quantity, $price, $payment_method, $reference_number, $total_amount, $cash_given, $discount) {
        $quantity = (float)$quantity;
        $price = (float)$price;
        $total_amount = (float)$total_amount;
        $cash_given = (float)$cash_given;
        $discount = (float)$discount;

        $change_amount = $cash_given - $total_amount;

        $query = "INSERT INTO sales_items 
            (sale_id, product_code, quantity, price, payment_method, reference_number, total_amount, cash_given, change_amount, discount) 
            VALUES (:sale_id, :product_code, :quantity, :price, :payment_method, :reference_number, :total_amount, :cash_given, :change_amount, :discount)";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $stmt->bindParam(':product_code', $product_code);
        $stmt->bindValue(':quantity', round($quantity, 3));
        $stmt->bindValue(':price', round($price, 2));
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':reference_number', $reference_number);
        $stmt->bindValue(':total_amount', round($total_amount, 2));
        $stmt->bindValue(':cash_given', round($cash_given, 2));
        $stmt->bindValue(':change_amount', round($change_amount, 2));
        $stmt->bindValue(':discount', round($discount, 2));

        return $stmt->execute();
    }

    // Deduct stock (main + batches)
    public function deductStock($product_code, $quantity) {
        $quantity = (float)$quantity;

        // Fetch main product
        $stmt = $this->db->prepare("SELECT stock, id FROM products WHERE code = :code");
        $stmt->bindParam(':code', $product_code);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) return false;

        $remaining = $quantity;

        // Deduct main stock
        if ($product['stock'] >= $remaining) {
            $stmt = $this->db->prepare("UPDATE products SET stock = stock - :qty WHERE code = :code");
            $stmt->bindValue(':qty', $remaining);
            $stmt->bindParam(':code', $product_code);
            $stmt->execute();
            return true;
        } else {
            $stmt = $this->db->prepare("UPDATE products SET stock = 0 WHERE code = :code");
            $stmt->bindParam(':code', $product_code);
            $stmt->execute();
            $remaining -= $product['stock'];

            // Deduct from batches (FIFO)
            $stmt = $this->db->prepare("SELECT batch_id, stock FROM product_batches WHERE product_id = :pid AND stock > 0 ORDER BY date_added ASC");
            $stmt->bindParam(':pid', $product['id']);
            $stmt->execute();
            $batches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($batches as $batch) {
                if ($batch['stock'] >= $remaining) {
                    $stmtUpdate = $this->db->prepare("UPDATE product_batches SET stock = stock - :qty WHERE batch_id = :bid");
                    $stmtUpdate->bindValue(':qty', $remaining);
                    $stmtUpdate->bindParam(':bid', $batch['batch_id']);
                    $stmtUpdate->execute();
                    $remaining = 0;
                    break;
                } else {
                    $stmtUpdate = $this->db->prepare("UPDATE product_batches SET stock = 0 WHERE batch_id = :bid");
                    $stmtUpdate->bindParam(':bid', $batch['batch_id']);
                    $stmtUpdate->execute();
                    $remaining -= $batch['stock'];
                }
            }
        }
    }

    // Check if product has sufficient stock (main + batches)
    public function hasSufficientStock($product_code, $quantity) {
        $quantity = (float)$quantity;

        $stmt = $this->db->prepare("SELECT stock, id FROM products WHERE code = :product_code");
        $stmt->bindParam(':product_code', $product_code);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) return false;

        $totalStock = (float)$product['stock'];

        $stmtBatches = $this->db->prepare("SELECT SUM(stock) FROM product_batches WHERE product_id = :product_id");
        $stmtBatches->bindParam(':product_id', $product['id']);
        $stmtBatches->execute();
        $batchStock = (float)$stmtBatches->fetchColumn();

        $totalStock += $batchStock;

        return $totalStock >= $quantity;
    }
}
?>
