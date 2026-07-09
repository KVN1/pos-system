<?php
require_once __DIR__ . '/../database.php';

class ProductModel {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getProductById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE status='active' ORDER BY date_added DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getAllProductsDescription() {
    $stmt = $this->conn->prepare("SELECT * FROM products ORDER BY description ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function insertProduct($code, $description, $category, $perishability, $stock, $unit, $buy_price, $sell_price, $date_added, $expiry) {
        $stmt = $this->conn->prepare("
            INSERT INTO products 
            (code, description, category, perishability, stock, unit, buy_price, sell_price, date_added, expiry, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ");
        return $stmt->execute([$code, $description, $category, $perishability, $stock, $unit, $buy_price, $sell_price, $date_added, $expiry]);
    }

    public function updateProduct($id, $code, $description, $category, $perishability, $stock, $unit, $buy_price, $sell_price, $expiry) {
        $stmt = $this->conn->prepare("
            UPDATE products SET 
                code=?, description=?, category=?, perishability=?, stock=?, unit=?, buy_price=?, sell_price=?, expiry=?
            WHERE id=?
        ");
        return $stmt->execute([$code, $description, $category, $perishability, $stock, $unit, $buy_price, $sell_price, $expiry, $id]);
    }

    public function getProductStock($id) {
        $stmt = $this->conn->prepare("SELECT stock FROM products WHERE id=?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ? $product['stock'] : 0;
    }

    public function updateProductStockById($id, $stock) {
        $stmt = $this->conn->prepare("UPDATE products SET stock=? WHERE id=?");
        return $stmt->execute([$stock, $id]);
    }

    public function deactivateProduct($id) {
        $stmt = $this->conn->prepare("UPDATE products SET status='archived' WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function activateProduct($id) {
        $stmt = $this->conn->prepare("UPDATE products SET status='active' WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getActiveProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE status='active' ORDER BY date_added DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getArchivedProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE status='archived' ORDER BY date_added DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function getArchivedBatches() {
    $sql = "
        SELECT 
            b.batch_id,
            b.product_id,
            b.stock,
            b.buy_price,
            b.sell_price,
            b.expiry,
            b.date_added,
            b.status,
            p.description
        FROM product_batches b
        LEFT JOIN products p ON p.id = b.product_id
        WHERE b.status = 'archived'
        ORDER BY b.date_added DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getActiveBatchesByProductId($productId) {
    $stmt = $this->conn->prepare("
        SELECT batch_id, product_id, stock, buy_price, sell_price, expiry, batch_number
        FROM product_batches
        WHERE product_id = ? AND status = 'active'
        ORDER BY batch_id ASC
    ");
    $stmt->execute([$productId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




public function getActiveBatches() {
    $stmt = $this->conn->prepare("SELECT * FROM product_batches WHERE status='active'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getLowStockProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE status='active' AND stock <= 10 ORDER BY stock ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNearExpiryProducts() {
        $today = date('Y-m-d');
        $stmt = $this->conn->prepare("
            SELECT * FROM products 
            WHERE status='active' AND expiry <= DATE_ADD(:today, INTERVAL 7 DAY)
            ORDER BY expiry ASC
        ");
        $stmt->execute(['today' => $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $stmt = $this->conn->prepare("SELECT category_name FROM categories");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductBySearch($search) {
        $stmt = $this->conn->prepare("
            SELECT code, description, sell_price, perishability 
            FROM products 
            WHERE code LIKE :s OR description LIKE :s OR perishability LIKE :s
        ");
        $stmt->execute(['s' => "%$search%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductsByCategoryName($category) {
    $stmt = $this->conn->prepare("SELECT * FROM products WHERE category=?");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProductByCode($code) {
    $stmt = $this->conn->prepare("SELECT * FROM products WHERE code = ?");
    $stmt->execute([$code]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function increaseStock($code, $qty) {
    $stmt = $this->conn->prepare("UPDATE products SET stock = stock + ? WHERE code = ?");
    return $stmt->execute([$qty, $code]);
}
public function decreaseStock($code, $qty) {
    $stmt = $this->conn->prepare("UPDATE products SET stock = stock - ? WHERE code = ?");
    return $stmt->execute([$qty, $code]);
}



public function deleteProduct($id) {
        $stmt = $this->conn->prepare("DELETE FROM products WHERE id=?");
        return $stmt->execute([$id]);
    }

public function getAllProductsWithBatches() {
    $sql = "SELECT 
        p.*, 
        b.batch_id, 
        b.batch_number,
        b.stock AS batch_stock, 
        b.buy_price AS batch_buy_price, 
        b.sell_price AS batch_sell_price, 
        b.expiry AS batch_expiry,
        b.date_added AS batch_date_added
    FROM products p
    LEFT JOIN product_batches b 
        ON p.id = b.product_id 
        AND b.status = 'active'
    ORDER BY p.description ASC, b.batch_number ASC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



   public function updateBatch($batch_id, $stock, $buy_price, $sell_price, $expiry) {
        $stmt = $this->conn->prepare("
            UPDATE product_batches SET stock=?, buy_price=?, sell_price=?, expiry=? WHERE batch_id=?
        ");
        return $stmt->execute([$stock, $buy_price, $sell_price, $expiry, $batch_id]);
    }

    public function getLastInsertedId() {
    return $this->conn->lastInsertId();
}


public function deleteBatch($batch_id) {
    $stmt = $this->conn->prepare("DELETE FROM product_batches WHERE batch_id=?");
    return $stmt->execute([$batch_id]);
}
// In ProductModel.php
public function archiveBatch($batchId) {
    $stmt = $this->conn->prepare("UPDATE product_batches SET status='archived' WHERE batch_id=?");
    return $stmt->execute([$batchId]);
}
public function restoreBatch($batchId) {
    $sql = "UPDATE product_batches SET status = 'active' WHERE batch_id = :batch_id";
    $stmt = $this->conn->prepare($sql);
    return $stmt->execute(['batch_id' => $batchId]);
}


public function insertBatch($productId, $stock, $buyPrice, $sellPrice, $expiry) {
    // Get highest batch number for this product
    $stmt = $this->conn->prepare("SELECT MAX(batch_number) as max_batch FROM product_batches WHERE product_id = ?");
    $stmt->execute([$productId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $batchNumber = ($row['max_batch'] ?? 1) + 1; // start from 2 if no batch exists

    $stmt = $this->conn->prepare("
        INSERT INTO product_batches 
        (product_id, stock, buy_price, sell_price, expiry, date_added, status, batch_number)
        VALUES (?, ?, ?, ?, ?, NOW(), 'active', ?)
    ");
    $stmt->execute([$productId, $stock, $buyPrice, $sellPrice, $expiry, $batchNumber]);

    return $this->conn->lastInsertId();
}



public function deductProductStock($productId, $qty) {
    $stmt = $this->conn->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");
    return $stmt->execute(['qty' => $qty, 'id' => $productId]);
}

public function deductBatchStock($batchId, $qty) {
    $stmt = $this->conn->prepare("UPDATE product_batches SET stock = stock - :qty WHERE batch_id = :batch_id");
    return $stmt->execute(['qty' => $qty, 'batch_id' => $batchId]);
}

public function insertDamagedItem($productId, $batchId, $qty, $reason) {
    $stmt = $this->conn->prepare("
        INSERT INTO damaged_items (product_id, batch_id, quantity, reason, date_added)
        VALUES (:product_id, :batch_id, :quantity, :reason, :date_added)
    ");
    $stmt->execute([
        'product_id' => $productId,
        'batch_id' => $batchId,
        'quantity' => $qty,
        'reason' => $reason,
        'date_added' => date('Y-m-d H:i:s')
    ]);
}

// ProductModel.php — add this method
public function getDamagedItems() {
    $sql = "
        SELECT di.*, p.description AS product_description, b.batch_id, b.date_added AS batch_date_added
        FROM damaged_items di
        LEFT JOIN products p ON p.id = di.product_id
        LEFT JOIN product_batches b ON b.batch_id = di.batch_id
        ORDER BY di.date_added DESC
    ";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    $damaged = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($damaged)) return [];

    // Collect product IDs
    $productIds = array_values(array_unique(array_filter(array_column($damaged, 'product_id'))));

    // Fetch active batches for these products (FIFO)
    $batchesByProduct = [];
    if (!empty($productIds)) {
        $in = str_repeat('?,', count($productIds) - 1) . '?';
        $sql2 = "SELECT batch_id, product_id, date_added FROM product_batches WHERE product_id IN ($in) AND status='active' ORDER BY product_id, date_added ASC";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->execute($productIds);
        $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $r) {
            $batchesByProduct[$r['product_id']][] = $r;
        }
    }

    $result = [];
    foreach ($damaged as $d) {
        $productId = $d['product_id'] ?? null;
        $batchId = $d['batch_id'] ?? null;
        $batchLabel = '-';

        if ($batchId && $productId && isset($batchesByProduct[$productId])) {
    // Find the index of this batch in the current active batches
    $idx = null;
    foreach ($batchesByProduct[$productId] as $i => $b) {
        if ((string)$b['batch_id'] === (string)$batchId) {
            $idx = $i + 1; // 1-based batch number
            break;
        }
    }
    if ($idx !== null) {
        $batchLabel = "Batch " . ($idx + 1); // shift by +1
    } else {
        $batchLabel = "Batch (ID {$batchId})"; // batch no longer active
    }
}


        $result[] = [
            'damaged_id' => $d['id'] ?? null,
            'product_id' => $productId,
            'product_description' => $d['product_description'] ?? 'Unknown',
            'batch_id' => $batchId,
            'batch_label' => $batchLabel,
            'quantity' => $d['quantity'],
            'reason' => $d['reason'] ?? '',
            'date_added' => $d['date_added'] ?? null
        ];
    }

    return $result;
}

public function getBatchByDetails($productId, $expiry, $buy_price, $sell_price) {
    $stmt = $this->conn->prepare("
        SELECT * FROM product_batches
        WHERE product_id = ?
          AND buy_price = ?
          AND sell_price = ?
          AND (expiry = ? OR (expiry IS NULL AND ? = ''))
          AND status='active'
        LIMIT 1
    ");
    $stmt->execute([$productId, $buy_price, $sell_price, $expiry, $expiry]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getBatchById($batchId) {
    $stmt = $this->conn->prepare("SELECT *, batch_number FROM product_batches WHERE batch_id = :batch_id AND status='active'");
    $stmt->execute(['batch_id' => $batchId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}




public function getBatchByProductAndExpiry($productId, $expiry) {
    $stmt = $this->conn->prepare("SELECT * FROM product_batches WHERE product_id = ? AND expiry = ? AND status='active'");
    $stmt->execute([$productId, $expiry]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function increaseBatchStock($batchId, $qty) {
    $stmt = $this->conn->prepare("UPDATE product_batches SET stock = stock + ? WHERE batch_id = ?");
    return $stmt->execute([$qty, $batchId]);
}

public function deleteDamagedItem($damagedId) {
    $stmt = $this->conn->prepare("DELETE FROM damaged_items WHERE id = ?");
    return $stmt->execute([$damagedId]);
}
public function getExpiredProducts() {
    $today = date('Y-m-d');

    // Main products that have expired
    $sqlMain = "SELECT id AS product_id, description, stock, buy_price, sell_price, expiry
                FROM products
                WHERE status='active' AND expiry IS NOT NULL AND expiry < :today";

    $stmtMain = $this->conn->prepare($sqlMain);
    $stmtMain->execute(['today' => $today]);
    $mainProducts = $stmtMain->fetchAll(PDO::FETCH_ASSOC);

    // Expired batches
    $sqlBatches = "SELECT b.batch_id, b.product_id, b.stock, b.buy_price, b.sell_price, b.expiry, p.description
                   FROM product_batches b
                   LEFT JOIN products p ON p.id = b.product_id
                   WHERE b.status='active' AND b.expiry IS NOT NULL AND b.expiry < :today";

    $stmtBatches = $this->conn->prepare($sqlBatches);
    $stmtBatches->execute(['today' => $today]);
    $expiredBatches = $stmtBatches->fetchAll(PDO::FETCH_ASSOC);

    // Merge main products and expired batches into a single array
    $result = [];

    foreach ($mainProducts as $p) {
        $result[] = [
            'product_id' => $p['product_id'],
            'batch_id'   => null,
            'description'=> $p['description'],
            'stock'      => $p['stock'],
            'buy_price'  => $p['buy_price'],
            'sell_price' => $p['sell_price'],
            'expiry'     => $p['expiry'],
            'source'     => 'main'
        ];
    }

    foreach ($expiredBatches as $b) {
        $result[] = [
            'product_id' => $b['product_id'],
            'batch_id'   => $b['batch_id'],
            'description'=> $b['description'],
            'stock'      => $b['stock'],
            'buy_price'  => $b['buy_price'],
            'sell_price' => $b['sell_price'],
            'expiry'     => $b['expiry'],
            'source'     => 'batch'
        ];
    }

    // Sort by expiry ascending
    usort($result, function($a, $b) {
        return strtotime($a['expiry']) - strtotime($b['expiry']);
    });

    return $result;
}



}

?>
