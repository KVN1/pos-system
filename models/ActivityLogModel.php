<?php
require_once __DIR__ . '/../database.php';

class ActivityLogModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // Log an activity
    public function log($userId, $action, $details = '') {
        if (is_array($details)) {
            $details = implode("; ", $details);
        }

        $stmt = $this->conn->prepare("
            INSERT INTO activity_log (user_id, action, details, log_time) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $action, $details]);
    }

    // Get logs with optional pagination
    public function getLogs($search = '', $startDate = '', $endDate = '', $limit = 50, $offset = 0) {
        $sql = "SELECT * FROM activity_log WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (action LIKE :search OR details LIKE :search)";
            $params['search'] = "%$search%";
        }
        if ($startDate) {
            $sql .= " AND DATE(log_time) >= :startDate";
            $params['startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(log_time) <= :endDate";
            $params['endDate'] = $endDate;
        }

        $sql .= " ORDER BY log_time DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Count logs
    public function getLogsCount($search = '', $startDate = '', $endDate = '') {
        $sql = "SELECT COUNT(*) FROM activity_log WHERE 1=1";
        $params = [];

        if ($search) {
            $sql .= " AND (action LIKE :search OR details LIKE :search)";
            $params['search'] = "%$search%";
        }
        if ($startDate) {
            $sql .= " AND DATE(log_time) >= :startDate";
            $params['startDate'] = $startDate;
        }
        if ($endDate) {
            $sql .= " AND DATE(log_time) <= :endDate";
            $params['endDate'] = $endDate;
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    // Get users (user_id => full name)
    public function getUsers() {
        $stmt = $this->conn->query("SELECT user_id, CONCAT(first_name, ' ', last_name) AS name FROM users");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Get products (id => description)
    public function getProducts() {
        $stmt = $this->conn->query("SELECT id, description FROM products");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Get categories (id => category_name)
    public function getCategories() {
        $stmt = $this->conn->query("SELECT id, category_name FROM categories");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    // Get batches (batch_id => batch_id)
    public function getBatches() {
        $stmt = $this->conn->query("SELECT batch_id, batch_id FROM product_batches");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

}
?>
