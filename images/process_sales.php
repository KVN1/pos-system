<?php
// Include necessary files and classes
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/ProductModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/SalesModel.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/SaleItemsModel.php'; // If you have a SaleItems model

// Start the session to retrieve cart data
session_start();

// Check if the cart has items
if (!isset($_SESSION['sales']) || empty($_SESSION['sales'])) {
    echo json_encode(["success" => false, "message" => "No items in the cart"]);
    exit;
}

// Process sale data from the session
$total_amount = 0;
$sale_items = [];
foreach ($_SESSION['sales'] as $item) {
    // Calculate the total amount for each item and the entire sale
    $total_amount += $item['total'];

    // Add item data to sale_items
    $sale_items[] = [
        'product_code' => $item['code'],
        'description' => $item['description'],
        'price' => $item['sell_price'],
        'quantity' => $item['quantity'],
        'total' => $item['total']
    ];
}

// Check if total_amount is valid
if ($total_amount <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid sale amount"]);
    exit;
}

// Initialize models
$salesModel = new SalesModel();
$productModel = new ProductModel();

// Begin transaction (if your SalesModel supports transactions)
$salesModel->beginTransaction();

try {
    // Create the sale record
    $sale_id = $salesModel->addSale($total_amount);

    if (!$sale_id) {
        throw new Exception("Error creating sale record");
    }

    // Process each sale item
    foreach ($sale_items as $item) {
        // Add sale item record
        $isItemAdded = $salesModel->addSaleItem($sale_id, $item['product_code'], $item['description'], $item['price'], $item['quantity'], $item['total']);

        if (!$isItemAdded) {
            throw new Exception("Error adding sale item for product code: " . $item['product_code']);
        }

        // Check product stock before updating
        $current_stock = $productModel->getStockByProductCode($item['product_code']);
        if ($current_stock < $item['quantity']) {
            throw new Exception("Insufficient stock for product code: " . $item['product_code']);
        }

        // Update product stock after sale
        $new_stock = $current_stock - $item['quantity'];
        $isStockUpdated = $productModel->updateProductStock($item['product_code'], $new_stock);

        if (!$isStockUpdated) {
            throw new Exception("Error updating stock for product code: " . $item['product_code']);
        }
    }

    // Commit the transaction if everything is successful
    $salesModel->commit();

    // Clear the cart after successful sale
    unset($_SESSION['sales']);

    echo json_encode(["success" => true, "message" => "Sale completed successfully", "sale_id" => $sale_id]);
} catch (Exception $e) {
    // Rollback the transaction in case of any error
    $salesModel->rollback();

    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
