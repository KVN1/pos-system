<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
        <link rel="stylesheet" href="styles/stylee.css">

    <link rel="stylesheet" href="styles/sales-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

        <?php include $_SERVER['DOCUMENT_ROOT'] . "/includes/sidebar.php"; ?>

    <div class="main-content">
        <h1>Sales Report</h1>

        <!-- Date Range Filters -->
        <form action="sales-report.php" method="GET" class="filter-form">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date">

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date">

            <button type="submit">Filter</button>
        </form>

        <!-- Total Sales -->
        <section class="report-section">
            <h2>Total Sales: ₱0.00</h2>
        </section>

        <!-- Total Transactions -->
        <section class="report-section">
            <h2>Total Transactions: 0</h2>
        </section>

        <!-- Top Selling Products -->
        <section class="report-section">
            <h2>Top Selling Products</h2>
            <ul>
                <li>Product 1 - 0 sold</li>
                <li>Product 2 - 0 sold</li>
                <li>Product 3 - 0 sold</li>
                <li>Product 4 - 0 sold</li>
                <li>Product 5 - 0 sold</li>
            </ul>
        </section>

        <!-- Sales by Category -->
        <section class="report-section">
            <h2>Sales by Category</h2>
            <ul>
                <li>Category 1 - ₱0.00</li>
                <li>Category 2 - ₱0.00</li>
            </ul>
        </section>

        <!-- Sales by Payment Method -->
        <section class="report-section">
            <h2>Sales by Payment Method</h2>
            <ul>
                <li>Cash - ₱0.00</li>
                <li>Card - ₱0.00</li>
            </ul>
        </section>

        <!-- Gross Profit -->
        <section class="report-section">
            <h2>Gross Profit: ₱0.00</h2>
        </section>

        <!-- Total Discounts -->
        <section class="report-section">
            <h2>Total Discounts: ₱0.00</h2>
        </section>

        <!-- Sales Target vs Actual Sales -->
        <section class="report-section">
            <h2>Sales Target vs Actual Sales</h2>
            <p>Target: ₱10,000.00</p>
            <p>Actual Sales: ₱0.00</p>
            <p>Target Achieved: No</p>
        </section>

        <!-- Graph for Sales Data -->
        <section class="report-section">
            <h2>Sales Overview</h2>
            <canvas id="salesChart" width="400" height="200"></canvas>
            <script>
                var ctx = document.getElementById('salesChart').getContext('2d');
                var salesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Total Sales', 'Total Transactions', 'Gross Profit', 'Discounts'],
                        datasets: [{
                            label: 'Sales Report',
                            data: [0, 0, 0, 0], // Placeholder data
                            backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                            borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 99, 132, 1)'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
        </section>
    </div>

</body>
</html>
