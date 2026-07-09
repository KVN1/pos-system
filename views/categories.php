<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$categoryController = new CategoryController();
$categories = $categoryController->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Categories - POS</title>
<link rel="stylesheet" href="/styles/stylee.css">
<link rel="stylesheet" href="/styles/categories-style.css?v=<?= time(); ?>">
</head>
<body>
<div class="dashboard">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
        <header class="header">
            <h1>Grocery Categories</h1>
            <p>Manage grocery categories efficiently.</p>
<div class="actions" style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
    <button class="btn-download" onclick="downloadFilteredCSV()">Download CSV</button>
    <button class="btn-download" onclick="exportCategoriesPDF()">Export PDF</button>
    <button class="btn-download" onclick="window.print()">Print Page</button>
</div>

        </header>

        <!-- Flash Message -->
        <?php if(isset($_SESSION['flash_message'])): ?>
            <div class="flash-message <?= htmlspecialchars($_SESSION['flash_type'] ?? 'success'); ?>">
                <?= htmlspecialchars($_SESSION['flash_message']); ?>
            </div>
            <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <div class="top-bar">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search Categories" oninput="searchCategories()">
                <button id="search-btn" onclick="searchCategories()">Search</button>
            </div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <button class="add-btn" onclick="openAddModal()">+ Add Category</button>
            <?php endif; ?>
        </div>

        <div class="categories-table">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $index => $category): ?>
                        <tr data-category-name="<?= strtolower($category['category_name']); ?>">
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($category['category_name']); ?></td>
                            <td>
                                <button class="save-btn" onclick="viewProducts('<?= htmlspecialchars($category['category_name'], ENT_QUOTES); ?>')">View Products</button>

                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <button class="save-btn" onclick="openEditModal(<?= $category['id']; ?>, '<?= htmlspecialchars($category['category_name'], ENT_QUOTES); ?>')">Edit</button>

                                    <form method="POST" action="/controllers/CategoryController.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to deactivate this category?');">
                                        <input type="hidden" name="action" value="deactivate">
                                        <input type="hidden" name="id" value="<?= $category['id']; ?>">
                                        <button type="submit" class="cancel-btn">Deactivate</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Add Category Modal -->
        <div class="modal-overlay" id="addModal">
            <div class="modal-content">
                <h2>Add Category</h2>
                <form id="addCategoryForm">
                    <input type="hidden" name="action" value="add">
                    <input type="text" name="category_name" placeholder="Category Name" required>
                    <div class="modal-buttons">
                        <button type="submit" class="save-btn">Add</button>
                        <button type="button" class="cancel-btn" onclick="closeModal('addModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Category Modal -->
        <div class="modal-overlay" id="editModal">
            <div class="modal-content">
                <h2>Edit Category</h2>
                <form method="POST" action="/controllers/CategoryController.php">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="editId" name="id">
                    <input type="text" id="editName" name="category_name" required>
                    <div class="modal-buttons">
                        <button type="submit" class="save-btn">Update</button>
                        <button type="button" class="cancel-btn" onclick="closeModal('editModal')">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Products Modal -->
        <div class="modal-overlay" id="productsModal">
            <div class="modal-content products-modal">
                <h2>Products in this Category</h2>

                <!-- Search bar inside modal -->
                <div class="modal-search-bar" style="margin-bottom:10px;">
                    <input type="text" id="productsSearchInput" placeholder="Search Products" oninput="searchProducts()">
                </div>

                <div class="products-table-container">
<table id="productsTable">
    <thead>
        <tr>
            <th>Code</th>
            <th>Description</th>
            <th>Perishability</th>
            <th>Price</th>
            <th>Stock</th>
        </tr>
    </thead>

                        <tbody id="productsList">
                            <!-- Dynamically populated -->
                        </tbody>
                    </table>
                </div>
                <button class="save-btn close-btn" onclick="closeModal('productsModal')">Close</button>
            </div>
        </div>

    </main>
</div>

<script>
// --- MODAL FUNCTIONS ---
function openAddModal() { document.getElementById('addModal').style.display = 'flex'; }
function openEditModal(id, name) {
    document.getElementById('editId').value = id;
    document.getElementById('editName').value = name;
    document.getElementById('editModal').style.display = 'flex';
}
function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }

// --- ADD CATEGORY ---
document.querySelector("#addCategoryForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);

    fetch("/controllers/CategoryController.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeModal('addModal');
            location.reload();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});

// --- SEARCH CATEGORIES ---
function searchCategories() {
    let searchTerm = document.getElementById("searchInput").value.toLowerCase();
    document.querySelectorAll(".categories-table tbody tr").forEach(row => {
        let categoryName = row.dataset.categoryName.toLowerCase();
        row.style.display = categoryName.includes(searchTerm) ? "table-row" : "none";
    });
}

// --- VIEW PRODUCTS ---
function viewProducts(categoryName) {
    document.getElementById('productsSearchInput').value = ''; // reset search
    fetch(`/controllers/CategoryController.php?action=getProducts&category_name=${encodeURIComponent(categoryName)}`)
    .then(response => response.json())
    .then(products => {
        let list = document.getElementById('productsList');
        list.innerHTML = '';
        if (products.length > 0) {
            products.forEach(p => {
               list.innerHTML += `
    <tr>
        <td>${p.code}</td>
        <td>${p.description}</td>
        <td>${p.perishability}</td>
        <td>${parseFloat(p.sell_price).toFixed(2)}</td>
        <td>${p.stock} ${p.unit}</td>
    </tr>
`;

            });
        } else {
            list.innerHTML = `<tr><td colspan="4" style="text-align:center; padding: 10px;">No active products in this category.</td></tr>`;
        }
        document.getElementById('productsModal').style.display = 'flex';
    })
    .catch(console.error);
}

// --- SEARCH PRODUCTS IN MODAL ---
function searchProducts() {
    let filter = document.getElementById('productsSearchInput').value.toLowerCase();
    let rows = document.querySelectorAll('#productsList tr');

    rows.forEach(row => {
        let code = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
        let desc = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        row.style.display = (code.includes(filter) || desc.includes(filter)) ? 'table-row' : 'none';
    });
}

// --- DOWNLOAD CSV ---
function downloadFilteredCSV() {
    let rows = document.querySelectorAll(".categories-table tbody tr");
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "No,Category Name\n";

    rows.forEach((row, index) => {
        let categoryName = row.querySelector("td:nth-child(2)").textContent.trim();
        csvContent += `${index+1},${categoryName}\n`;
    });

    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "categories.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}


function exportCategoriesPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Table headers
    const headers = ["No", "Category Name"];

    // Table data
    const data = [];
    document.querySelectorAll(".categories-table tbody tr").forEach((row, index) => {
        if(row.style.display === "none") return; // skip filtered out
        const categoryName = row.querySelector("td:nth-child(2)").textContent.trim();
        data.push([index + 1, categoryName]);
    });

    // Add table to PDF
    doc.autoTable({
        head: [headers],
        body: data,
        startY: 20,
        styles: { fontSize: 10 },
        headStyles: { fillColor: [100, 100, 255] }
    });

    doc.save("categories.pdf");
}


</script>

<style>
.flash-message {
    position: fixed;
    top: 20px; right: 20px;
    background-color: #4CAF50; color: white;
    padding: 12px 20px; border-radius: 10px;
    font-weight: bold; z-index: 9999; box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.flash-message.error { background-color: #f44336; }

.btn-download {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    cursor: pointer;
    transition: background 0.3s;
}
.btn-download:hover {
    background-color: #45a049;
}



</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

</body>
</html>
