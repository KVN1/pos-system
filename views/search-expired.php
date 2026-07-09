<?php
require_once __DIR__ . '/../ProductController.php';
$pc = new ProductController();

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = $pc->getExpiredProducts(); // returns all expired

if ($search !== '') {
    $search = strtolower($search);
    $products = array_filter($products, function($p) use ($search) {
        $descMatch = strpos(strtolower($p['description']), $search) !== false;

        // handle null batch_id
        $batchLabel = isset($p['batch_id']) && $p['batch_id'] !== null ? 'Batch ' . $p['batch_id'] : '';
        $batchMatch = strpos(strtolower($batchLabel), $search) !== false;

        return $descMatch || $batchMatch;
    });
}

// Return JSON
header('Content-Type: application/json');
echo json_encode(array_values($products));
exit;
