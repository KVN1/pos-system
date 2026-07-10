<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../controllers/ActivityLogController.php';

try {
    $conn = Database::getConnection();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Validate POST data
if (!isset($_POST['return_item_id']) || !isset($_POST['sale_id'])) {
    $_SESSION['error'] = "Invalid return request.";
    header("Location: sales.php");
    exit;
}

$item_id = (int)$_POST['return_item_id'];
$sale_id = (int)$_POST['sale_id'];
$return_reason = trim($_POST['return_reason'] ?? 'No reason provided');

// Fetch sale item info
$stmt = $conn->prepare("SELECT product_code, quantity, status FROM sales_items WHERE id = ?");
$stmt->execute([$item_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    $_SESSION['error'] = "Item not found.";
    header("Location: sales.php");
    exit;
}

// Prevent double return
if ($item['status'] === 'Returned') {
    $_SESSION['error'] = "Item already returned.";
    header("Location: sales.php");
    exit;
}

$product_code = $item['product_code'];
$qty = (int)$item['quantity'];

try {
    $conn->beginTransaction();

    // Mark item as returned and save reason
    $update = $conn->prepare("UPDATE sales_items SET status = 'Returned', return_reason = ? WHERE id = ?");
    $update->execute([$return_reason, $item_id]);

    // Restock product
    $restock = $conn->prepare("UPDATE products SET stock = stock + ? WHERE code = ?");
    $restock->execute([$qty, $product_code]);

    $conn->commit();

    // Log action
    $logController = new ActivityLogController();
    $user_id = $_SESSION['user_id'] ?? 0;
    $details = "Returned item ID: $item_id (Product code: $product_code, Qty: $qty) from Sale ID: $sale_id. Reason: $return_reason";
    $logController->addLog($user_id, 'Return Item', $details);

    $_SESSION['success'] = "Item successfully returned and restocked.";
} catch (Exception $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Failed to process return: " . $e->getMessage();
}

header("Location: sales.php");
exit;

?>
