<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Manila');

require_once __DIR__ . '/../ProductModel.php';
require_once __DIR__ . '/../ActivityLogModel.php';

$productController = new ProductController();

/** ───── Instantiate Controller ───── */

/** ───── Handle all POST Actions ───── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case "add": $productController->addProduct(); break;
        case "edit": $productController->editProduct(); break;
        case "delete": $productController->deleteProduct(); break;
        case "restore": $productController->restoreProduct(); break;
        case "reorder": $productController->reorderStock(); break;
        case "edit_batch": $productController->editBatch(); break;
        case "delete_batch": $productController->archiveBatch(); break;
        case "restore_batch": $productController->restoreBatch(); break;
        case "return_damaged": $productController->returnDamagedItem(); break; // <-- new

    }
}

/** ───── AJAX Search ───── */
if (isset($_POST['search'])) {
    $search = $_POST['search'];
    $products = $productController->productModel->getProductBySearch($search);

    if (!empty($products)) {
        $result = [];
        foreach ($products as $product) {
            $result[] = "{$product['code']}|{$product['description']}|{$product['sell_price']}|{$product['perishability']}";
        }
        echo implode("\n", $result);
    } else {
        echo "not_found";
    }
    exit;
}

/** ───── Get Products by Category (AJAX) ───── */
if (isset($_GET['action'], $_GET['category_name']) && $_GET['action'] === 'getByCategory') {
    $products = $productController->getByCategoryName($_GET['category_name']);
    echo json_encode($products);
    exit;
}
class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new ProductModel();
    }

    /** ───── Activity Log Helper ───── */
    private function logAction($action, $details) {
        $logModel = new ActivityLogModel();
        $userId = $_SESSION['user_id'] ?? 0; 
        $logModel->log($userId, $action, $details);
    }

    /** ───── Page Load ───── */
    public function products() {
        require_once 'views/products.php';
    }

    /** ───── Getters ───── */
public function index() {
    $allProducts = $this->productModel->getAllProducts();
    $allBatches  = $this->productModel->getActiveBatches();

    $batchesByProduct = [];
    foreach ($allBatches as $row) {
        if (!empty($row['batch_id'])) {
            $pid = $row['product_id'];
            // ensure numeric stock types
            $batchesByProduct[$pid][] = [
    'batch_id'     => $row['batch_id'],
    'batch_number' => $row['batch_number'] ?? null, // <-- add this
    'stock'        => (float)($row['stock'] ?? 0),
    'buy_price'    => (float)($row['buy_price'] ?? 0),
    'sell_price'   => (float)($row['sell_price'] ?? 0),
    'date_added'   => $row['date_added'] ?? null,
    'expiry'       => $row['expiry'] ?? null,
    'status'       => $row['status'] ?? null
];

        }
    }

    // attach batches and compute total_stock (main stock + sum of all batches)
foreach ($allProducts as &$product) {
    $pid = $product['id'];
    $product['batches'] = $batchesByProduct[$pid] ?? [];

    // FIFO sorting: oldest → newest
    if (!empty($product['batches'])) {
        usort($product['batches'], function ($a, $b) {
            return strtotime($a['date_added']) - strtotime($b['date_added']);
        });
    }

    $mainStock = isset($product['stock']) ? (float)$product['stock'] : 0;
    $batchStock = !empty($product['batches']) 
        ? array_sum(array_column($product['batches'], 'stock')) 
        : 0;

    $product['total_stock'] = $mainStock + $batchStock;

    $product['stock'] = $mainStock;
    $product['buy_price'] = isset($product['buy_price']) ? (float)$product['buy_price'] : 0;
    $product['sell_price'] = isset($product['sell_price']) ? (float)$product['sell_price'] : 0;
}

    unset($product);

    return $allProducts;
}

public function indexForSales() {
    $allProducts = $this->productModel->getAllProducts();
    $allBatches  = $this->productModel->getActiveBatches();

    // group batches by product_id
    $batchesByProduct = [];
    foreach ($allBatches as $row) {
        if (!empty($row['batch_id'])) {
            $pid = $row['product_id'];
            $batchesByProduct[$pid][] = [
                'batch_id'   => $row['batch_id'],
                'stock'      => (float)($row['stock'] ?? 0),
                'buy_price'  => (float)($row['buy_price'] ?? 0),
                'sell_price' => (float)($row['sell_price'] ?? 0),
                'date_added' => $row['date_added'] ?? null,
                'expiry'     => $row['expiry'] ?? null,
                'status'     => $row['status'] ?? null
            ];
        }
    }

    // Attach batches, stock, FIFO, compute total stock
    foreach ($allProducts as &$product) {
        $pid = $product['id'];
        $product['batches'] = $batchesByProduct[$pid] ?? [];

        if (!empty($product['batches'])) {
            usort($product['batches'], function ($a, $b) {
                return strtotime($a['date_added']) - strtotime($b['date_added']);
            });
        }

        $mainStock = isset($product['stock']) ? (float)$product['stock'] : 0;
        $batchStock = !empty($product['batches']) 
            ? array_sum(array_column($product['batches'], 'stock')) 
            : 0;

        $product['total_stock'] = $mainStock + $batchStock;
    }

    unset($product);

    // ✔ Filter expired products ONLY for sales
    $today = date('Y-m-d');

    $validProducts = array_filter($allProducts, function($p) use ($today) {

        // No expiry → allowed
        if (empty($p['expiry'])) {
            foreach ($p['batches'] as $b) {
                if (!empty($b['expiry']) && $b['expiry'] > $today) {
                    return true;
                }
            }
            return true;
        }

        // Main product not expired
        if ($p['expiry'] > $today) {
            return true;
        }

        // If main expired → check batches
        foreach ($p['batches'] as $b) {
            if (!empty($b['expiry']) && $b['expiry'] > $today) {
                return true;
            }
        }

        return false;
    });

    return $validProducts;
}



    public function getCategories() { return $this->productModel->getCategories(); }
    public function getByCategoryName($categoryName) { return $this->productModel->getProductsByCategoryName($categoryName); }
    public function getActiveProducts() { return $this->productModel->getActiveProducts(); }
    public function getLowStockProducts() { return $this->productModel->getLowStockProducts(); }
    public function getNearExpiryProducts() { return $this->productModel->getNearExpiryProducts(); }

    /** ───── Add Product ───── */
public function addProduct() {
    $code           = $_POST['code'] ?? null;
    $description    = $_POST['description'] ?? null;
    $category       = $_POST['category'] ?? '';
    $perishability  = $_POST['perishability'] ?? '';
    $stock          = intval($_POST['stock'] ?? 0);
    $unit           = $_POST['unit'] ?? '';
    $buy_price      = floatval($_POST['buy_price'] ?? 0);
    $sell_price     = floatval($_POST['sell_price'] ?? 0);
    $expiry         = $_POST['expiry'] ?? null;
    $date_added     = date("Y-m-d H:i:s");

    if (!$code || !$description) return;

    $existingProduct = $this->productModel->getProductByCode($code);

    if ($existingProduct) {
        // Relaxed main product check: match code, unit, and buy/sell price
        $isSameMainProduct =
            strtolower(trim($existingProduct['unit'])) === strtolower(trim($unit)) &&
            floatval($existingProduct['buy_price']) == $buy_price &&
            floatval($existingProduct['sell_price']) == $sell_price;

        if ($isSameMainProduct) {
            // Increase main product stock
            $this->productModel->increaseStock($code, $stock);

            $this->logAction(
                "Increase Main Stock",
                "Increased stock of product [{$code}] {$description} by {$stock}"
            );
        } else {
            // Create a new batch
            $this->productModel->insertBatch(
                $existingProduct['id'],
                $stock,
                $buy_price,
                $sell_price,
                $expiry
            );

            $this->logAction(
                "Add Batch",
                "Added new batch for product [{$code}] {$description}"
            );
        }
    } else {
        // New product
        $this->productModel->insertProduct(
            $code,
            $description,
            $category,
            $perishability,
            $stock,
            $unit,
            $buy_price,
            $sell_price,
            $date_added,
            $expiry
        );

        $this->logAction(
            "Add Product",
            "Added new product [{$code}] {$description} with initial stock"
        );
    }

    $_SESSION['flash_message'] = "Product/batch added successfully";
    $_SESSION['flash_type'] = "success";
    header("Location: /products");
    exit();
}



    /** ───── Edit Product ───── */
public function editProduct() {
    $id = $_POST['id'] ?? null;
    if (!$id) return;

    $batchId = $_POST['batch_id'] ?? null;

    $code = $_POST['code'] ?? '';
    $description = $_POST['description'] ?? '';
    $category = $_POST['category'] ?? '';
    $perishability = $_POST['perishability'] ?? '';
    $stock = $_POST['stock'] ?? 0;
    $unit = $_POST['unit'] ?? '';
    $buy_price = $_POST['buy_price'] ?? 0;
    $sell_price = $_POST['sell_price'] ?? 0;
    $expiry = $_POST['expiry'] ?? null;
    $reason = trim($_POST['reason'] ?? 'No justification provided');

    $oldProduct = $this->productModel->getProductById($id);

    // Update main product
    $this->productModel->updateProduct($id, $code, $description, $category, $perishability, $stock, $unit, $buy_price, $sell_price, $expiry);

    // Update batch if batchId exists
    if ($batchId) {
        $this->productModel->updateBatch($batchId, $stock, $buy_price, $sell_price, $expiry);
    }

    // Handle damaged quantity
    // Handle damaged quantity
$damagedQty = (float)($_POST['damaged_qty'] ?? 0);
if ($damagedQty > 0) {
    if ($batchId) {
        // Deduct only from batch
        $this->productModel->deductBatchStock($batchId, $damagedQty);
    } else {
        // Deduct from main product
        $this->productModel->deductProductStock($id, $damagedQty);
    }

    // Insert into damaged items table
    $this->productModel->insertDamagedItem($id, $batchId, $damagedQty, $reason);

    $this->logAction(
        "Damaged Product/Batch",
        "Damaged {$damagedQty} units for product ID {$id}" . ($batchId ? " (Batch ID {$batchId})" : "")
    );
}


    // Log changes
    $fields = [
        'code'=>'Code',
        'description'=>'Description',
        'category'=>'Category',
        'perishability'=>'Perishability',
        'stock'=>'Stock',
        'unit'=>'Unit',
        'buy_price'=>'Buy Price',
        'sell_price'=>'Sell Price',
        'expiry'=>'Expiry Date'
    ];
    $changes = [];
    foreach ($fields as $key => $label) {
        $oldVal = $oldProduct[$key] ?? null;
        $newVal = $_POST[$key] ?? null;
        if ($oldVal != $newVal) $changes[] = "{$label} changed from '{$oldVal}' to '{$newVal}'";
    }
    $details = count($changes) ? implode("; ", $changes) . ". Reason: {$reason}" : "Edited product ID {$id} ({$description}) — no field changes. Reason: {$reason}";
    $this->logAction("Edit Product/Batch", $details);

    $_SESSION['flash_message'] = "Product updated successfully";
    $_SESSION['flash_type'] = "success";
    header("Location: /products");
    exit();
}

    /** ───── Archive / Restore ───── */
    public function deleteProduct() {
        $productId = $_POST['id'] ?? null;
        if ($productId && $this->productModel->deactivateProduct($productId)) {
            $this->logAction("Archive Product", "Archived product ID {$productId}");
            $_SESSION['flash_message'] = "Product archived successfully";
            $_SESSION['flash_type'] = "success";
        }
        header("Location: /products");
        exit();
    }

    public function restoreProduct() {
        $productId = $_POST['id'] ?? null;
        if ($productId && $this->productModel->activateProduct($productId)) {
            $this->logAction("Restore Product", "Restored product ID {$productId}");
            $_SESSION['flash_message'] = "Product restored successfully";
            $_SESSION['flash_type'] = "success";
        }
        header("Location: /views/settings.php#deactivatedModal");
        exit();
    }

    public function archiveBatch() {
        $batchId = $_POST['batch_id'] ?? null;
        if ($batchId && $this->productModel->archiveBatch($batchId)) {
            $this->logAction("Archive Batch", "Archived batch ID {$batchId}");
            $_SESSION['flash_message'] = "Batch archived successfully";
            $_SESSION['flash_type'] = "success";
        }
        header("Location: /products");
        exit();
    }

    public function getBatchesByProductId($productId) {
    return $this->productModel->getActiveBatchesByProductId($productId);
}


// ProductController.php — add inside the class
public function getDamagedItems() {
    return $this->productModel->getDamagedItems();
}

public function returnDamagedItem() {
    $damagedId = $_POST['damaged_id'];
    $productId = $_POST['product_id'];
    $batchId   = $_POST['batch_id']; // may be null
    $qty       = $_POST['quantity'];
    $expiry    = $_POST['expiry'];
    $buyPrice  = $_POST['buy_price'];
    $sellPrice = $_POST['sell_price'];

    if ($batchId) {
        // Returning a damaged item from a batch
        $existingBatch = $this->productModel->getBatchByProductAndExpiry($productId, $expiry);

        if ($existingBatch) {
            $this->productModel->increaseBatchStock($existingBatch['batch_id'], $qty);
        } else {
            $this->productModel->insertBatch($productId, $qty, $buyPrice, $sellPrice, $expiry);
        }
    } else {
        // Returning a damaged item from main product stock
        $existingBatch = $this->productModel->getBatchByProductAndExpiry($productId, $expiry);

        if ($existingBatch) {
            $this->productModel->increaseBatchStock($existingBatch['batch_id'], $qty);
        } else {
            $this->productModel->insertBatch($productId, $qty, $buyPrice, $sellPrice, $expiry);
        }
    }

    // Remove damaged item record
    $this->productModel->deleteDamagedItem($damagedId);

    $_SESSION['flash_message'] = "Damaged item returned successfully";
    $_SESSION['flash_type'] = "success";
    header("Location: /products");
    exit();
}

    

public function editBatch() {
    $batchId = $_POST['batch_id'] ?? null;
    if (!$batchId) return;

    $stock = $_POST['stock'] ?? 0;
    $buy_price = $_POST['buy_price'] ?? 0;
    $sell_price = $_POST['sell_price'] ?? 0;
    $expiry = $_POST['expiry'] ?? null;
    $reason = trim($_POST['reason'] ?? 'No justification provided');
    $productId = $_POST['product_id'] ?? null;

    // Update batch
    if ($this->productModel->updateBatch($batchId, $stock, $buy_price, $sell_price, $expiry)) {

        // Handle damaged quantity
// Handle damaged quantity
$damagedQty = (float)($_POST['damaged_qty'] ?? 0);
if ($damagedQty > 0) {
    // Deduct only from batch
    $this->productModel->deductBatchStock($batchId, $damagedQty);

    // Insert into damaged items table
    $this->productModel->insertDamagedItem($productId, $batchId, $damagedQty, $reason);

    $this->logAction(
        "Damaged Batch",
        "Damaged {$damagedQty} units for batch ID {$batchId} (Product ID {$productId})"
    );
}


        $this->logAction("Edit Batch", "Edited batch ID {$batchId}");
        $_SESSION['flash_message'] = "Batch updated successfully";
        $_SESSION['flash_type'] = "success";
    }
    header("Location: /products#batch_{$batchId}");
    exit();
}

    public function restoreBatch() {
    $batchId = $_POST['batch_id'] ?? null;
    if ($batchId && $this->productModel->restoreBatch($batchId)) {
        $this->logAction("Restore Batch", "Restored batch ID {$batchId}");
        $_SESSION['flash_message'] = "Batch restored successfully";
        $_SESSION['flash_type'] = "success";
    }
    header("Location: /views/settings.php");
    exit();
}

public function getExpiredProducts() {
    return $this->productModel->getExpiredProducts();
}

public function getExpiredProductsWithBatchNumber() {
    $allProducts = $this->index(); // get all products including batches
    $today = date('Y-m-d');
    $expiredProducts = [];

    foreach ($allProducts as $product) {
        // Check main product expiry
        if (!empty($product['expiry']) && $product['expiry'] <= $today) {
            $expiredProducts[] = [
                'product_id' => $product['id'],
                'description' => $product['description'],
                'batch_number' => null,
                'stock' => $product['stock'],
                'expiry' => $product['expiry']
            ];
        }

        // Check batches
        $batchNumber = 2;
        foreach ($product['batches'] as $batch) {
            if (!empty($batch['expiry']) && $batch['expiry'] <= $today) {
                $expiredProducts[] = [
                    'product_id' => $product['id'],
                    'description' => $product['description'],
                    'batch_number' => $batchNumber,
                    'stock' => $batch['stock'],
                    'expiry' => $batch['expiry']
                ];
            }
            $batchNumber++;
        }
    }

    return $expiredProducts;
}



public function reorderStock() {
    $reorderData = $_POST['reorder_qty'] ?? [];

    foreach ($reorderData as $productId => $items) {
        $product = $this->productModel->getProductById($productId);
        if (!$product) continue;

        foreach ($items as $batchId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) continue; // skip invalid input

            // If batchId is 'main', update product stock directly
            if ($batchId === 'main') {
                $currentStock = $product['stock'] ?? 0;
                $newStock = $currentStock + $qty;

                $this->productModel->updateProductStockById($productId, $newStock);

                $this->logAction(
                    "Reorder Stock",
                    "Reordered {$qty} units for main product ID {$productId} ({$product['description']})"
                );
            } else {
                // Fetch batch by batch_id
                $batch = $this->productModel->getBatchById($batchId);

if ($batch) {
    $newStock = $batch['stock'] + $qty;

    $this->productModel->updateBatch(
        $batchId,
        $newStock,
        $batch['buy_price'],
        $batch['sell_price'],
        $batch['expiry']
    );

    $batchNumber = isset($batch['batch_number']) ? $batch['batch_number'] : 'N/A';

    $this->logAction(
        "Reorder Stock",
        "Reordered {$qty} units for batch ID {$batchId} (Batch #{$batchNumber}) of product ID {$productId} ({$product['description']})"
    );
} else {
    // Insert new batch with proper batch number
    $newBatchId = $this->productModel->insertBatch(
        $productId,
        $qty,
        $product['buy_price'],
        $product['sell_price'],
        date("Y-m-d", strtotime("+30 days")) // default expiry
    );

    // Fetch the new batch to get its batch_number for logging
    $newBatch = $this->productModel->getBatchById($newBatchId);
    $batchNumber = $newBatch['batch_number'] ?? 'N/A';

    $this->logAction(
        "Reorder Stock",
        "Created new batch (Batch #{$batchNumber}) and reordered {$qty} units for product ID {$productId} ({$product['description']})"
    );
}

            }
        }
    }

    $_SESSION['flash_message'] = "Stock reordered successfully";
    $_SESSION['flash_type'] = "success";
    header('Location: /products');
    exit();
}
}

?>
