<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/DiscountModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/ActivityLogModel.php';

class DiscountController {
    private $model;

    public function __construct() {
        $this->model = new DiscountModel();
    }

    private function logAction($action, $details) {
        $logModel = new ActivityLogModel();
        $userId = $_SESSION['user_id'] ?? 0; 
        $logModel->addLog($userId, $action, $details);
    }

    public function index() {
        return $this->model->getAllDiscounts();
    }

public function update() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'], $_POST['percentage'])) {
        $id = intval($_POST['update']);
        $percentage = floatval($_POST['percentage'][$id] ?? 0);

        if ($this->model->updateDiscount($id, $percentage)) {
            $this->logAction("Update Discount", "Updated discount ID $id to $percentage%");
            $_SESSION['message'] = "Discount updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update discount";
        }

        header("Location: /POSu/views/settings.php");
        exit;
    }
}

}

// Instantiate controller if direct POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new DiscountController();
    $controller->update();
}
