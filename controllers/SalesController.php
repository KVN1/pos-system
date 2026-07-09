<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/SalesModel.php';
require_once __DIR__ . '/../controllers/ActivityLogController.php';

class SalesController {
    private $salesModel;
    private $logController;

    public function __construct() {
        $this->salesModel = new SalesModel();
        $this->logController = new ActivityLogController();
    }

    // Save sale into DB from session
public function processSale() {
if (empty($_SESSION['sales'])) {
$_SESSION['flash_message'] = "No items in cart to process.";
$_SESSION['flash_type'] = "error";
header("Location: /views/add-sales.php");
exit();
}

$user_id         = $_SESSION['user_id'] ?? 0;
$paymentMethod   = $_SESSION['paymentMethod'] ?? 'Cash';
$referenceNumber = $_SESSION['referenceNumber'] ?? null;
$totalAmount     = (float)($_SESSION['totalAmount'] ?? 0);
$cashGiven       = (float)($_SESSION['cashGiven'] ?? 0);
$discount        = (float)($_SESSION['discount'] ?? 0);
$salesData       = $_SESSION['sales'];

// 1️⃣ Check stock for all items before creating sale
foreach ($salesData as $item) {
    $quantity = (float)$item['quantity'];
    if (!$this->salesModel->hasSufficientStock($item['code'], $quantity)) {
        $_SESSION['flash_message'] = "Item {$item['description']} is out of stock!";
        $_SESSION['flash_type'] = "error";
        header("Location: /views/add-sales.php");
        exit();
    }
}

// 2️⃣ Insert sale header
$sale_id = $this->salesModel->addSale($user_id, $totalAmount, $paymentMethod, $cashGiven, $discount);
if (!$sale_id) {
    $_SESSION['flash_message'] = "Failed to save sale. Please try again.";
    $_SESSION['flash_type'] = "error";
    header("Location: /views/add-sales.php");
    exit();
}

// 3️⃣ Insert sale items & deduct stock
foreach ($salesData as $item) {
    $quantity = (float)$item['quantity'];
    $sell_price = (float)$item['sell_price'];

    // Insert sale item
    $this->salesModel->addSaleItem(
        $sale_id,
        $item['code'],
        $quantity,
        $sell_price,
        $paymentMethod,
        $referenceNumber,
        $totalAmount,
        $cashGiven,
        $discount
    );

    // Deduct stock (main + batches)
    $this->salesModel->deductStock($item['code'], $quantity);
}

// 4️⃣ Add activity log
$details = "Sale ID: $sale_id, Total: ₱" . number_format($totalAmount, 2) . ", Payment: $paymentMethod";
$this->logController->addLog($user_id, 'Add Sale', $details);

// 5️⃣ Clear session
unset(
    $_SESSION['sales'],
    $_SESSION['totalAmount'],
    $_SESSION['paymentMethod'],
    $_SESSION['cashGiven'],
    $_SESSION['changeAmount'],
    $_SESSION['discount'],
    $_SESSION['referenceNumber']
);

// 6️⃣ Flash success
$_SESSION['flash_message'] = "Sale processed successfully!";
$_SESSION['flash_type'] = "success";
header("Location: /views/add-sales.php");
exit();

}

    // Add or update an item in session cart
    public function addItemToSession($item) {
        if (!isset($_SESSION['sales'])) {
            $_SESSION['sales'] = [];
        }

        $found = false;
        foreach ($_SESSION['sales'] as &$existingItem) {
            if ($existingItem['code'] === $item['code']) {
                $existingItem['quantity'] += (float)$item['quantity']; // ✅ keep decimals
                $found = true;
                break;
            }
        }
        if (!$found) {
            $item['quantity'] = (float)$item['quantity']; // ✅ ensure float
            $_SESSION['sales'][] = $item;
        }

        $_SESSION['totalAmount'] = $this->calculateTotal($_SESSION['sales']);
    }

    // Remove an item from session cart
    public function removeItemFromSession($code) {
        if (isset($_SESSION['sales'])) {
            $_SESSION['sales'] = array_filter($_SESSION['sales'], function ($item) use ($code) {
                return $item['code'] !== $code;
            });
            $_SESSION['totalAmount'] = $this->calculateTotal($_SESSION['sales']);
        }
    }

    // Set payment/session info before saving
    public function setPaymentDetails($method, $cash, $change, $reference = null, $discount = 0) {
        $_SESSION['paymentMethod']   = $method;
        $_SESSION['cashGiven']       = (float)$cash;
        $_SESSION['changeAmount']    = (float)$change;
        $_SESSION['referenceNumber'] = $reference;
        $_SESSION['discount']        = (float)$discount;
    }

    public function addSalesPage() {
        include "views/add-sales.php";
    }

    public function salesReport() {
        header('Location: /views/sales.php');
        exit();
    }

    public function calculateTotal($items) {
        $total = 0;
        foreach ($items as $item) {
            $total += ((float)$item['sell_price'] * (float)$item['quantity']); // ✅ precise
        }
        return round($total, 2); // ✅ 2 decimal precision
    }
}

// Only trigger saving when form submits
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proceedSale'])) {
    $saleController = new SalesController();
    $saleController->processSale();
}
?>
