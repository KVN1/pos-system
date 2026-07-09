<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/controllers/SalesController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'checkout') {
    $saleData = $_POST['saleData'];

    // Decode JSON if needed (depends on how you pass data)
    if (is_string($saleData)) {
        $saleData = json_decode($saleData, true);
    }

    $salesController = new SalesController();
    $saleId = $salesController->processCheckout($saleData);

    echo json_encode([
        'success' => true,
        'sale_id' => $saleId
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
