<?php
if (session_status() === PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
}

// Make sure $dashboardData exists and is an array
if (!isset($dashboardData) || !is_array($dashboardData)) {
    $dashboardData = [];
}

// Merge with default values to prevent undefined index errors
$dashboardData = array_merge([
    'todaySales' => "0.00",
    'todayTransactions' => 0,
    'bestSellerWithQuantity' => null,
    'nearExpiryProducts' => [],
    'lowStockProducts' => [],
    'notifications' => []
], $dashboardData);

require_once __DIR__ . '/../controllers/BackupSchedulerController.php';
$scheduler = new BackupSchedulerController();
$scheduler->checkAndRunBackup();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - POS</title>
    <link rel="stylesheet" href="/styles/stylee.css">
    <link rel="stylesheet" href="/styles/dashboard.css">
</head>
<body>

    <main class="main-content">
        <div class="header">
            <h1>Dashboard</h1>
            <div class="user-info">
                <img src="/images/user.png" alt="User Icon">
                <p>Welcome, <strong>
                    <?= isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : "Guest"; ?>
                </strong>!</p>
            </div>
        </div>

<div class="button-container">
    <a href="/ADD-SALES-PAGE" class="image-btn">
        <img src="/images/add.png" alt="Add Sales">
        <span>Add Sales</span>
    </a>

    <a href="/products" class="image-btn">
        <img src="/images/prod.png" alt="Products">
        <span>Product Inventory</span>
    </a>

    <a href="/Categories" class="image-btn">
        <img src="/images/categ.png" alt="Categories">
        <span>Categories</span>
    </a>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="/sales" class="image-btn">
            <img src="/images/sales.png" alt="Sales Report">
            <span>Sales</span>
        </a>
    <?php endif; ?>

    <a href="/notifications" class="image-btn" style="position: relative;">
        <img src="/images/notif.png" alt="Notifications">
        <span>Notifications</span>
        <?php if (!empty($dashboardData['notifications']) && count($dashboardData['notifications']) > 0): ?>
            <div class="notif-exclamation">!</div>
        <?php endif; ?>
    </a>

    <a href="/settings" class="image-btn">
        <img src="/images/settings.png" alt="Settings">
        <span>Settings</span>
    </a>

    <form action="/user/logout" method="POST" class="image-btn">
        <button type="submit">
            <img src="/images/logout.png" alt="Logout">
            <span>Logout</span>
        </button>
    </form>
</div>


        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <!-- Admin-only Summary Cards -->
            <div class="summary-cards">
                <div class="card">
                    <h3>Sales Today</h3>
                    <h3>₱<?= $dashboardData['todaySales']; ?></h3>
                </div>

                <div class="card">
                    <h3>Total Transactions Today</h3>
                    <h3><?= $dashboardData['todayTransactions']; ?></h3>
                </div>

                <div class="card">
                    <h3>Best Seller Today</h3>
                    <?php if ($dashboardData['bestSellerWithQuantity']): ?>
                        <h3><?= htmlspecialchars($dashboardData['bestSellerWithQuantity']['product_name']); ?></h3>
                        <p style="font-size: 1rem; margin-top: 5px; color: #666;">
                            <?= $dashboardData['bestSellerWithQuantity']['total_sold']; ?> units sold
                        </p>
                    <?php else: ?>
                        <h3>No sales today</h3>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <br><br>

        <!-- Near Expiry Products Table -->
        <div class="notifmain-content">
            <div class="notification-section">
                <h2>Near Expiry Products</h2>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dashboardData['nearExpiryProducts'])): ?>
                            <?php foreach ($dashboardData['nearExpiryProducts'] as $product): ?>
                                <tr class="alert-row">
                                    <td><?= htmlspecialchars($product['product_name']); ?></td>
                                    <td><?= htmlspecialchars($product['category_name']); ?></td>
                                    <td><?= htmlspecialchars($product['expiry']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #666;">No products near expiry</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <br><br>

            <!-- Low Stock Products Table -->
            <div class="notification-section">
                <h2>Low Stock Products</h2>
                <br><br>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stock Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($dashboardData['lowStockProducts'])): ?>
                            <?php foreach ($dashboardData['lowStockProducts'] as $product): ?>
                                <tr class="alert-row">
                                    <td><?= htmlspecialchars($product['product_name']); ?></td>
                                    <td><?= htmlspecialchars($product['category_name']); ?></td>
                                    <td><?= htmlspecialchars($product['stock_quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: #666;">No products with low stock</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
