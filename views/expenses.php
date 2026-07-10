<?php
if (session_status() === PHP_SESSION_NONE) {
    if (session_status() === PHP_SESSION_NONE) { session_start(); } // Ensure session is started
}
?>

<div class="main-content">
    <!-- Date Range Selector (From Date to Date) -->
    <div class="date-selector">
        <label for="start-date">From Date:</label>
        <input type="date" id="start-date" name="start-date" value="2025-04-01" onchange="updateExpenses()">
        
        <label for="end-date">To Date:</label>
        <input type="date" id="end-date" name="end-date" value="2025-04-06" onchange="updateExpenses()">
    </div>

    <div class="header">
        <link rel="stylesheet" href="/styles/stylee.css?v=<?= time(); ?>">
        <link rel="stylesheet" href="/styles/expenses.css?v=<?= time(); ?>">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        <h2>Expenses Overview</h2>
    </div>

    <div class="expenses-container">

<!-- Total Expenses Section -->
<div class="expense-card">
    <h3>Total Expenses</h3> 
    <p class="amount">₱1,685.00</p>

    <!-- Breakdown for Total Expenses -->
    <div class="expense-breakdown">
        <table>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Date Added</th>
                <th>Expiry Date</th>
                <th>Buying Price</th>
            </tr>
            <!-- Example Expenses -->
            <tr>
                <td>4800014141081 Summit 500ml Beverages</td>
                <td>10</td>
                <td>2025-04-01</td>
                <td>2025-06-01</td>
                <td>₱50.00</td>
            </tr>
            <tr>
                <td>7622202243639 Dairy Milk - Milk Chocolate 11g Snacks</td>
                <td>15</td>
                <td>2025-04-02</td>
                <td>2025-07-02</td>
                <td>₱35.00</td>
            </tr>
            <tr>
                <td>4800361331500 BearBrand Sterilized 200ml Beverages</td>
                <td>5</td>
                <td>2025-04-03</td>
                <td>2025-10-03</td>
                <td>₱100.00</td>
            </tr>
        </table>
    </div>

    <!-- Other Expenses Table -->
    <h4 style="margin-top: 30px;">Other Expenses</h4> <button class="save-btn" onclick="showAddExpenseModal()">+ Add Expense</button>

    <div class="expense-breakdown">
        <table>
            <tr>
                <th>Type of Expense</th>
                <th>Description</th>
                <th>Cost</th>
            </tr>
            <tr>
                <td>Salary</td>
                <td>Keziah</td>
                <td>₱500.00</td>
            </tr>
            <tr>
                <td>Salary</td>
                <td>Khloe</td>
                <td>₱500.00</td>
            </tr>
            <tr>
                <td>Salary</td>
                <td>Kraven</td>
                <td>₱500.00</td>
            </tr>
        </table>
    </div>
</div>



<!-- Add Expense Modal -->
<div class="modal-overlay" id="add-expense-modal" style="display: none;">
    <div class="modal-content">
        <h2>Add New Expense</h2>
        <form id="add-expense-form" method="POST" action="/expenses/add"> <!-- Update this to your PHP handler -->
            <label for="expense-type">Type of Expense:</label>
            <select id="expense-type" name="type" required>
                <option value="Salary">Salary</option>
                <option value="Delivery Fee">Delivery Fee</option>
                <option value="Utilities">Utilities</option>
                <option value="Other">Other</option>
            </select>

            <label for="expense-description">Description:</label>
            <input type="text" id="expense-description" name="description" required>

            <label for="expense-cost">Cost (₱):</label>
            <input type="number" id="expense-cost" name="cost" min="0" step="0.01" required>

            <div class="modal-buttons">
                <button type="button" class="save-btn" onclick="console.log('Save clicked');">Save</button>
                <button type="button" class="cancel-btn" onclick="closeAddExpenseModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showExpenseDetails() {
    document.getElementById('expense-details-modal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('expense-details-modal').style.display = 'none';
}

function updateExpenses() {
    const startDate = document.getElementById("start-date").value;
    const endDate = document.getElementById("end-date").value;
    window.location.href = "expenses.php?start_date=" + startDate + "&end_date=" + endDate;
}
function showAddExpenseModal() {
    document.getElementById('add-expense-modal').style.display = 'flex';
}

function closeAddExpenseModal() {
    document.getElementById('add-expense-modal').style.display = 'none';
}

</script>

