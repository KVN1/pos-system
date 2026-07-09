<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'models/DashboardModel.php';

class DashboardController {
    private $dashboardModel;

    public function __construct() {
        $this->dashboardModel = new DashboardModel();
    }

    // Load dashboard with data
    public function index() {
        // Check authentication
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header("Location: /user/login");
            exit;
        }

        // Get dashboard data from model
        $dashboardData = $this->dashboardModel->getDashboardData();

        // Load the view with data
        require_once 'views/dashboard.php';
    }

    // Get sales data for today
    public function getTodaySales() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $todaySales = $this->dashboardModel->getTodaySales();
        echo json_encode($todaySales);
    }

    // Get total transactions for today
    public function getTodayTransactions() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $todayTransactions = $this->dashboardModel->getTodayTransactions();
        echo json_encode($todayTransactions);
    }

    // Get best selling product
    public function getBestSeller() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $bestSeller = $this->dashboardModel->getBestSeller();
        echo json_encode($bestSeller);
    }

    // Get near expiry products
    public function getNearExpiryProducts() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $nearExpiryProducts = $this->dashboardModel->getNearExpiryProducts();
        echo json_encode($nearExpiryProducts);
    }

    // Get low stock products
    public function getLowStockProducts() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /user/login");
            exit;
        }

        $lowStockProducts = $this->dashboardModel->getLowStockProducts();
        echo json_encode($lowStockProducts);
    }
}
?>
