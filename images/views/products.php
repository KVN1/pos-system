<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}
include 'includes/header.php';

require_once 'controllers/ProductController.php';

$productController = new ProductController();
$products = $productController->index();
$categories = $productController->getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - POS</title>
    <link rel="stylesheet" href="/styles/stylee.css?v=<?= time(); ?>">
    <script>
        function openAddModal() {
            document.getElementById("addModal").style.display = "flex";
            document.getElementById("date_added").value = new Date().toISOString().slice(0, 19).replace("T", " ");
        }

        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }

        function filterProducts() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let table = document.querySelector(".products-table table tbody");
            let rows = table.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let cols = rows[i].getElementsByTagName("td");
                let match = false;

                for (let j = 0; j < cols.length - 1; j++) { // Exclude action buttons column
                    if (cols[j].textContent.toLowerCase().includes(input)) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? "" : "none";
            }
        }
    </script>
</head>
<body>
    <div class="dashboard">
        <?php include 'includes/sidebar.php'; ?>
        <main class="main-content">
            <header class="header">
                <h1>Products</h1>
                <p>Manage your product inventory efficiently.</p>
            </header>

            <div class="top-controls">
                <button class="add-btn" onclick="openAddModal()">+ Add Product</button>
                <input type="text" id="searchInput" class="search-bar" placeholder="Search for a product..." onkeyup="filterProducts()">
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
                                <th>Stock</th>
                                <th>Unit</th>
                                <th>Buying Price</th>
                                <th>Selling Price</th>
                                <th>Date Added</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['code'] ?? ''); ?></td>
                                    <td><?= htmlspecialchars($product['description'] ?? ''); ?></td>
                                    <td><?= htmlspecialchars($product['category'] ?? ''); ?></td>
                                    <td class="stock" 
                                        style="background-color: <?= 
                                            ($product['stock'] == 0) ? 'red' : 
                                            (($product['stock'] <= 10) ? 'orange' : 
                                            (($product['stock'] <= 20) ? 'yellow' : 'green')); 
                                        ?>;">
                                        <?= htmlspecialchars($product['stock'] ?? 0); ?>
                                    </td>
                                    <td><?= htmlspecialchars($product['unit'] ?? ''); ?></td>
                                    <td>₱<?= number_format($product['buy_price'] ?? 0, 2); ?></td>
                                    <td>₱<?= number_format($product['sell_price'] ?? 0, 2); ?></td>
                                    <td><?= htmlspecialchars($product['date_added'] ?? ''); ?></td>
                                    <td><?= date("m-d-Y", strtotime($product['expiry'] ?? '')); ?></td>
                                    <td class="action-buttons">
                                        <button class="edit-btn" onclick="openEditModal(
                                            '<?= $product['id']; ?>',
                                            '<?= htmlspecialchars($product['code'] ?? ''); ?>',
                                            '<?= htmlspecialchars($product['description'] ?? ''); ?>',
                                            '<?= htmlspecialchars($product['category'] ?? ''); ?>',
                                            '<?= $product['stock'] ?? 0; ?>',
                                            '<?= htmlspecialchars($product['unit'] ?? ''); ?>',
                                            '<?= $product['buy_price'] ?? 0; ?>',
                                            '<?= $product['sell_price'] ?? 0; ?>',
                                            '<?= htmlspecialchars($product['expiry'] ?? ''); ?>'
                                        )">Edit</button>

                                        <form method="POST" action="/products" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $product['id']; ?>">
                                            <button type="submit" class="delete-btn">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

 <!-- Add Product Modal -->
<div class="modal-overlay" id="addModal">
    <div class="modal-content">
        <h2>Add Product</h2>
        <form method="POST" action="/products">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="date_added" id="date_added">

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

            <label>Stock Level</label>
            <input type="number" name="stock" placeholder="Stock Level" required>

            <label>Unit</label>
            <select name="unit" required>
                <option value="Kg">Kg</option>
                <option value="Piece">Piece</option>
            </select>

            <label>Buying Price</label>
            <input type="number" step="0.01" name="buy_price" placeholder="Buying Price" required>

            <label>Selling Price</label>
            <input type="number" step="0.01" name="sell_price" placeholder="Selling Price" required>

            <label>Expiry Date</label>
            <input type="date" name="expiry" required>

            <button type="submit" class="save-btn">Add</button>
            <button type="button" class="cancel-btn" onclick="closeModal('addModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, code, description, category, stock, unit, buy_price, sell_price, expiry) {
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_code").value = code;
        document.getElementById("edit_description").value = description;
        document.getElementById("edit_category").value = category;
        document.getElementById("edit_stock").value = stock;
        document.getElementById("edit_unit").value = unit;
        document.getElementById("edit_buy_price").value = buy_price;
        document.getElementById("edit_sell_price").value = sell_price;
        document.getElementById("edit_expiry").value = expiry;

        document.getElementById("editModal").style.display = "flex";
    }
</script>
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <h2>Edit Product</h2>
        <form method="POST" action="/products">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit_id">

            <label>Product Code</label>
            <input type="text" name="code" id="edit_code" required>

            <label>Description</label>
            <input type="text" name="description" id="edit_description" required>

            <label>Category</label>
            <select name="category" id="edit_category" required>
                <option value="" disabled>Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['category_name'] ?? ''); ?>">
                        <?= htmlspecialchars($category['category_name'] ?? ''); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Stock Level</label>
            <input type="number" name="stock" id="edit_stock" required>

            <label>Unit</label>
            <select name="unit" id="edit_unit" required>
                <option value="Kg">Kg</option>
                <option value="Piece">Piece</option>
            </select>

            <label>Buying Price</label>
            <input type="number" step="0.01" name="buy_price" id="edit_buy_price" required>

            <label>Selling Price</label>
            <input type="number" step="0.01" name="sell_price" id="edit_sell_price" required>

            <label>Expiry Date</label>
            <input type="date" name="expiry" id="edit_expiry" required>

            <button type="submit" class="save-btn">Save Changes</button>
            <button type="button" class="cancel-btn" onclick="closeModal('editModal')">Cancel</button>
        </form>
    </div>
</div>
</body>
</html>
