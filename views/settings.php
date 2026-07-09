<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User info
$role = $_SESSION['role'] ?? 'cashier'; // Default to cashier
$user_name = $_SESSION['username'] ?? 'Guest';

// Notifications
$notifications = [
    "Product 'Apple Juice' is near expiry.",
    "Low stock on 'Banana Chips'.",
    "New order received for 2 items."
];

// Models
require_once __DIR__ . '/../UserModel.php';
$userModel = new UserModel();
$activeUsers = $userModel->getUsersByStatus('Active');
$deactivatedUsers = $userModel->getUsersByStatus('Inactive');

require_once __DIR__ . '/../ProductModel.php';
$productModel = new ProductModel();
$archivedProducts = $productModel->getArchivedProducts();
$archivedBatches = $productModel->getArchivedBatches(); // ✅ Correct usage
$getAllproductsDescription = $productModel->getAllproductsDescription();

require_once __DIR__ . '/../CategoryModel.php';
$categoryModel = new CategoryModel();
$deactivatedCategories = $categoryModel->getDeactivatedCategories();

require_once __DIR__ . '/../DiscountModel.php';
$discountModel = new DiscountModel();
$discounts = $discountModel->getAllDiscounts();

require_once __DIR__ . '/../SystemSettingsModel.php';
$settingsModel = new SystemSettingsModel();
$verificationCode = $settingsModel->getVerificationCode();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="/POSu/styles/stylee.css">
    <link rel="stylesheet" href="/POSu/styles/settingscss.css">
</head>
<body>

<?php include __DIR__ . '/../sidebar.php'; ?>

<main class="main-content">
    <div class="header">
        <h1>Settings</h1>
<div class="user-info">
    <img src="/POSu/images/user.png" alt="User Icon">
    <div>
        <p>Welcome, <strong><?= htmlspecialchars($user_name); ?></strong></p>
        <!-- Email removed since it's not needed -->
    </div>
</div>

    </div>

    <!-- Flash messages -->
<!-- Flash messages -->
<?php if (isset($_SESSION['flash_message'])): ?>
    <div style="background-color: <?= $_SESSION['flash_type'] === 'success' ? '#d4edda' : '#f8d7da'; ?>; 
                color: <?= $_SESSION['flash_type'] === 'success' ? '#155724' : '#721c24'; ?>; 
                padding: 10px; border-radius: 5px; margin-bottom: 15px;">
        <?= htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
    <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
<?php endif; ?>


    <div class="button-container">
        <?php if ($role === 'admin'): ?>
            <button class="settings-btn" onclick="window.location.href='/POSu/views/Activity.php'">Activity Log</button>
            <button class="settings-btn" onclick="openModal('usersModal')">Users</button>
            <button class="settings-btn" onclick="openModal('deactivatedModal')">Archives</button>
            <button class="settings-btn" onclick="window.location.href='/POSu/backup/backup.php'">Backup Database</button>
            <button class="settings-btn" onclick="openModal('discountsModal')">Discounts/Codes</button>
        <?php endif; ?>
        <a class="settings-btn" href="/POSu/views/usermanual.php">User Manual</a>
    </div>

<!-- Discounts Modal -->
<?php if ($role === 'admin'): ?>
<div class="modal-overlay" id="discountsModal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal('discountsModal')">&times;</span>
        <h2>Edit Discounts & System Code</h2>

        <!-- Discounts Form -->
        <form method="POST" action="/POSu/index.php?url=discounts">
            <table class="modal-table">
                <thead>
                    <tr>
                        <th>Discount Name</th>
                        <th>Percentage (%)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($discounts as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['name']); ?></td>
                        <td>
                            <input type="number" name="percentage[<?= $d['id']; ?>]" 
                                   value="<?= $d['percentage']; ?>" step="0.01" min="0" required>
                        </td>
                        <td>
                            <button type="submit" class="save-btn" name="update" value="<?= $d['id']; ?>">Update</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

<!-- System Code Section -->
<form method="POST" action="/POSu/controllers/SystemSettingsController.php" 
      style="display: flex; align-items: center; gap: 10px; margin-top: 20px;">

    <label for="verification_code" style="font-weight:bold; white-space: nowrap;">
        Forgot Password 6-Digit Code:
    </label>

    <div style="position: relative; flex-grow: 1; display: flex; align-items: center;">
        <input type="password" id="verification_code" name="verification_code"
               value="<?= htmlspecialchars($verificationCode ?? ''); ?>" maxlength="6" required
               style="flex-grow: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">

        <button type="button" onclick="toggleCodeVisibility()"
                style="margin-left: 5px; padding: 6px 10px; background: #eee; border: 1px solid #ccc; cursor: pointer;">
            👁
        </button>

        <button type="submit" name="update_code"
                style="margin-left: 5px; padding: 10px 15px; background: #88976C; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Update
        </button>
    </div>

</form>


<script>
function toggleCodeVisibility() {
    const codeInput = document.getElementById('verification_code');
    codeInput.type = codeInput.type === 'password' ? 'text' : 'password';
}
</script>



        <button class="save-btn" onclick="closeModal('discountsModal')" style="margin-top: 15px;">Close</button>
    </div>
</div>
<?php endif; ?>




    <!-- Manual Modal -->
    <div class="modal-overlay" id="manualModal">
        <div class="modal-content">
            <h2>User Manual</h2>
            <div class="manual-section">
                <h3>🔹 Adding a Product</h3>
                <p>Go to the Products page and click "+ Add Product". Fill in the product details and click "Save".</p>

                <h3>🔹 Making a Sale</h3>
                <p>Go to Sales, scan or search for an item, set quantity, and click "Proceed". Review the sale, enter payment, and click "Checkout".</p>

                <h3>🔹 Viewing Sales Report</h3>
                <p>Go to Reports > Sales Report. Use the search or date filters to view previous transactions.</p>

                <h3>🔹 Managing Categories</h3>
                <p>On the Categories page, click "+ Add Category" to create a new category, or "Edit" to update an existing one.</p>

                <h3>🔹 Inventory Alerts</h3>
                <p>Products with low stock will appear with a colored indicator in the Products list. Reorder as needed.</p>
            </div>
            <button class="save-btn" onclick="closeModal('manualModal')">Close</button>
        </div>
    </div>

    <!-- Deactivated Modal -->
    <?php if ($role === 'admin'): ?>
    <div class="modal-overlay" id="deactivatedModal">
    <div class="modal-content" style="max-height: 90vh; overflow-y: auto; position: relative;">
        <span class="close-btn" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:24px;" onclick="closeModal('deactivatedModal')">&times;</span>

            <h1>Deactivated Items<br></h1>

            <h2><hr><br>Deactivated Products</h2>
<input type="text" id="searchArchivedProducts" placeholder="Search products..." class="modal-search">
<table class="modal-table" id="archivedProductsTable">
    <thead>
        <tr>
            <th>Product Code</th>
            <th>Name</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($archivedProducts)): ?>
            <?php foreach ($archivedProducts as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['code']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td>
                        <form method="POST" action="/POSu/controllers/ProductController.php">
                            <input type="hidden" name="action" value="restore">
                            <input type="hidden" name="id" value="<?= $product['id']; ?>">
                            <button type="submit" class="restore-btn">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4">No deactivated products found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<h2><br>Deactivated Batches</h2>
<input type="text" id="searchDeactivatedBatches" placeholder="Search batches..." class="modal-search">
<table class="modal-table" id="deactivatedBatchesTable">
    <thead>
        <tr>
            <th>Product Name</th>
            <th>Batch Number</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($archivedBatches)): ?>
            <?php foreach ($archivedBatches as $batch): ?>
                <tr>
                    <td><?= htmlspecialchars($batch['description']); ?></td>
                    <td><?= htmlspecialchars($batch['batch_id']); ?></td>
                    <td>
                        <form method="POST" action="/POSu/controllers/ProductController.php">
                            <input type="hidden" name="action" value="restore_batch">
                            <input type="hidden" name="batch_id" value="<?= $batch['batch_id']; ?>">
                            <button type="submit" class="restore-btn">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">No deactivated batches found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


<h2><br>Deactivated Categories</h2>
<input type="text" id="searchDeactivatedCategories" placeholder="Search categories..." class="modal-search">
<table class="modal-table" id="deactivatedCategoriesTable">
    <thead>
        <tr>
            <th>Category</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($deactivatedCategories)): ?>
            <?php foreach ($deactivatedCategories as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['category_name']); ?></td>
                    <td>
                        <form method="POST" action="/POSu/controllers/CategoryController.php">
                            <input type="hidden" name="action" value="restore">
                            <input type="hidden" name="id" value="<?= $category['id']; ?>">
                            <button type="submit" class="restore-btn">Restore</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="2">No deactivated categories found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>


            
            <button class="save-btn" onclick="closeModal('deactivatedModal')">Close</button>
        </div>
    </div>



   <!-- Users Modal -->
<div class="modal-overlay" id="usersModal">
    <div class="modal-content" style="max-height: 90vh; overflow-y: auto; position: relative;">
        <!-- Exit button top-right -->
        <span class="close-btn" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:24px;" onclick="closeModal('usersModal')">&times;</span>

        <h2>Users</h2>

        <!-- Active Users -->
        <h3>Active Users</h3>
        <input type="text" id="searchActiveUsers" placeholder="Search active users..." class="modal-search">
        <table class="modal-table" id="activeUsersTable">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($activeUsers)): ?>
                    <?php foreach ($activeUsers as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['status']) ?></td>
                            <td>
                                <form method="POST" action="/POSu/controllers/UserController.php">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                    <input type="hidden" name="action" value="deactivate_user">
                                    <button type="submit" class="delete-btn">Deactivate</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No active users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Deactivated Users -->
        <h3>Deactivated Users</h3>
        <input type="text" id="searchDeactivatedUsers" placeholder="Search deactivated users..." class="modal-search">
        <table class="modal-table" id="deactivatedUsersTable">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($deactivatedUsers)): ?>
                    <?php foreach ($deactivatedUsers as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['status']) ?></td>
                            <td>
                                <form method="POST" action="/POSu/controllers/UserController.php">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                    <input type="hidden" name="action" value="activate_user">
                                    <button type="submit" class="restore-btn">Activate</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5">No deactivated users found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <button class="save-btn" onclick="closeModal('usersModal')">Close</button>
    </div>
</div>

    <?php endif; ?> <!-- End admin-only modals -->

</main>

<script>
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Auto-hide flash messages
setTimeout(() => {
    const msg = document.querySelector('div[style*="background-color"]');
    if (msg) msg.style.display = 'none';
}, 3000);
<?php if(isset($_SESSION['flash_message']) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    openModal('deactivatedModal');
<?php endif; ?>

// Filter archived products
document.getElementById('searchArchivedProducts').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#archivedProductsTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Filter deactivated categories
document.getElementById('searchDeactivatedCategories').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#deactivatedCategoriesTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Filter active users
document.getElementById('searchActiveUsers').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#activeUsersTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Filter deactivated users
document.getElementById('searchDeactivatedUsers').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#deactivatedUsersTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

function toggleCodeVisibility() {
    const codeInput = document.getElementById('verification_code');
    if (codeInput.type === 'password') {
        codeInput.type = 'text';
    } else {
        codeInput.type = 'password';
    }
}
</script>
</body>
</html>
