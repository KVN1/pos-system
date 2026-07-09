<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once '../database.php';
$conn = Database::getConnection();

$nearExpiryDays = 5;
$currentDate = date('Y-m-d');

// Fetch all main products
$mainProducts = $conn->query("SELECT id, description, category, stock FROM products ORDER BY description ASC")
                     ->fetchAll(PDO::FETCH_ASSOC);

// Fetch all batches
$batches = $conn->query("SELECT batch_id, product_id, stock, expiry, batch_number FROM product_batches ORDER BY product_id, expiry ASC")
                ->fetchAll(PDO::FETCH_ASSOC);

// Prepare Near Expiry / Expired items
$expiryItems = [];
foreach ($mainProducts as $product) {
    $productBatches = array_filter($batches, fn($b) => $b['product_id'] == $product['id']);
    foreach ($productBatches as $batch) {
        if (!$batch['expiry']) continue;
        $isExpired = ($batch['expiry'] < $currentDate);
        $daysDiff = (strtotime($batch['expiry']) - strtotime($currentDate)) / (60*60*24);
        if ($isExpired || $daysDiff <= $nearExpiryDays) {
            $expiryItems[] = [
                'product' => $product['description'],
                'category' => $product['category'],
                'batch' => $batch['batch_number'],
                'expiry' => $batch['expiry'],
                'statusClass' => $isExpired ? 'status-red' : 'status-orange',
                'statusText' => $isExpired ? 'Expired' : 'Expiring Soon'
            ];
        }
    }
}

// Prepare Low / Out of Stock items (≤20)
$lowStockItems = [];
foreach ($mainProducts as $product) {
    $productBatches = array_filter($batches, fn($b) => $b['product_id'] == $product['id']);
    $totalStock = (int)$product['stock'];

    // Include main product only if stock ≤ 20
    if ($totalStock <= 20) {
        $statusText = $totalStock === 0 ? 'Out of Stock' : ($totalStock <= 10 ? 'Low Stock' : 'Near Out of Stock');
        $statusClass = $totalStock <= 10 ? 'status-red' : 'status-orange';
        $lowStockItems[] = [
            'product' => $product['description'],
            'category' => $product['category'],
            'batch' => '-',
            'stock' => $totalStock,
            'statusClass' => $statusClass,
            'statusText' => $statusText
        ];
    }

    // Add batches with stock ≤ 20
    foreach ($productBatches as $batch) {
        $bStock = (int)$batch['stock'];
        if ($bStock <= 20) {
            $bStatusText = $bStock === 0 ? 'Out of Stock' : ($bStock <= 10 ? 'Low Stock' : 'Near Out of Stock');
            $bStatusClass = $bStock <= 10 ? 'status-red' : 'status-orange';
            $lowStockItems[] = [
                'product' => $product['description'] . ' (Batch)',
                'category' => $product['category'],
                'batch' => $batch['batch_number'],
                'stock' => $bStock,
                'statusClass' => $bStatusClass,
                'statusText' => $bStatusText
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Product Notifications</title>
<link rel="stylesheet" href="/POSu/styles/stylee.css">
<link rel="stylesheet" href="/POSu/styles/notif-style.css">
<style>
body { font-family: Arial, sans-serif; }
.status-green { color: green; font-weight: bold; }
.status-orange { color: orange; font-weight: bold; }
.status-red { color: red; font-weight: bold; }
.search-input { margin-bottom: 10px; padding: 5px; width: 250px; }
table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
table, th, td { border: 1px solid #ddd; }
th, td { padding: 8px; text-align: left; }
th { background: #f2f2f2; }
.pagination { margin: 10px 0; display: flex; gap: 5px; flex-wrap: wrap; }
.pagination button { padding: 5px 10px; border: 1px solid #ccc; background: #fff; cursor: pointer; border-radius: 4px; }
.pagination button.active { background: #007bff; color: #fff; border-color: #007bff; }
</style>
</head>
<body>
<?php include '../includes/sidebar.php'; ?>

<div class="main-content">
    <h1>Product Notifications</h1>
    <p>Shows products that are near expiry, expired, or low/out of stock</p>

    <!-- Near Expiry / Expired -->
    <div class="notification-section">
        <h2>Near Expiry / Expired Products</h2>
        <div style="display:flex; gap:10px; margin-bottom:10px;">
            <button class="btn-download" onclick="downloadCSV('expiryTable','expiry')">Download CSV</button>
            <button class="btn-download" onclick="exportPDF('expiryTable','expiry')">Export PDF</button>
            <button class="btn-download" onclick="printTable('expiryTable')">Print</button>
        </div>
        <input type="text" id="searchExpiry" class="search-input" placeholder="Search by name or category..." onkeyup="filterTable('expiryTable', 'searchExpiry')">
        <table id="expiryTable">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Batch</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expiryItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= htmlspecialchars($item['batch']) ?></td>
                        <td><?= htmlspecialchars($item['expiry']) ?></td>
                        <td class="<?= $item['statusClass'] ?>"><?= $item['statusText'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination" id="expiryPagination"></div>
    </div>

    <!-- Low / Out of Stock -->
    <div class="notification-section">
        <h2>Low / Out of Stock Products</h2>
        <div style="display:flex; gap:10px; margin-bottom:10px;">
            <button class="btn-download" onclick="downloadCSV('stockTable','stock')">Download CSV</button>
            <button class="btn-download" onclick="exportPDF('stockTable','stock')">Export PDF</button>
            <button class="btn-download" onclick="printTable('stockTable')">Print</button>
        </div>
        <input type="text" id="searchStock" class="search-input" placeholder="Search by name or category..." onkeyup="filterTable('stockTable', 'searchStock')">
        <table id="stockTable">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Batch</th>
                    <th>Stock Quantity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lowStockItems as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product']) ?></td>
                        <td><?= htmlspecialchars($item['category']) ?></td>
                        <td><?= htmlspecialchars($item['batch']) ?></td>
                        <td><?= $item['stock'] ?></td>
                        <td class="<?= $item['statusClass'] ?>"><?= $item['statusText'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination" id="stockPagination"></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

<script>
// --- CSV Download ---
function downloadCSV(tableId, type) {
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    let csv = "data:text/csv;charset=utf-8,";
    
    if(type==='expiry') csv += "Product Name,Category,Batch,Expiry Date,Status\n";
    else csv += "Product Name,Category,Batch,Stock Quantity,Status\n";

    rows.forEach(row => {
        if(row.style.display === "none") return;
        const cols = Array.from(row.querySelectorAll("td")).map(td => td.textContent.trim());
        csv += cols.join(",") + "\n";
    });

    const encodedUri = encodeURI(csv);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", type + "_products.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// --- PDF Export ---
function exportPDF(tableId, type) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    const headers = [];
    const body = [];

    document.querySelectorAll(`#${tableId} thead th`).forEach(th => headers.push(th.textContent));
    document.querySelectorAll(`#${tableId} tbody tr`).forEach(row => {
        if(row.style.display === "none") return;
        const cols = Array.from(row.querySelectorAll("td")).map(td => td.textContent.trim());
        body.push(cols);
    });

    doc.autoTable({
        head: [headers],
        body: body,
        startY: 20,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [100,100,255] }
    });

    doc.save(type + "_products.pdf");
}

// --- Print Table ---
function printTable(tableId) {
    const table = document.getElementById(tableId).outerHTML;
    const newWin = window.open("");
    newWin.document.write("<html><head><title>Print</title></head><body>");
    newWin.document.write(table);
    newWin.document.write("</body></html>");
    newWin.document.close();
    newWin.print();
}
</script>


<script>
// Search filter
function filterTable(tableId, inputId) {
    const input = document.getElementById(inputId).value.toLowerCase();
    const rows = document.querySelectorAll(`#${tableId} tbody tr`);
    rows.forEach(row => {
        const text = (row.cells[0].textContent + row.cells[1].textContent).toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}

// Client-side pagination
function setupPagination(tableId, paginationId) {
    const rows = Array.from(document.querySelectorAll(`#${tableId} tbody tr`));
    const perPage = 10;
    let currentPage = 1;
    const paginationDiv = document.getElementById(paginationId);

    function showPage(page) {
        currentPage = page;
        rows.forEach((row, i) => {
            row.style.display = (i >= (page-1)*perPage && i < page*perPage) ? '' : 'none';
        });

        paginationDiv.innerHTML = '';
        const totalPages = Math.ceil(rows.length / perPage);
        if (totalPages <= 1) return;
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.classList.toggle('active', i === currentPage);
            btn.onclick = () => showPage(i);
            paginationDiv.appendChild(btn);
        }
    }

    showPage(1);
}

document.addEventListener('DOMContentLoaded', () => {
    setupPagination('expiryTable', 'expiryPagination');
    setupPagination('stockTable', 'stockPagination');
});
</script>
</body>
</html>
