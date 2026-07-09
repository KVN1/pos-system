<?php
require_once __DIR__ . '/../models/SalesModel.php';

class SalesController {
    private $salesModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->salesModel = new SalesModel();
    }



    // Show all sales
    public function index() {
        $sales = $this->salesModel->getAllSales();
        include __DIR__ . '/../views/sales.php';
    }

    // Show details of a single sale
    public function show($id) {
        $sale = $this->salesModel->getSaleById($id);
        if (!$sale) {
            header("Location: /sales?error=Sale not found");
            exit;
        }
        include __DIR__ . '/../views/sale-detail.php';
    }

    public function addSales() {
        require_once 'views/add-sales.php';
    }

    // Process a new sale with items
public function store() {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            echo json_encode(["success" => false, "message" => "Invalid JSON data"]);
            exit;
        }

        $user_id = $_SESSION['user_id'] ?? 1; // Default to user 1 if not set
        $total_amount = filter_var($data['total_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $payment_method = $data['payment_method'] ?? 'cash';  // Default payment method is cash
        $items = $data['items'];

        if (!$user_id || !$total_amount || !$payment_method || empty($items)) {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
            exit;
        }

        // Now proceed with adding the sale
        $sale_id = $this->salesModel->addSale($user_id, $total_amount, $payment_method);
        
        if ($sale_id) {
            foreach ($items as $item) {
                $this->salesModel->addSaleItem($sale_id, $item['product_id'], $item['quantity'], $item['subtotal']);
            }
            echo json_encode(["success" => true, "message" => "Sale completed successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to add sale"]);
        }
    }
}


    // Delete a sale
    public function destroy($id) {
        if (!is_numeric($id)) {
            header("Location: /sales?error=Invalid sale ID");
            exit;
        }

        if ($this->salesModel->deleteSale($id)) {
            header("Location: /sales?success=Sale deleted");
            exit;
        } else {
            header("Location: /sales?error=Failed to delete sale");
            exit;
        }
    }
}

// Handle request from AJAX
if (isset($_POST['action']) && $_POST['action'] === 'processSale') {
    $controller = new SalesController();
    $controller->store();
}
?>
