<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/database.php';

class SalesReportModel {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection(); // PDO connection
    }

public function getFilteredSales($from = null, $to = null, $payment = '', $user = '', $limit = 15, $offset = 0) {
    $params = [];
    $query = "SELECT s.*, u.first_name, u.last_name 
              FROM sales s 
              JOIN users u ON s.user_id = u.user_id 
              WHERE 1=1";

    if (!empty($from)) { $query .= " AND s.sale_date >= ?"; $params[] = $from; }
    if (!empty($to)) { $query .= " AND s.sale_date <= ?"; $params[] = $to; }
    if (!empty($payment)) { $query .= " AND s.payment_method = ?"; $params[] = $payment; }
    if (!empty($user)) { $query .= " AND s.user_id = ?"; $params[] = $user; }

    $query .= " ORDER BY s.sale_date DESC";

    // Only add LIMIT/OFFSET if they are valid numbers
    if ($limit !== null && $limit > 0 && $offset !== null && $offset >= 0) {
        $query .= " LIMIT $limit OFFSET $offset";
    }

    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getTotalSales($from = null, $to = null, $payment = '', $user = '') {
    $params = [];
    $query = "
        SELECT IFNULL(SUM(si.price * si.quantity), 0) AS total_sales
        FROM sales_items si
        JOIN sales s ON si.sale_id = s.id
        WHERE si.status != 'Returned'
    ";

    if (!empty($from)) { $query .= " AND s.sale_date >= ?"; $params[] = $from; }
    if (!empty($to)) { $query .= " AND s.sale_date <= ?"; $params[] = $to; }
    if (!empty($payment)) { $query .= " AND s.payment_method = ?"; $params[] = $payment; }
    if (!empty($user)) { $query .= " AND s.user_id = ?"; $params[] = $user; }

    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['total_sales'] ?? 0;
}



    public function getTotalTransactions($from = null, $to = null, $payment = '', $user = '') {
        $query = "SELECT COUNT(*) AS total FROM sales WHERE 1=1";
        $params = [];

        if (!empty($from)) { $query .= " AND sale_date >= ?"; $params[] = $from; }
        if (!empty($to)) { $query .= " AND sale_date <= ?"; $params[] = $to; }
        if (!empty($payment)) { $query .= " AND payment_method = ?"; $params[] = $payment; }
        if (!empty($user)) { $query .= " AND user_id = ?"; $params[] = $user; }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function getBestSellers($from = null, $to = null, $limit = 15, $offset = 0) {
        $params = [];
        $query = "
            SELECT p.description AS product_name, 
                   SUM(si.quantity) AS quantity_sold, 
                   SUM(si.price * si.quantity) AS total_sales
            FROM sales_items si
            JOIN products p ON si.product_code = p.code
            JOIN sales s ON si.sale_id = s.id
            WHERE 1=1
        ";

        if (!empty($from)) { $query .= " AND s.sale_date >= ?"; $params[] = $from; }
        if (!empty($to)) { $query .= " AND s.sale_date <= ?"; $params[] = $to; }

        $query .= " GROUP BY p.description ORDER BY quantity_sold DESC";

        // Only apply LIMIT/OFFSET if not null
        if ($limit !== null && $offset !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSlowMovingProducts($from = null, $to = null, $limit = 15, $offset = 0) {
        $params = [];
        $query = "
            SELECT p.description AS product_name, 
                   IFNULL(SUM(si.quantity), 0) AS quantity_sold, 
                   IFNULL(SUM(si.price * si.quantity), 0) AS total_sales
            FROM products p
            LEFT JOIN sales_items si ON si.product_code = p.code
            LEFT JOIN sales s ON si.sale_id = s.id
        ";

        $whereAdded = false;
        if (!empty($from)) { $query .= " WHERE s.sale_date >= ?"; $params[] = $from; $whereAdded = true; }
        if (!empty($to)) { $query .= $whereAdded ? " AND s.sale_date <= ?" : " WHERE s.sale_date <= ?"; $params[] = $to; }

        $query .= " GROUP BY p.description ORDER BY quantity_sold ASC";

        if ($limit !== null && $offset !== null) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " LIMIT $limit OFFSET $offset";
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT user_id, first_name, last_name FROM users ORDER BY first_name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getSaleItems($saleId) {
    $stmt = $this->db->prepare("
        SELECT si.*, p.description AS product_name, p.buy_price, 
               (si.price - p.buy_price) AS markup
        FROM sales_items si
        LEFT JOIN products p ON si.product_code = p.code
        WHERE si.sale_id = ?
    ");
    $stmt->execute([$saleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function returnSale($sale_id, $user_id, $reason = '') {
    try {
        $this->db->beginTransaction();

        // Get sale items
        $items = $this->db->prepare("SELECT product_code, quantity FROM sales_items WHERE sale_id = ?");
        $items->execute([$sale_id]);
        $sale_items = $items->fetchAll(PDO::FETCH_ASSOC);

        if (empty($sale_items)) {
            throw new Exception("No items found for sale ID $sale_id");
        }

        // Update sale and include reason
        $updateSale = $this->db->prepare("UPDATE sales SET status = 'Returned', return_reason = ? WHERE id = ?");
        $updateSale->execute([$reason, $sale_id]);

        // Update sales items
$this->db->prepare("
    UPDATE sales_items 
    SET status = 'Returned', return_reason = ? 
    WHERE sale_id = ?
")->execute([$reason, $sale_id]);

        // Restock each product
        $restock = $this->db->prepare("UPDATE products SET stock = stock + ? WHERE code = ?");
        foreach ($sale_items as $item) {
            $restock->execute([$item['quantity'], $item['product_code']]);
        }

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        error_log("❌ Return failed: " . $e->getMessage());
        return false;
    }
}





}
?>
