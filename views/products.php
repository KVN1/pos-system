<?php
if (session_status() === PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
}
include 'includes/header.php';
require_once 'controllers/ProductController.php';

$productController = new ProductController();
$products = $productController->index(); // returns all products
$categories = $productController->getCategories();
$damagedItems = $productController->getDamagedItems(); 

$lowStockItems = [];
foreach ($products as $product) {
    // fetch batches per product
    $batches = $productController->getBatchesByProductId($product['id']); // <-- add this method
    $product['batches'] = $batches;

    $mainLowStock = $product['stock'] <= 20;

    // Include main product if low
    if ($mainLowStock) {
        $lowStockItems[] = [
            'product_id' => $product['id'],
            'batch_id' => 'main',
            'description' => $product['description'],
            'stock' => $product['stock']
        ];
    }

    // Include low batches
    foreach ($batches as $b) {
        if ($b['stock'] <= 20) {
            $batchNumber = $b['batch_number'] ?? 'N/A';
            $lowStockItems[] = [
                'product_id' => $product['id'],
                'batch_id' => $b['batch_id'],
                'description' => $product['description'] . " (Batch $batchNumber)",
                'stock' => $b['stock']
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
    <title>Products - POS</title>
    <link rel="stylesheet" href="/styles/stylee.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="/styles/prodstyles.css?v=<?= time(); ?>">
    <style>
        .legend-container { margin: 20px 0; display: flex; gap: 40px; flex-wrap: wrap; }
        .legend-section { background: #f9f9f9; border: 1px solid #ccc; border-radius: 10px; padding: 15px 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .legend-section h3 { margin-bottom: 10px; font-size: 1rem; }
        .legend-item { display: flex; align-items: center; margin-bottom: 6px; }
        .color-box { width: 18px; height: 18px; border-radius: 4px; margin-right: 10px; border: 1px solid #aaa; }
        .color-green { background-color: #66cc66; }
        .color-orange { background-color: #ffcc66; }
        .color-red { background-color: #ff6666; }
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            resize: vertical;
            transition: border-color 0.2s ease;
        }
        textarea:focus {
            border-color: #4CAF50;
            outline: none;
        }

        /* Radio buttons styled like checkboxes */
.unit-options {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}
.unit-label {
    flex: 1;
    text-align: center;
    padding: 10px 0;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    background-color: #e0e5d6;
    color: #333;
    border: 2px solid transparent;
    user-select: none;
}
.unit-label input[type="radio"] {
    display: none;
}
.unit-label span {
    display: block;
    width: 100%;
    height: 100%;
    border-radius: 6px;
    transition: all 0.3s ease;
}
.unit-label input[type="radio"]:checked + span {
    background-color: #344F1F;
    color: #fff;
    border-color: #2c4016;
}
.unit-label:hover span {
    background-color: #d3dac2;
}


.batch-hidden {
    display: none;
}


    </style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>


    <script>
function openEditBatchModal(batchId, productId, stock, buyPrice, sellPrice, expiry) {
    document.getElementById('modal_batch_id').value = batchId;
    document.getElementById('modal_product_id').value = productId;
    document.getElementById('modal_stock').value = stock;
    document.getElementById('modal_buy_price').value = buyPrice;
    document.getElementById('modal_sell_price').value = sellPrice;
    document.getElementById('modal_expiry').value = expiry;

    document.getElementById('editBatchModal').style.display = 'flex';
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.style.display = 'none';

    // Only for add/edit product modals
    if (modalId === 'addModal' || modalId === 'editModal') {
        modal.querySelectorAll('input').forEach(input => {
            if (input.type !== 'hidden') input.value = '';
        });
        modal.querySelectorAll('textarea').forEach(t => t.value = '');
        modal.querySelectorAll('select').forEach(sel => {
            sel.value = '';
            sel.disabled = false;
        });
        modal.querySelectorAll('input[type="radio"]').forEach(r => {
    r.checked = false;
    r.disabled = false;
    const span = r.closest('.unit-label')?.querySelector('span');
    if (span) {
        span.style.backgroundColor = '';
        span.style.color = '';
        span.style.borderColor = '';
    }
});

    }
}


        function openAddModal() {
            document.getElementById("addModal").style.display = "flex";
        }
function filterProducts() {
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll(".products-table tbody tr");

    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll("td"));
        const match = cells.some(cell => cell.textContent.toLowerCase().includes(input));
        row.style.display = match ? "" : "none";
    });
}


function openEditModal(
    id, code, description, category, perishability,
    stock, unit, buy_price, sell_price, expiry, date_added, batch_id = null, type = ''
) {
    // Show modal
    document.getElementById("editModal").style.display = "flex";

    // Fill text/number inputs
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_code").value = code;
    document.getElementById("edit_description").value = description;
    document.getElementById("edit_stock").value = stock;
    document.getElementById("edit_buy_price").value = buy_price;
    document.getElementById("edit_sell_price").value = sell_price;
    document.getElementById("edit_expiry").value = expiry;
    document.getElementById("edit_date_added").value = date_added;

    // Optional batch ID
    if (batch_id !== null) {
        document.getElementById("edit_batch_id").value = batch_id;
    }

    // Select dropdowns
    document.getElementById("edit_category").value = category;
    document.getElementById("edit_perishability").value = perishability;
    if (type) document.getElementById("edit_type").value = type;

    // Select correct unit radio
    const unitRadios = document.querySelectorAll('#editProductForm input[name="unit"]');
    unitRadios.forEach(radio => {
        radio.checked = (radio.value === unit);
    });

    // Clear reason field
    document.getElementById("edit_reason").value = "";
}



    function downloadFilteredCSV() {
        const table = document.querySelector(".products-table table");
        const rows = table.querySelectorAll("tbody tr");
        let csvContent = '';
        const headers = table.querySelectorAll("thead th");
        let headerArr = [];
        headers.forEach((h,index) => { if(h.textContent.trim()!=="Actions") headerArr.push('"' + h.textContent.trim() + '"'); });
        csvContent += headerArr.join(",") + "\n";
        rows.forEach(row => {
            if(row.style.display==="none") return;
            const cells = row.querySelectorAll("td");
            let rowArr=[];
            cells.forEach((cell,index)=>{ if(headers[index].textContent.trim()!=="Actions") rowArr.push('"' + cell.textContent.trim().replace(/"/g,'""') + '"'); });
            csvContent += rowArr.join(",") + "\n";
        });
        const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "filtered_products.csv");
        link.style.visibility="hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Title
    doc.setFontSize(14);
    doc.text("Products Inventory", 14, 15);

    // Table headers
    const headers = [];
    document.querySelectorAll(".products-table thead th").forEach(th => {
        if(th.textContent.trim() !== "Actions") headers.push(th.textContent.trim());
    });

    // Table data
    const data = [];
    document.querySelectorAll(".products-table tbody tr").forEach(row => {
        if(row.style.display === "none") return; // skip filtered out
        const rowData = [];
        row.querySelectorAll("td").forEach((td, i) => {
            if(headers[i]) {
                // Remove currency symbols and extra spaces for clean PDF numbers
                let cellText = td.textContent.trim();
                rowData.push(cellText); 
            }
        });
        data.push(rowData);
    });

    // Add table
    doc.autoTable({
        head: [headers],
        body: data,
        startY: 25,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [100, 100, 255] },
        theme: 'grid',
    });

    doc.save("products_inventory.pdf");
}


    </script>
</head>
<body>

<?php if (isset($_SESSION['flash_message'])): ?>
<div class="flash-message <?= htmlspecialchars($_SESSION['flash_type'] ?? 'success'); ?>">
    <?= htmlspecialchars($_SESSION['flash_message']); ?>
</div>
<script>
document.querySelectorAll('.expand-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.querySelectorAll('.batch-' + id).forEach(r => {
            r.style.display = (r.style.display === 'none' || r.style.display === '') ? 'table-row' : 'none';
        });
        this.textContent = (this.textContent === "▶") ? "▼" : "▶";
    });
});


</script>
<style>
.flash-message {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: bold;
    z-index: 9999;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.flash-message.error { background-color: #f44336; }
</style>
<?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
<?php endif; ?>


<div class="dashboard">
<?php include 'includes/sidebar.php'; ?>
<main class="main-content">
    <header class="header">
        <h1>Products</h1>
        <p>Manage your product inventory efficiently.</p>
        <div class="actions">
<div class="actions" style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
    <button class="btn-download" onclick="downloadFilteredCSV()">Download CSV</button>
    <button class="btn-download" onclick="exportPDF()">Export PDF</button>
    <button class="btn-download" onclick="window.print()">Print Page</button>
</div>

    </header>

<div class="top-controls">
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <button class="add-btn" onclick="openAddModal()">+ Add Product</button>
    <?php endif; ?>
    <input type="text" id="searchInput" class="search-bar" placeholder="Search for a product..." onkeyup="filterProducts()">
</div>

<!-- LEGEND -->
<div class="legend-container">
    <div class="legend-section">
        <h3>Stock Level Legend</h3>
        <div class="legend-item"><div class="color-box color-green"></div>21 and above — Sufficient stock</div>
        <div class="legend-item"><div class="color-box color-orange"></div>11–20 — Near out of stock</div>
        <div class="legend-item"><div class="color-box color-red"></div>10 and below — Low stock</div>
    </div>
    <div class="legend-section">
        <h3>Expiry Date Legend</h3>
        <div class="legend-item"><div class="color-box color-green"></div>Not expired</div>
        <div class="legend-item"><div class="color-box color-orange"></div>Expiring soon</div>
        <div class="legend-item"><div class="color-box color-red"></div>Expired</div>
    </div>
</div>

<section class="products-table">
    <h2>Product Inventory</h2>

    <?php if ($_SESSION['role'] === 'admin'): ?>
    <!-- Button container -->
    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin-bottom: 10px;">
        
        <button class="damaged-btn" onclick="openDamagedModal()">Damaged Items</button>

        <div style="position: relative; display: inline-block;">
            <button class="damaged-btn" onclick="openExpiredModal()">Expired Products</button>
            <div id="expiredBadge"
                 style="
                    display:none;
                    position: absolute;
                    top: -10px;
                    right: -10px;
                    width: 22px;
                    height: 22px;
                    background: #d9534f;
                    color: white;
                    border-radius: 50%;
                    font-size: 14px;
                    font-weight: bold;
                    line-height: 22px;
                    text-align: center;
                    box-shadow: 0 0 4px rgba(0,0,0,0.3);
                    pointer-events: none;
                 ">
                !
            </div>
        </div>

        <div style="position: relative; display: inline-block;">
            <button class="reorder-btn" onclick="document.getElementById('reorderModal').style.display='flex'">Low/No Stock</button>
            <?php if(!empty($lowStockItems)): ?>
            <div style="
                    position: absolute;
                    top: -10px;
                    right: -10px;
                    width: 22px;
                    height: 22px;
                    background: #d9534f;
                    color: white;
                    border-radius: 50%;
                    font-size: 14px;
                    font-weight: bold;
                    line-height: 22px;
                    text-align: center;
                    box-shadow: 0 0 4px rgba(0,0,0,0.3);
                    pointer-events: none;
                 ">
                !
            </div>
            <?php endif; ?>
        </div>

    </div>
<?php endif; ?>


    
    <div class="table-container">
        <table class="products-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Perishability</th>
                    <th>Stock</th>
                    <th>Unit</th>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                        <th>Buying Price</th>
                    <?php endif; ?>
                    <th>Selling Price</th>
                    <th>Date and Time Added</th>
                    <th>Expiry Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product):
                    $productId = $product['id'];
                    $batches = $product['batches'] ?? [];
                    $expiryDate = strtotime($product['expiry']);
                    $currentDate = strtotime(date("Y-m-d"));
                    $daysRemaining = floor(($expiryDate - $currentDate) / (60*60*24));

                    $expiryColor = ($daysRemaining < 0) ? '#ff6666' : (($daysRemaining <= 5) ? '#ffcc66' : '#66cc66');
                    $stockColor = ($product['stock'] <= 10) ? '#ff6666' : (($product['stock'] <= 20) ? '#ffcc66' : '#66cc66');
                ?>
                <!-- PRODUCT ROW -->
                <tr class="product-row">
                    <td class="expand-btn" data-id="<?= $productId ?>" style="cursor:pointer; font-weight:bold; text-align:center;">▶</td>
                    <td><?= htmlspecialchars($product['code']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td><?= htmlspecialchars($product['perishability']) ?></td>
                    <td style="background-color: <?= $stockColor ?>;"><?= $product['stock'] ?></td>
                    <td><?= htmlspecialchars($product['unit']) ?></td>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                        <td>₱<?= number_format($product['buy_price'],2) ?></td>
                    <?php endif; ?>
                    <td>₱<?= number_format($product['sell_price'],2) ?></td>
                    <td><?= date("Y-m-d | g:i:s A", strtotime($product['date_added'])) ?></td>
                    <td style="background-color: <?= $expiryColor ?>;"><?= date("m-d-Y", strtotime($product['expiry'])) ?></td>
                    <td>
                            <div style="display:flex; gap:5px;">

<?php if ($_SESSION['role'] === 'admin'): ?>
    <button class="edit-btn" onclick="openEditModal(
        '<?= $product['id'] ?>',
        '<?= htmlspecialchars($product['code']) ?>',
        '<?= htmlspecialchars($product['description']) ?>',
        '<?= htmlspecialchars($product['category']) ?>',
        '<?= htmlspecialchars($product['perishability']) ?>',
        '<?= $product['stock'] ?>',
        '<?= htmlspecialchars($product['unit']) ?>',
        '<?= $product['buy_price'] ?>',
        '<?= $product['sell_price'] ?>',
        '<?= htmlspecialchars($product['expiry']) ?>',
        '<?= $product['date_added'] ?>',
        '<?= $product['batch_id'] ?? '' ?>'
    )">
        Edit
    </button>

    <form method="POST" action="/products" style="display:inline;" 
          onsubmit="return confirm('Are you sure you want to deactivate this product?');">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <button type="submit" class="delete-btn">Deactivate</button>
    </form>
<?php endif; ?>


               <!-- BATCH ROWS -->
<?php foreach($batches as $batch): 

    $batchNumber = $batch['batch_number'] ?? 'N/A';
    $batchExpiryDate = strtotime($batch['expiry']);
    $currentDate = strtotime(date("Y-m-d"));
    $daysRemaining = floor(($batchExpiryDate - $currentDate) / (60*60*24));
    $batchExpiryColor = ($daysRemaining < 0) ? '#ff6666' : (($daysRemaining <= 5) ? '#ffcc66' : '#66cc66');

    $bStock = (int)$batch['stock'];
    $batchStockColor = ($bStock <= 10) ? '#ff6666' : (($bStock <= 20) ? '#ffcc66' : '#66cc66');

?>
<tr class="batch-row batch-<?= $product['id'] ?>" style="display:none; background:#fafafa;">
    <td></td>
    <td>Batch <?= $batchNumber ?></td>
    <td>—</td>
    <td>—</td>
    <td>—</td>
    <td style="background-color: <?= $batchStockColor ?>;"><?= $batch['stock'] ?></td>
    <td><?= htmlspecialchars($product['unit']) ?></td>
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <td>₱<?= number_format($batch['buy_price'],2) ?></td>
    <?php endif; ?>
    <td>₱<?= number_format($batch['sell_price'],2) ?></td>
    <td><?= !empty($batch['date_added']) ? date("Y-m-d | g:i:s A", strtotime($batch['date_added'])) : "—" ?></td>
    <td style="background-color: <?= $batchExpiryColor ?>;"><?= date("m-d-Y", strtotime($batch['expiry'])) ?></td>
    <td>
        <div style="display:flex; gap:5px;">
            <button class="edit-batch-btn" 
                    onclick="openEditBatchModal(
                        '<?= $batch['batch_id'] ?>',
                        '<?= $product['id'] ?>',
                        '<?= $batch['stock'] ?>',
                        '<?= $batch['buy_price'] ?>',
                        '<?= $batch['sell_price'] ?>',
                        '<?= $batch['expiry'] ?>'
                    )">
                Edit
            </button>
            <form method="POST" action="/products" style="display:inline;" 
                  onsubmit="return confirm('Are you sure you want to deactivate this batch?');">
                <input type="hidden" name="action" value="delete_batch">
                <input type="hidden" name="batch_id" value="<?= $batch['batch_id'] ?>">
                <button type="submit" class="delete-btn">Deactivate</button>
            </form>
        </div>
    </td>
</tr>
<?php endforeach; ?>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
document.querySelectorAll('.expand-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const id = this.dataset.id;
        document.querySelectorAll('.batch-' + id).forEach(r => {
            r.style.display = (r.style.display === 'none' || r.style.display === '') ? 'table-row' : 'none';
        });
        this.textContent = (this.textContent === "▶") ? "▼" : "▶";
    });
});
</script>
        </table>
    </div>
</section>
    </main>
</div>
<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>


<!-- Damaged Items Modal -->
<div class="modal-overlay" id="damagedModal">
  <div class="modal-content">
    <h2>Damaged Items</h2>
    <table id="damagedTable">

      <thead>
        <tr>
          <th>Product</th>
          <th>Batch</th>
          <th>Damaged Quantity</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <!-- Will be filled dynamically -->
      </tbody>
    </table>
    <div class="modal-footer">
      <button type="button" class="cancel-btn" onclick="closeModal('damagedModal')">Close</button>
    </div>
  </div>
</div>

<!-- Expired Products Modal -->
<!-- Expired Products Modal -->
<div class="modal-overlay" id="expiredModal" style="display:none;">
  <div class="modal-content" style="max-width: 800px;">
    <h2>Expired Products</h2>

    <table id="expiredTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Product</th>
          <th>Batch</th>
          <th>Quantity</th>
          <th>Expiry Date</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>

    <div id="expiredPagination" class="pagination"></div>

    <div class="modal-footer">
      <button type="button" class="cancel-btn" onclick="closeModal('expiredModal')">Close</button>
    </div>
  </div>
</div>

<script>
const expiredProducts = <?= json_encode($productController->getExpiredProductsWithBatchNumber()); ?>;
let currentPage = 1;
const itemsPerPage = 20;
let filteredProducts = [...expiredProducts];

// Show red badge if expired products exist
if (expiredProducts.length > 0) {
    document.getElementById('expiredBadge').style.display = 'inline-block';
}

// Open modal
function openExpiredModal() {
    currentPage = 1;
    filteredProducts = [...expiredProducts];
    renderExpiredTable();
    document.getElementById('expiredModal').style.display = 'flex';
}

// Close modal
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// Render table
function renderExpiredTable() {
    const tbody = document.querySelector('#expiredTable tbody');
    tbody.innerHTML = '';

    const modalHeader = document.querySelector('#expiredModal h2');
    modalHeader.textContent = `Expired Products (Total: ${filteredProducts.length})`;

    if (filteredProducts.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;">No expired products.</td></tr>`;
        document.getElementById('expiredPagination').innerHTML = '';
        return;
    }

    const start = (currentPage - 1) * itemsPerPage;
    const pageItems = filteredProducts.slice(start, start + itemsPerPage);

    pageItems.forEach((p, i) => {
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.innerHTML = `
            <td>${start + i + 1}</td>
            <td>${p.description || '-'}</td>
            <td>${p.batch_number !== null ? 'Batch ' + p.batch_number : '-'}</td>
            <td>${p.stock || 0}</td>
            <td>${p.expiry || '-'}</td>
        `;

        tr.addEventListener('click', () => {
            closeModal('expiredModal');
            const productRow = document.querySelector(`.product-row[data-id='${p.product_id}']`);
            const batchRows = document.querySelectorAll(`.batch-${p.product_id}`);
            if (productRow) {
                batchRows.forEach(r => r.style.display = '');
                let targetBatch = null;
                batchRows.forEach(r => {
                    if (p.batch_number !== null && r.querySelector('td:nth-child(2)').textContent.trim() === `Batch ${p.batch_number}`) {
                        targetBatch = r;
                    }
                });
                const scrollTarget = targetBatch || productRow;
                scrollTarget.scrollIntoView({behavior:'smooth', block:'center'});
                const originalBg = scrollTarget.style.backgroundColor;
                scrollTarget.style.backgroundColor = '#ffeb99';
                setTimeout(() => scrollTarget.style.backgroundColor = originalBg || '', 1500);
            }
        });

        tbody.appendChild(tr);
    });

    renderPagination();
}

// Pagination
function renderPagination() {
    const paginationDiv = document.getElementById('expiredPagination');
    paginationDiv.innerHTML = '';
    const totalPages = Math.ceil(filteredProducts.length / itemsPerPage);
    if (totalPages <= 1) return;
    for (let i=1; i<=totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = (i === currentPage ? 'active-page' : '');
        btn.onclick = () => { currentPage = i; renderExpiredTable(); };
        paginationDiv.appendChild(btn);
    }
}
</script>






<!-- Add Product Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-content">
        <h2>Add Product</h2>
        <form method="POST" action="/products" id="addProductForm">
            <input type="hidden" name="action" value="add">

            <label>Product Code</label>
            <input type="text" name="code" placeholder="Product Code" required>

            <label>Description</label>
            <input type="text" name="description" placeholder="Description" required>

            <label>Category</label>
            <select name="category" required>
                <option value="" disabled selected>Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['category_name'] ?? ''); ?>">
                        <?= htmlspecialchars($category['category_name'] ?? ''); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Perishability:</label>

            <div style="display: flex; align-items: center; gap: 20px; margin-top: 5px;">

                <label style="display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" name="perishability" value="Perishable" onclick="onlyOne(this)">
                    Perishable
                </label>

                <label style="display: flex; align-items: center; gap: 5px;">
                    <input type="checkbox" name="perishability" value="N/P" checked onclick="onlyOne(this)">
                    N/P
                </label>

            </div>

            <script>
            function onlyOne(checkbox) {
                const checkboxes = document.getElementsByName('perishability');
                checkboxes.forEach((item) => {
                    if (item !== checkbox) item.checked = false;
                });
            }
            </script>

            <label>Stock Level</label>
            <input type="number" name="stock" placeholder="Stock Level" required>

            <label>Unit</label>
            <div class="unit-options">
                <label class="unit-label">
                    <input type="radio" name="unit" value="Kg" required>
                    <span>Kg</span>
                </label>
                <label class="unit-label">
                    <input type="radio" name="unit" value="Piece">
                    <span>Piece</span>
                </label>
                <label class="unit-label">
                    <input type="radio" name="unit" value="Pack">
                    <span>Pack</span>
                </label>
            </div>

            <label>Buying Price</label>
            <input type="number" step="0.01" name="buy_price" placeholder="Buying Price" required>

            <label>Selling Price</label>
            <input type="number" step="0.01" name="sell_price" placeholder="Selling Price" required>

            <label>Expiry Date</label>
            <input type="date" name="expiry" required>

            <button type="submit" data-confirm="Add this product?" class="save-btn">Add</button>
            <button type="button" class="cancel-btn" onclick="closeModal('addModal')">Cancel</button>
        </form>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <h2>Edit Product</h2>
        <form method="POST" action="/products" id="editProductForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="date_added" id="edit_date_added">

            <label>Product Code</label>
            <input type="text" name="code" id="edit_code" placeholder="Enter product code" required>

            <label>Description</label>
            <input type="text" name="description" id="edit_description" placeholder="Enter description" required>

            <label>Category</label>
            <select name="category" id="edit_category" required>
                <option value="" disabled>Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['category_name'] ?? ''); ?>">
                        <?= htmlspecialchars($category['category_name'] ?? ''); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Perishability</label>
            <select name="perishability" id="edit_perishability" required>
                <option value="Perishable">Perishable</option>
                <option value="N/P">N/P</option>
            </select>

            <label>Stock Level</label>
            <input type="number" name="stock" id="edit_stock" placeholder="Enter stock quantity" required>

            <label>Unit</label>
            <div class="unit-options">
                <label class="unit-label">
                    <input type="radio" name="unit" value="Kg" required>
                    <span>Kg</span>
                </label>
                <label class="unit-label">
                    <input type="radio" name="unit" value="Piece">
                    <span>Piece</span>
                </label>
                <label class="unit-label">
                    <input type="radio" name="unit" value="Pack">
                    <span>Pack</span>
                </label>
            </div>

            <label>Buying Price</label>
            <input type="number" step="0.01" name="buy_price" id="edit_buy_price" placeholder="Enter buying price" required>

            <label>Selling Price</label>
            <input type="number" step="0.01" name="sell_price" id="edit_sell_price" placeholder="Enter selling price" required>

            <label>Expiry Date</label>
            <input type="date" name="expiry" id="edit_expiry" min="<?= date('Y-m-d'); ?>" required>

            <label>Reason / Justification</label>
            <textarea name="reason" id="edit_reason" rows="3" placeholder="Explain reason for editing" required></textarea>

            <label>Type</label>
            <select name="type" id="edit_type" required>
                <option value="" disabled selected>Select Type</option>
                <option value="Adjustment">Adjustment</option>
                <option value="Stock Out">Stock Out</option>
                <option value="Stock In">Stock In</option>
                <option value="Damaged">Damaged</option>
            </select>

            <!-- Damaged quantity input (hidden by default) -->
            <label id="damagedLabel" style="display:none;">Damaged Quantity</label>
            <input type="number" name="damaged_qty" id="damaged_qty" style="display:none;" min="1" placeholder="Enter damaged quantity">

            <button type="submit" data-confirm="Edit this product?" class="save-btn">Update</button>
            <button type="button" class="cancel-btn" onclick="closeModal('editModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
    const editType = document.getElementById('edit_type');
    const damagedLabel = document.getElementById('damagedLabel');
    const damagedInput = document.getElementById('damaged_qty');

    editType.addEventListener('change', function() {
        if (this.value === 'Damaged') {
            damagedLabel.style.display = 'block';
            damagedInput.style.display = 'block';
            damagedInput.required = true;
        } else {
            damagedLabel.style.display = 'none';
            damagedInput.style.display = 'none';
            damagedInput.required = false;
            damagedInput.value = '';
        }
    });
</script>

<!-- Reorder Modal -->
<!-- Reorder Modal -->
<div class="modal-overlay" id="reorderModal">
    <div class="modal-content">
        <h2>Low/No Stock (≤20)</h2>

        <form method="POST" action="/products" id="reorderForm">
            <input type="hidden" name="action" value="reorder">

            <table id="reorderTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product / Batch</th>
                        <th>Current Stock</th>
                        <th>Repurchase Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockItems as $i => $item): ?>
                    <tr class="reorder-row" data-index="<?= $i ?>">
                        <td><?= $i + 1 ?></td>
<td>
    <?= htmlspecialchars($item['description']) ?> 
    <?php if(isset($item['batch_number']) && $item['batch_number']): ?>
        (Batch <?= $item['batch_number'] ?>)
        (Main)
    <?php endif; ?>
</td>
                        <td><?= htmlspecialchars($item['stock']) ?></td>
                        <td>
                            <input type="number" class="reorder-qty" name="reorder_qty[<?= $item['product_id'] ?>][<?= $item['batch_id'] ?>]" min="1" placeholder="Quantity">
                        </td>
                        <td>
                            <button type="button" class="repurchase-btn" disabled
                                    data-product="<?= $item['product_id'] ?>"
                                    data-batch="<?= $item['batch_id'] ?>">
                                Repurchase
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">Total low/no stock items: <?= count($lowStockItems) ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- Pagination -->
            <div id="reorderPagination" style="margin-top:10px;"></div>

            <!-- Bulk repurchase -->
            <button type="submit" class="reorder-btn" style="margin-top:10px;">Repurchase Selected</button>
            <button type="button" class="cancel-btn" onclick="closeModal('reorderModal')">Cancel</button>
        </form>
    </div>
</div>

<style>
#reorderPagination button {
    padding: 5px 10px;
    margin-right: 5px;
    border: 1px solid #ccc;
    background-color: #f8f8f8;
    cursor: pointer;
    border-radius: 4px;
}
#reorderPagination button.active {
    font-weight: bold;
    background-color: #d9534f;
    color: white;
}
.repurchase-btn:disabled {
    background-color: #ccc;
    cursor: not-allowed;
}
.repurchase-btn {
    padding: 4px 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.repurchase-btn:hover:not(:disabled) {
    background-color: #218838;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = Array.from(document.querySelectorAll('#reorderTable .reorder-row'));
    const perPage = 10;
    let currentPage = 1;
    const paginationDiv = document.getElementById('reorderPagination');
    const form = document.getElementById('reorderForm');

    // Show page function
    function showPage(page, filteredRows = rows) {
        currentPage = page;
        const start = (page - 1) * perPage;
        const end = start + perPage;
        filteredRows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? '' : 'none';
        });

        // Pagination buttons
        const totalPages = Math.ceil(filteredRows.length / perPage);
        paginationDiv.innerHTML = '';
        if(totalPages <= 1) return;
        for(let i = 1; i <= totalPages; i++){
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = i;
            btn.classList.toggle('active', i === currentPage);
            btn.addEventListener('click', () => showPage(i, filteredRows));
            paginationDiv.appendChild(btn);
        }
    }
    showPage(1);

    // Enable repurchase button only if quantity > 0
    document.querySelectorAll('.reorder-qty').forEach(input => {
        const btn = input.closest('tr').querySelector('.repurchase-btn');
        input.addEventListener('input', () => {
            btn.disabled = !input.value || parseInt(input.value) <= 0;
        });
    });

    // Per-item repurchase (adds to existing stock)
    document.querySelectorAll('.repurchase-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const qtyInput = this.closest('tr').querySelector('.reorder-qty');
            const qty = parseInt(qtyInput.value);
            if(!qty || qty <= 0) return;

            Swal.fire({
                title: "Confirm Repurchase",
                text: `Add ${qty} units to this item?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                zIndex: 99999
            }).then(result => {
                if(result.isConfirmed){
                    const singleForm = document.createElement('form');
                    singleForm.method = 'POST';
                    singleForm.action = '/controllers/ProductController.php';
                    singleForm.innerHTML = `
                        <input type="hidden" name="action" value="reorder">
                        <input type="hidden" name="reorder_qty[${this.dataset.product}][${this.dataset.batch}]" value="${qty}">
                        <input type="hidden" name="add_to_stock" value="1">
                    `;
                    document.body.appendChild(singleForm);
                    singleForm.submit();
                }
            });
        });
    });

    // Bulk repurchase
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const filled = Array.from(form.querySelectorAll('.reorder-qty')).some(input => input.value && parseInt(input.value) > 0);
        if(!filled){
            Swal.fire('Error','Please enter quantity for at least one item','error');
            return;
        }

        Swal.fire({
            title: "Confirm Repurchase",
            text: `Add quantities for selected items?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            zIndex: 99999
        }).then(result => {
            if(result.isConfirmed){
                // Add hidden input to indicate "add" behavior
                const addInput = document.createElement('input');
                addInput.type = 'hidden';
                addInput.name = 'add_to_stock';
                addInput.value = '1';
                form.appendChild(addInput);
                form.submit();
            }
        });
    });
});
</script>





<?php endif; ?>

<!-- Return Damaged Modal -->
<div class="modal-overlay" id="returnDamagedModal">
    <div class="modal-content" style="max-width: 500px;">
        <h2>Return Damaged Item</h2>

        <form id="returnDamagedForm" method="POST" action="/products">

            <input type="hidden" name="action" value="return_damaged">
            <input type="hidden" name="damaged_id" id="return_damaged_id">
            <input type="hidden" name="product_id" id="return_product_id">
            <input type="hidden" name="batch_id" id="return_batch_id">

            <!-- Product -->
            <div style="margin-bottom:15px;">
                <label><strong>Product:</strong></label>
                <div id="return_product" style="padding:8px; background:#f4f4f4; border-radius:5px;"></div>
            </div>

            <!-- Quantity -->
            <div style="margin-bottom:15px;">
                <label><strong>Quantity to Return:</strong></label>
                <input type="number" name="quantity" id="return_qty"
                       min="1" required
                       style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
            </div>

            <!-- Expiry -->
            <div style="margin-bottom:15px;">
                <label><strong>Expiry Date:</strong></label>
                <input type="date" name="expiry" id="return_expiry"
                       style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
            </div>

            <!-- Buy Price -->
            <div style="margin-bottom:15px;">
                <label><strong>Buying Price:</strong></label>
                <input type="number" step="0.01" name="buy_price" id="return_buy_price"
                       style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
            </div>

            <!-- Sell Price -->
            <div style="margin-bottom:15px;">
                <label><strong>Selling Price:</strong></label>
                <input type="number" step="0.01" name="sell_price" id="return_sell_price"
                       style="width:100%; padding:8px; border:1px solid #ccc; border-radius:5px;">
            </div>

            <!-- Buttons -->
            <div style="margin-top:20px; text-align:right;">
                <button type="button" class="cancel-btn"
                        onclick="document.getElementById('returnDamagedModal').style.display='none'">
                    Cancel
                </button>
                <button type="submit" data-confirm="Return to Stock?" class="save-btn">Return to Stock</button>
            </div>

        </form>
    </div>
</div>


<!-- Edit Batch Modal -->
<div class="modal-overlay" id="editBatchModal">
    <div class="modal-content">
        <h2>Edit Batch</h2>
        <form method="POST" action="/products" id="editBatchForm">
            <input type="hidden" name="action" value="edit_batch">
            <input type="hidden" name="batch_id" id="modal_batch_id">
            <input type="hidden" name="product_id" id="modal_product_id">

            <label>Stock</label>
            <input type="number" name="stock" id="modal_stock" placeholder="Enter stock quantity" required>

            <label>Buying Price</label>
            <input type="number" step="0.01" name="buy_price" id="modal_buy_price" placeholder="Enter buying price" required>

            <label>Selling Price</label>
            <input type="number" step="0.01" name="sell_price" id="modal_sell_price" placeholder="Enter selling price" required>

            <label>Expiry Date</label>
            <input type="date" name="expiry" id="modal_expiry" min="<?= date('Y-m-d'); ?>" required>

            <label>Type</label>
            <select name="type" id="modal_type" required>
                <option value="" disabled selected>Select Type</option>
                <option value="Adjustment">Adjustment</option>
                <option value="Stock Out">Stock Out</option>
                <option value="Stock In">Stock In</option>
                <option value="Damaged">Damaged</option>
            </select>

            <!-- Damaged quantity input (hidden by default) -->
            <label id="modal_damaged_label" style="display:none;">Damaged Quantity</label>
            <input type="number" name="damaged_qty" id="modal_damaged_qty" style="display:none;" min="1" placeholder="Enter damaged quantity">

            <label>Reason / Justification</label>
            <textarea name="reason" id="modal_reason" rows="3" placeholder="Explain reason for editing"></textarea>

            <div style="margin-top:15px;">
                <button type="submit" data-confirm="Edit batch?" class="save-btn">Save Changes</button>
                <button type="button" class="cancel-btn" onclick="closeModal('editBatchModal')">Cancel</button>
            </div>
        </form>
    </div>
</div>
<script>
    const modalType = document.getElementById('modal_type');
    const modalDamagedLabel = document.getElementById('modal_damaged_label');
    const modalDamagedInput = document.getElementById('modal_damaged_qty');

    modalType.addEventListener('change', function() {
        if (this.value === 'Damaged') {
            modalDamagedLabel.style.display = 'block';
            modalDamagedInput.style.display = 'block';
            modalDamagedInput.required = true;
        } else {
            modalDamagedLabel.style.display = 'none';
            modalDamagedInput.style.display = 'none';
            modalDamagedInput.required = false;
            modalDamagedInput.value = '';
        }
    });
</script>

<script>
const damagedItemsFromServer = <?= json_encode($damagedItems, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;

function openDamagedModal() {
    const tbody = document.querySelector('#damagedTable tbody');
    tbody.innerHTML = '';

    if (!damagedItemsFromServer || damagedItemsFromServer.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;">No damaged items recorded.</td></tr>`;
    } else {
        damagedItemsFromServer.forEach((item, index) => {
            const tr = document.createElement('tr');

            const prodCell = document.createElement('td');
            prodCell.textContent = item.product_description || 'Unknown';

            const batchCell = document.createElement('td');
            batchCell.textContent = item.batch_label || '-';

            const qtyCell = document.createElement('td');
            qtyCell.textContent = item.quantity;

            const actionCell = document.createElement('td');

            // Return button — this only affects UI. If you want server-side return action, make a form or AJAX call to controller.
            const returnBtn = document.createElement('button');
returnBtn.className = 'save-btn';
returnBtn.textContent = 'Return';
returnBtn.addEventListener('click', function(e) {
    e.preventDefault(); // prevent default form/button behavior
    const modal = document.getElementById('returnDamagedModal');
    modal.querySelector('#return_product').textContent = item.product_description;
    modal.querySelector('#return_qty').value = item.quantity;
    modal.querySelector('#return_expiry').value = item.batch_expiry || '';
    modal.querySelector('#return_buy_price').value = item.buy_price || '';
    modal.querySelector('#return_sell_price').value = item.sell_price || '';
    modal.querySelector('#return_damaged_id').value = item.damaged_id;
    modal.querySelector('#return_product_id').value = item.product_id;
    modal.querySelector('#return_batch_id').value = item.batch_id || '';

    modal.style.display = 'flex';
});


            actionCell.appendChild(returnBtn);

            tr.appendChild(prodCell);
            tr.appendChild(batchCell);
            tr.appendChild(qtyCell);
            tr.appendChild(actionCell);

            tbody.appendChild(tr);
        });
    }

    document.getElementById('damagedModal').style.display = 'flex';
}
</script>



<script>
const productsData = <?= json_encode($products); ?>;

const addModal = document.getElementById('addModal');
const codeInput = addModal.querySelector('input[name="code"]');
const descInput = addModal.querySelector('input[name="description"]');
const categorySelect = addModal.querySelector('select[name="category"]');
const stockInput = addModal.querySelector('input[name="stock"]');
const buyInput = addModal.querySelector('input[name="buy_price"]');
const sellInput = addModal.querySelector('input[name="sell_price"]');
const expiryInput = addModal.querySelector('input[name="expiry"]');
const unitRadios = addModal.querySelectorAll('input[name="unit"]');
const perishabilityRadios = addModal.querySelectorAll('input[name="perishability"]');

// Clear other fields (but keep code/desc intact)
function clearOtherFields() {
    stockInput.value = '';
    buyInput.value = '';
    sellInput.value = '';
    expiryInput.value = '';

    unitRadios.forEach(r => {
        r.checked = false;
        const span = r.closest('.unit-label')?.querySelector('span');
        if(span){
            span.style.backgroundColor = '';
            span.style.color = '';
            span.style.borderColor = '';
        }
    });

    if(categorySelect){
        categorySelect.value = '';
        categorySelect.disabled = false;
    }

    perishabilityRadios.forEach(r => r.checked = false);
    const defaultPerish = Array.from(perishabilityRadios).find(r => r.value === 'N/P');
    if(defaultPerish) defaultPerish.checked = true;
}

// Fill product details (auto-fill) but leave stock empty
function fillProductDetails(product){
    stockInput.value = ''; // leave for manual input
    buyInput.value = product.buy_price || '';
    sellInput.value = product.sell_price || '';
    expiryInput.value = product.expiry || '';

    // highlight unit
    unitRadios.forEach(r => {
        r.checked = false;
        const span = r.closest('.unit-label')?.querySelector('span');
        if(span){
            span.style.backgroundColor = '';
            span.style.color = '';
            span.style.borderColor = '';
        }
    });
    const unitRadio = Array.from(unitRadios).find(r => r.value.toLowerCase() === (product.unit||'').toLowerCase());
    if(unitRadio){
        unitRadio.checked = true;
        const span = unitRadio.closest('.unit-label')?.querySelector('span');
        if(span){
            span.style.backgroundColor = '#344F1F';
            span.style.color = '#fff';
            span.style.borderColor = '#2c4016';
        }
    }

    // category
    if(categorySelect){
        categorySelect.value = product.category || product.catagory || '';
        categorySelect.disabled = true;
    }

    // perishability
    perishabilityRadios.forEach(r => r.checked = false);
    const perishRadio = Array.from(perishabilityRadios).find(r => r.value === (product.perishability || 'N/P'));
    if(perishRadio) perishRadio.checked = true;
}

// Only fill if exact match; otherwise leave fields as-is
function checkAndFill(){
    const code = codeInput.value.trim().toLowerCase();
    const desc = descInput.value.trim().toLowerCase();

    let match = null;
    if(code){
        match = productsData.find(p => (p.code||'').trim().toLowerCase() === code);
    }
    if(!match && desc){
        match = productsData.find(p => (p.description||'').trim().toLowerCase() === desc);
    }

    if(match){
        codeInput.value = match.code || '';
        descInput.value = match.description || '';
        fillProductDetails(match);
    } else {
        // do NOT clear user input—just reset other fields for safety
        clearOtherFields();
    }
}

// Events
codeInput.addEventListener('blur', checkAndFill);
descInput.addEventListener('blur', checkAndFill);

function openAddModal(){
    codeInput.value = '';
    descInput.value = '';
    clearOtherFields();
    addModal.style.display = 'flex';
}

function closeModal(modalId){
    const modal = document.getElementById(modalId);
    if(!modal) return;
    codeInput.value = '';
    descInput.value = '';
    clearOtherFields();
    modal.style.display = 'none';
}






// Array to store damaged items
let damagedItems = []; 
// Example item: { productId: 1, batchId: 2, description: "Milk", damagedQty: 5 }

// function clearFields() {
//     codeInput.value = '';
//     descInput.value = '';
//     stockInput.value = '';
//     buyInput.value = '';
//     sellInput.value = '';
//     expiryInput.value = '';

//     // Reset unit radios
//     unitRadios.forEach(r => {
//         r.checked = false;
//         const span = r.closest('.unit-label')?.querySelector('span');
//         if(span) { 
//             span.style.backgroundColor=''; 
//             span.style.color=''; 
//             span.style.borderColor=''; 
//         }
//     });

//     // Re-enable selects and reset values
//     if(categorySelect) {
//         categorySelect.value='';
//         categorySelect.disabled=false;
//     }
//     if(perishabilitySelect) {
//         perishabilitySelect.value='N/P';
//         perishabilitySelect.disabled=false;
//     }
// }


function removeDamaged(index) {
    damagedItems.splice(index, 1);
    openDamagedModal();
}

function returnToStock(index) {
    const item = damagedItems[index];
    // Implement logic to add the quantity back to product/batch stock
    console.log(`Returning ${item.damagedQty} of product ${item.description} to stock`);
    damagedItems.splice(index, 1);
    openDamagedModal();
}

function markBatchDamaged() {
    const batchId = document.getElementById('modal_batch_id').value;
    const productId = document.getElementById('modal_product_id').value;
    const stock = parseInt(document.getElementById('modal_stock').value) || 0;
    const description = productsData.find(p => p.id == productId)?.description || 'Unknown';
    const qty = parseInt(document.getElementById('batch_damaged_qty').value) || 0;

    if(qty <= 0) {
        alert("Enter a valid damaged quantity.");
        return;
    }
    if(qty > stock) {
        alert("Damaged quantity cannot exceed current stock.");
        return;
    }

    damagedItems.push({
        productId: productId,
        batchId: batchId,
        description: description,
        damagedQty: qty
    });

    document.getElementById('batch_damaged_qty').value = '';
    openDamagedModal();
}

function openReturnModal(item) {
    const modal = document.getElementById('returnDamagedModal');
    modal.querySelector('#return_product').textContent = item.product_description;
    modal.querySelector('#return_qty').value = item.quantity;
    modal.querySelector('#return_expiry').value = item.batch_expiry || '';
    modal.querySelector('#return_buy_price').value = item.buy_price || '';
    modal.querySelector('#return_sell_price').value = item.sell_price || '';
    modal.querySelector('#return_damaged_id').value = item.damaged_id;
    modal.querySelector('#return_product_id').value = item.product_id;
    modal.querySelector('#return_batch_id').value = item.batch_id || '';
    modal.style.display = 'flex';
}

// Example inside your loop
returnBtn.addEventListener('click', function(e) {
    e.preventDefault(); // prevent any default behavior
    openReturnModal(item);
});

document.getElementById('returnDamagedForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const productId = document.getElementById('return_product_id').value;
    const batchExpiry = document.getElementById('return_expiry').value;
    const buyPrice = parseFloat(document.getElementById('return_buy_price').value);
    const sellPrice = parseFloat(document.getElementById('return_sell_price').value);
    const returnQty = parseInt(document.getElementById('return_qty').value);

    // Find product in productsData
    const product = productsData.find(p => p.id == productId);
    if (!product) return alert("Product not found!");

    // Check if a batch exists with same expiry, buy price, sell price
    let merged = false;
    if (product.batches && product.batches.length > 0) {
        product.batches.forEach(batch => {
            const batchBuy = parseFloat(batch.buy_price);
            const batchSell = parseFloat(batch.sell_price);
            const batchExp = batch.expiry;

            if (batchExp === batchExpiry && batchBuy === buyPrice && batchSell === sellPrice) {
                // Merge returned quantity into this batch
                batch.stock = (parseInt(batch.stock) || 0) + returnQty;
                merged = true;
            }
        });
    }

    // If no matching batch, add as new batch
    if (!merged) {
        if (!product.batches) product.batches = [];
        product.batches.push({
            batch_id: 'new_' + Date.now(),
            stock: returnQty,
            buy_price: buyPrice,
            sell_price: sellPrice,
            expiry: batchExpiry
        });
    }

    // Optionally, remove from damagedItems array
    const damagedId = document.getElementById('return_damaged_id').value;
    const index = damagedItems.findIndex(d => d.damaged_id == damagedId);
    if (index !== -1) damagedItems.splice(index, 1);

    // Close modal
    document.getElementById('returnDamagedModal').style.display = 'none';
    // Reopen damaged modal to refresh table
    openDamagedModal();
});
function getBatchColor(batch) {
    const today = new Date();
    const expiryDate = new Date(batch.expiry);
    const stock = parseInt(batch.stock) || 0;

    if (stock <= 0 || expiryDate < today) {
        return { bg: '#f8d7da', color: '#721c24' }; // Red for expired or no stock
    } else if (expiryDate - today <= 7 * 24 * 60 * 60 * 1000 || stock <= 10) {
        return { bg: '#fff3cd', color: '#856404' }; // Yellow for near expiry or low stock
    } else {
        return { bg: '#d4edda', color: '#155724' }; // Green for healthy batch
    }
}

// Example when rendering batches dynamically
const batchTableBody = document.querySelector('#batchTable tbody');
batchTableBody.innerHTML = '';

product.batches.forEach(batch => {
    const tr = document.createElement('tr');
    const colors = getBatchColor(batch);

    tr.style.backgroundColor = colors.bg;
    tr.style.color = colors.color;

    tr.innerHTML = `
        <td>${product.description}</td>
        <td>${batch.stock}</td>
        <td>${batch.expiry}</td>
        <td>
            <button class="edit-btn">Edit</button>
            <button class="damaged-btn">Damaged</button>
        </td>
    `;

    batchTableBody.appendChild(tr);
});


</script>


</body>
</html>
