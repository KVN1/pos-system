<?php
require_once 'database.php';

class DashboardModel {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Get all dashboard data
    public function getDashboardData() {
        return [
            'todaySales' => $this->getTodaySales(),
            'todayTransactions' => $this->getTodayTransactions(),
            'bestSeller' => $this->getBestSeller(),
            'nearExpiryProducts' => $this->getNearExpiryProducts(),
            'lowStockProducts' => $this->getLowStockProducts(),
            'notifications' => $this->getNotifications(),
            'todaySalesRaw' => $this->getTodaySalesRaw(),
            'bestSellerWithQuantity' => $this->getBestSellerWithQuantity()
        ];
    }

    // Get today's sales without formatting
    public function getTodaySalesRaw() {
        try {
            $conn = $this->db->getConnection();
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as today_sales 
                FROM sales 
                WHERE DATE(sale_date) = :today
            ");
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['today_sales'];
        } catch (Exception $e) {
            return 0;
        }
    }

    // Get best seller with quantity sold
    public function getBestSellerWithQuantity() {
        try {
            $conn = $this->db->getConnection();
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("
                SELECT p.description as product_name, SUM(si.quantity) as total_sold
                FROM sale_items si
                JOIN products p ON si.product_code = p.code
                JOIN sales s ON si.sale_id = s.id
                WHERE DATE(s.sale_date) = :today
                GROUP BY p.code, p.description
                ORDER BY total_sold DESC
                LIMIT 1
            ");
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    // Get today's total sales
    public function getTodaySales() {
        try {
            $conn = $this->db->getConnection();
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("
                SELECT COALESCE(SUM(total_amount), 0) as today_sales 
                FROM sales 
                WHERE DATE(sale_date) = :today
            ");
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return number_format($result['today_sales'], 2);
        } catch (Exception $e) {
            return "0.00";
        }
    }

    // Get today's total transactions count
    public function getTodayTransactions() {
        try {
            $conn = $this->db->getConnection();
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("
                SELECT COUNT(*) as transaction_count 
                FROM sales 
                WHERE DATE(sale_date) = :today
            ");
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['transaction_count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    // Get best selling product for today
    public function getBestSeller() {
        try {
            $conn = $this->db->getConnection();
            $today = date('Y-m-d');
            
            $stmt = $conn->prepare("
                SELECT p.description as product_name, SUM(si.quantity) as total_sold
                FROM sale_items si
                JOIN products p ON si.product_code = p.code
                JOIN sales s ON si.sale_id = s.id
                WHERE DATE(s.sale_date) = :today
                GROUP BY p.code, p.description
                ORDER BY total_sold DESC
                LIMIT 1
            ");
            $stmt->bindParam(':today', $today);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['product_name'] : 'No sales today';
        } catch (Exception $e) {
            return 'No sales today';
        }
    }

    // Get products near expiry (within 25 days)
    public function getNearExpiryProducts() {
        try {
            $conn = $this->db->getConnection();
            
            $stmt = $conn->prepare("
                SELECT p.description as product_name, p.category as category_name, p.expiry
                FROM products p
                WHERE p.expiry IS NOT NULL 
                AND p.expiry != ''
                AND STR_TO_DATE(p.expiry, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 25 DAY)
                ORDER BY STR_TO_DATE(p.expiry, '%Y-%m-%d') ASC
                LIMIT 10
            ");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get products with low stock (less than 30 units)
    public function getLowStockProducts() {
        try {
            $conn = $this->db->getConnection();
            
            $stmt = $conn->prepare("
                SELECT p.description as product_name, p.category as category_name, p.stock as stock_quantity
                FROM products p
                WHERE p.stock < 30
                ORDER BY p.stock ASC
                LIMIT 10
            ");
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    // Get notifications
    public function getNotifications() {
        $notifications = [];
        
        // Check for near expiry products
        $nearExpiry = $this->getNearExpiryProducts();
        if (!empty($nearExpiry)) {
            $count = count($nearExpiry);
            $notifications[] = "You have {$count} product(s) near expiry.";
        }
        
        // Check for low stock products
        $lowStock = $this->getLowStockProducts();
        if (!empty($lowStock)) {
            $count = count($lowStock);
            $notifications[] = "You have {$count} product(s) with low stock.";
        }
        
        // Add today's sales summary
        $todaySales = $this->getTodaySales();
        $todayTransactions = $this->getTodayTransactions();
        if ($todaySales > 0) {
            $notifications[] = "Today: {$todayTransactions} transactions, ₱{$todaySales} total sales.";
        }
        
        // If no notifications, add a default one
        if (empty($notifications)) {
            $notifications[] = "All systems running smoothly!";
        }
        
        return $notifications;
    }
}
?>
