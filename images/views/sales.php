<?php
require_once __DIR__ . '/../controllers/SalesController.php';
include 'includes/header.php';  // This includes your <head> and any other global styles or scripts

$salesController = new SalesController();
// $sales = $salesController->getAllSales(); // Uncomment when database works

// Temporary dummy sales data
$sales = [
    ['id' => 1, 'user_id' => 'User 101', 'total_amount' => 1200.50, 'payment_method' => 'Cash', 'sale_date' => '2025-03-31'],
    ['id' => 2, 'user_id' => 'User 102', 'total_amount' => 850.00, 'payment_method' => 'Credit Card', 'sale_date' => '2025-03-30'],
    ['id' => 3, 'user_id' => 'User 103', 'total_amount' => 1500.75, 'payment_method' => 'Gcash', 'sale_date' => '2025-03-29']
];
?>

<body>
    <div class="dashboard">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <main class="main-content">
            <header class="header">
                <h1>Sales</h1>
            </header>

            <section class="content">
                <div class="sales-container">
                    <button class="add-sale-btn" onclick="window.location.href='/views/add-sales.php'">Add Sale</button>
                    
                    <table class="sales-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Sale Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($sales)): ?>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sale['id']) ?></td>
                                        <td><?= htmlspecialchars($sale['user_id']) ?></td>
                                        <td>₱<?= number_format($sale['total_amount'], 2) ?></td>
                                        <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                                        <td><?= htmlspecialchars($sale['sale_date']) ?></td>
                                        <td>
                                            <a href="sale-detail.php?id=<?= $sale['id'] ?>" class="view-btn">View</a>
                                            <a href="../controllers/SalesController.php?delete=<?= $sale['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="no-data">No sales recorded yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>


<style>
.sales-container {
    width: 90%;
    margin: auto;
    padding: 20px;
    text-align: center;
}

.add-sale-btn {
    background: #8b5e34;
    color: #fff;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 10px;
}

.add-sale-btn:hover {
    background: #a47148;
}

.sales-table {
    width: 100%;
    border-collapse: collapse;
}

.sales-table th, .sales-table td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: center;
    color: #000;
}

.sales-table th {
    background: #f8f1e4;
}

.view-btn {
    padding: 5px 10px;
    background: #5cb85c;
    color: white;
    text-decoration: none;
    border-radius: 3px;
}

.delete-btn {
    padding: 5px 10px;
    background: #d9534f;
    color: white;
    text-decoration: none;
    border-radius: 3px;
}

.view-btn:hover {
    background: #4cae4c;
}

.delete-btn:hover {
    background: #c9302c;
}

.no-data {
    text-align: center;
    font-weight: bold;
    color: #777;
}
</style>
