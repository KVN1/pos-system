<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/controllers/SalesReportController.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: /POSu/views/sales.php');
    exit;
}

$sale_id = $_POST['sale_id'] ?? null;
$reason = trim($_POST['return_reason'] ?? 'No reason provided');
$user_id = $_SESSION['user_id'] ?? null;

if (!$sale_id || !$user_id) {
    $_SESSION['error'] = 'Sale or user not found.';
    header('Location: /POSu/views/sales.php');
    exit;
}

$controller = new SalesReportController();
$result = $controller->returnSale($sale_id, $user_id, $reason);

if ($result) {
    $_SESSION['success'] = 'Sale successfully marked as returned. Reason: ' . htmlspecialchars($reason);
} else {
    $_SESSION['error'] = 'Failed to return sale.';
}

header('Location: /POSu/views/sales.php');
exit;
?>
