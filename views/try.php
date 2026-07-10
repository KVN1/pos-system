<?php
if (session_status() === PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
}
include 'includes/header.php';
require_once 'controllers/ProductController.php';

$productController = new ProductController();
$products = $productController->index();
$categories = $productController->getCategories();

$lowStockProducts = array_filter($products, function ($product) {
    return $product['stock'] <= 10;
});
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
        textarea:focus { border-color: #4CAF50; outline: none; }
    </style>
    <script>
        function openAddModal() { document.getElementById("addModal").style.display = "flex"; }
        function closeModal(id) { document.getElementById(id).style.display = "none"; }

```
    function filterProducts() {
        let input = document.getElementById("searchInput").value.toLowerCase();
        let rows = document.querySelectorAll(".products-table tbody tr");
        rows.forEach(row => {
            let match = Array.from(row.getElementsByTagName("td")).some(cell =>
                cell.textContent.toLowerCase().includes(input)
            );
            row.style.display = match ? "" : "none";
        });
    }

    function openEditModal(id, code, description, category, perishability, stock, unit, buy_price, sell_price, expiry) {
        document.getElementById("editModal").style.display = "flex";
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_code").value = code;
        document.getElementById("edit_description").value = description;
        document.getElementById("edit_category").value = category;
        document.getElementById("edit_perishability").value = perishability;
        document.getElementById("edit_stock").value = stock;
        document.getElementById("edit_unit").value = unit;
        document.getElementById("edit_buy_price").value = buy_price;
        document.getElementById("edit_sell_price").value = sell_price;
        document.getElementById("edit_expiry").value = expiry;
        document.getElementById("edit_date_added").value = new Date().toISOString().slice(0,19).replace("T"," ");
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
        alert("PDF export not implemented yet. You can use a library like jsPDF or server-side PHP to generate PDF.");
    }
</script>
```

</head>
<body>

<?php if(isset($_SESSION['flash_message'])): ?>

<div class="flash-message <?= htmlspecialchars($_SESSION['flash_type'] ?? 'success'); ?>">
    <?= htmlspecialchars($_SESSION['flash_message']); ?>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const flash = document.querySelector(".flash-message");
    if(flash){ flash.style.display="block"; setTimeout(()=>{ flash.style.display="none"; },3000); }
});
</script>
<style>
.flash-message { display:none; position:fixed; top:20px; right:20px; background-color:#4CAF50; color:white; padding:12px 20px; border-radius:10px; font-weight:bold; z-index:9999; box-shadow:0 2px 6px rgba(0,0,0,0.2); }
.flash-message.error { background-color:#f44336; }
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
            <button type="button" class="btn-download" onclick="downloadFilteredCSV()">Download CSV</button>
            <button class="btn-download" onclick="window.print()">Print Page</button>
            <button class="btn-download" onclick="exportPDF()">Export PDF</button>
        </div>
    </header>

```
<div class="top-controls">
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <button class="add-btn" onclick="openAddModal()">+ Add Product</button>
        <button class="reorder-btn" onclick="document.getElementById('reorderModal').style.display='flex'" style="position:relative;">
            Repurchase
            <?php if(count($lowStockProducts)>0): ?>
                <span style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; padding:2px 6px; font-size:12px; font-weight:bold;">!</span>
            <?php endif; ?>
        </button>
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
        <div class="legend-item"><div class="color-box color-orange"></div>Expiring soon (within 5 days)</div>
        <div class="legend-item"><div class="color-box color-red"></div>Expired</div>
    </div>
</div>

<section class="products-table">
    <h2>Product Inventory</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Perishability</th>
                    <th>Stock</th>
                    <th>Unit</th>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?><th>Buying Price</th><?php endif; ?>
                    <th>Selling Price</th>
                    <th>Date and Time Added</th>
                    <th>Expiry Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product):
                    $expiryDate = strtotime($product['expiry']);
                    $currentDate = strtotime(date("Y-m-d"));
                    $daysRemaining = floor(($expiryDate - $currentDate)/(60*60*24));
                    $expiryColor = ($daysRemaining<0)?'#ff6666':(($daysRemaining<=5)?'#ffcc66':'#66cc66');
                    $stockColor = ($product['stock']<=10)?'#ff6666':(($product['stock']<=20)?'#ffcc66':'#66cc66');
                ?>
                <tr>
                    <td><?= htmlspecialchars($product['code']); ?></td>
                    <td><?= htmlspecialchars($product['description']); ?></td>
                    <td><?= htmlspecialchars($product['category']); ?></td>
                    <td><?= htmlspecialchars($product['perishability']); ?></td>
                    <td style="background-color:<?= $stockColor; ?>;"><?= htmlspecialchars($product['stock']); ?></td>
                    <td><?= htmlspecialchars($product['unit']); ?></td>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                        <td>₱<?= number_format($product['buy_price'],2); ?></td>
                    <?php endif; ?>
                    <td>₱<?= number_format($product['sell_price'],2); ?></td>
                    <td><?= date("Y-m-d | g:i:s A", strtotime($product['date_added'])); ?></td>
                    <td style="background-color:<?= $expiryColor; ?>;"><?= date("m-d-Y", strtotime($product['expiry'])); ?></td>
                    <td>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
                            <button class="edit-btn" onclick="openEditModal(
                                '<?= $product['id']; ?>',
                                '<?= htmlspecialchars($product['code']); ?>',
                                '<?= htmlspecialchars($product['description']); ?>',
                                '<?= htmlspecialchars($product['category']); ?>',
                                '<?= htmlspecialchars($product['perishability']); ?>',
                                '<?= $product['stock']; ?>',
                                '<?= htmlspecialchars($product['unit']); ?>',
                                '<?= $product['buy_price']; ?>',
                                '<?= $product['sell_price']; ?>',
                                '<?= htmlspecialchars($product['expiry']); ?>'
                            )">Edit</button>
                            <form method="POST" action="controllers/ProductController.php" onsubmit="return confirm('Are you sure you want to remove this product?');" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                <button type="submit" class="delete-btn">Deactivate</button>
                            </form>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
```

</main>
</div>

<!-- Admin Modals (Add/Edit/Reorder) remain the same as your original code -->

<?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): include 'modals/products_modals.php'; endif; ?>

</body>
</html>
