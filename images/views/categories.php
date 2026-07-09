<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/controllers/CategoryController.php';

$categoryController = new CategoryController();
$categories = $categoryController->index();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - POS</title>
    <link rel="stylesheet" href="/POSu/styles/stylee.css">
    <link rel="stylesheet" href="/POSu/styles/categories-style.css">

</head>
<body>
    <div class="dashboard">
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/POSu/includes/sidebar.php'; ?>
        <main class="main-content">
            <header class="header">
    <div>
        <h1>Grocery Categories</h1>
        <p>Manage grocery categories efficiently.</p>
    </div>
    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search Categories" oninput="searchCategories()">
        <button class="save-btn" onclick="searchCategories()">Search</button>
    </div>
    <button class="add-btn" onclick="openAddModal()">+ Add Category</button>
</header>


            <div class="categories-container" id="categoriesContainer">
                <?php foreach ($categories as $category): ?>
                    <div class="category-card" data-category-name="<?= strtolower($category['category_name']); ?>" onclick="openEditModal(<?= $category['id']; ?>, '<?= htmlspecialchars($category['category_name']); ?>')">
                        <h3><?= htmlspecialchars($category['category_name']); ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </main>
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

    <!-- Success Modal -->
    <div class="modal-overlay" id="successModal">
        <div class="modal-content">
            <h2>Success!</h2>
            <p id="successMessage"></p>
            <button class="save-btn" onclick="closeModal('successModal'); location.reload();">OK</button>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <h2>Edit Category</h2>
                <form method="POST" action="/POSu/controllers/CategoryController.php">
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

    <!-- JavaScript -->
    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        function openEditModal(id, name) {
            document.getElementById('editId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        document.querySelector("#addCategoryForm").addEventListener("submit", function(event) {
            event.preventDefault();

            let formData = new FormData(this);

            fetch("/POSu/controllers/CategoryController.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("successMessage").innerText = data.message;
                    closeModal('addModal'); // Close add modal
                    document.getElementById("successModal").style.display = "flex";
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(error => console.error("Error:", error));
        });

        function searchCategories() {
            let searchTerm = document.getElementById("searchInput").value.toLowerCase();
            let categories = document.querySelectorAll(".category-card");

            categories.forEach(category => {
                let categoryName = category.getAttribute("data-category-name");
                if (categoryName.includes(searchTerm)) {
                    category.style.display = "block";
                } else {
                    category.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>
