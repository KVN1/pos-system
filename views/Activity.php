<?php
require_once __DIR__ . '/../controllers/ActivityLogController.php';

$search = $_GET['search'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$controller = new ActivityLogController();
$logs = $controller->getLogs($search, $startDate, $endDate, 1000, 0); // load all filtered logs

// Function to determine entity type
function getEntity($log) {
    $text = strtolower($log['action'] . ' ' . $log['details']);
    if (strpos($text, 'batch') !== false || strpos($text, 'product') !== false) return 'Product';
    if (strpos($text, 'sale') !== false) return 'Sales';
    if (strpos($text, 'category') !== false) return 'Category';
    if (strpos($text, 'user') !== false) return 'User';
    return 'Other';
}

// Group logs by entity
$entityLogs = [];
foreach ($logs as $log) {
    $entity = getEntity($log);
    $entityLogs[$entity][] = $log;
}
?>

<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<link rel="stylesheet" href="/styles/stylee.css">
<link rel="stylesheet" href="/styles/activitycss.css">

<div class="main-content">
    <h2>Activity Log</h2>

    <form class="search-form" method="GET">
        <input type="text" placeholder="Search by action or details..." name="search" value="<?= htmlspecialchars($search) ?>">
        <label>From:<input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>"></label>
        <label>To:<input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>"></label>
        <button type="submit">Filter</button>
    </form>

    <?php foreach ($entityLogs as $entity => $logsArray): ?>
        <div class="entity-section">
            <h3><?= htmlspecialchars($entity) ?> Logs</h3>
            <table class="table" data-rows-per-page="10">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>User ID</th>
                        <th>Action</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logsArray as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['log_time']) ?></td>
                            <td><?= htmlspecialchars($log['user_id']) ?></td>
                            <td><?= htmlspecialchars($log['action']) ?></td>
                            <td><?= htmlspecialchars($log['details']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="table-pagination" style="text-align:center; margin-top:10px;">
                <button class="prev-btn">Prev</button>
                <span class="page-info">1 / 1</span>
                <button class="next-btn">Next</button>
            </div>
        </div>
        <br>
    <?php endforeach; ?>
</div>

<style>
/* Table and pagination styling */
.table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
.table th, .table td { padding: 8px 10px; border: 1px solid #ddd; text-align: left; }
.table th { background-color: #cfcfcf; }
.table tbody tr:hover { background-color: #f1f1f1; }

.table-pagination button {
    padding: 5px 12px;
    margin: 0 5px;
    background-color: #6c7a6c;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
    transition: background 0.2s;
}
.table-pagination button:hover { background-color: #556655; }
.table-pagination .page-info {
    font-weight: bold;
    margin: 0 8px;
}
</style>

<script>
// Arrow-based table pagination
document.querySelectorAll('table[data-rows-per-page]').forEach(function(table) {
    const rowsPerPage = parseInt(table.getAttribute('data-rows-per-page'));
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const paginationContainer = table.nextElementSibling;
    const prevBtn = paginationContainer.querySelector('.prev-btn');
    const nextBtn = paginationContainer.querySelector('.next-btn');
    const pageInfo = paginationContainer.querySelector('.page-info');

    let currentPage = 1;
    const totalPages = Math.ceil(rows.length / rowsPerPage);

    function showPage(page) {
        currentPage = page;
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;

        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });

        pageInfo.textContent = `${currentPage} / ${totalPages}`;
    }

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) showPage(currentPage - 1);
    });
    nextBtn.addEventListener('click', () => {
        if (currentPage < totalPages) showPage(currentPage + 1);
    });

    showPage(1);
});
</script>
