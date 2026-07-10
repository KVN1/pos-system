<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../controllers/SalesReportController.php';

$reportController = new SalesReportController();

// Default filter values
$from = $_GET['from_date'] ?? null;
$to = $_GET['to_date'] ?? null;
$payment = $_GET['payment_method'] ?? '';
$user = $_GET['user_id'] ?? '';
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$limit = 15;
$offset = ($currentPage - 1) * $limit;

// Fetch filtered sales data with pagination
$sales = $reportController->getFilteredSales($from, $to, $payment, $user, $currentPage);
$totalSales = $reportController->getTotalSales($from, $to, $payment, $user);
$totalTransactions = $reportController->getTotalTransactions($from, $to, $payment, $user);

$allSalesItems = [];
foreach ($sales as $s) {
    $allSalesItems[$s['id']] = $reportController->getSaleItems($s['id']);
}

// Fetch best and slow-moving products
$bestSellers = $reportController->getBestSellers($from, $to);
$slowMovingProducts = $reportController->getSlowMovingProducts($from, $to);

// Fetch user list for dropdown
$users = $reportController->getAllUsers();

// Fetch all data for modals (no pagination)
$allSales = $reportController->getFilteredSales($from, $to, $payment, $user, null); // null = all
$allBestSellers = $reportController->getBestSellers($from, $to, null); // all
$allSlowProducts = $reportController->getSlowMovingProducts($from, $to, null); // all

$totalOverallMarkup = 0;
foreach ($allSales as $sale) {
    if (isset($sale['status']) && $sale['status'] === 'Returned') {
        continue; // skip returned sales
    }

    $items = $reportController->getSaleItems($sale['id']);
    foreach ($items as $item) {
        if (isset($item['status']) && $item['status'] === 'Returned') {
            continue; // skip returned items
        }

        $markup = $item['price'] - $item['buy_price']; // per item
        $totalOverallMarkup += $markup * $item['quantity'];
    }
}

$returnedItems = [];
foreach ($allSales as $sale) {
    if (isset($sale['status']) && $sale['status'] === 'Returned') {
        // If entire sale is returned, add all items
        $items = $reportController->getSaleItems($sale['id']);
        foreach ($items as $item) {
            $item['sale_id'] = $sale['id'];
            $item['sale_date'] = $sale['sale_date'];
            $item['user_name'] = $sale['first_name'].' '.$sale['last_name'];
            $returnedItems[] = $item;
        }
    } else {
        // Check for individually returned items
        $items = $reportController->getSaleItems($sale['id']);
        foreach ($items as $item) {
            if (isset($item['status']) && $item['status'] === 'Returned') {
                $item['sale_id'] = $sale['id'];
                $item['sale_date'] = $sale['sale_date'];
                $item['user_name'] = $sale['first_name'].' '.$sale['last_name'];
                $returnedItems[] = $item;
            }
        }
    }
}

$totalReturnedItems = count($returnedItems);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Report</title>
<link rel="stylesheet" href="/styles/stylee.css?v=<?= time(); ?>">
<link rel="stylesheet" href="/styles/sales-style.css?v=<?= time(); ?>">
<style>
.modal {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}
.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    width: 90%;
    max-width: 1200px;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
}
.modal-content h2 { margin-top: 0; }
.modal .close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

/* Pagination styling */
.pagination span {
    font-weight: bold;
    font-size: 16px;
    margin: 0 10px;
}
.color-box { width: 18px; height: 18px; border-radius: 4px; margin-right: 10px; border: 1px solid #aaa; }
.color-green { background-color: #66cc66; }
.color-orange { background-color: #ffcc66; }
.color-red { background-color: #ff6666; }
.legend-item { display: flex; align-items: center; margin-bottom: 6px; }
.btn-download { background-color: #4CAF50; color: white; border: none; padding: 8px 14px; border-radius: 6px; cursor: pointer; transition: background 0.3s; }
.btn-download:hover { background-color: #45a049; }
.returned-sale { background-color: #ffcc66 !important; }
.delete-btn { padding: 5px 10px; background: #d9534f; color: white; border: none; border-radius: 4px; cursor: pointer; }
.view-btn { padding: 5px 10px; background: #0275d8; color: white; border: none; border-radius: 4px; cursor: pointer; }
</style>
</head>
<body>
<div class="dashboard">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="main-content">
        <header class="header" style="display: flex; align-items: center; justify-content: space-between;">
    <h1>Sales Report</h1>
    <div class="actions">
        <button class="btn-download" onclick="exportCSV()">Download CSV</button>
        <button class="btn-download" onclick="window.print()">Print Page</button>
        <button class="btn-download" onclick="exportPDF()">Export PDF</button>
    </div>
</header>

<script>
// CSV export function replaces the previous form
function exportCSV() {
    const from = "<?= htmlspecialchars($from ?? '') ?>";
    const to = "<?= htmlspecialchars($to ?? '') ?>";
    const payment = "<?= htmlspecialchars($payment ?? '') ?>";
    const user = "<?= htmlspecialchars($user ?? '') ?>";

    const url = `exportSales.php?from_date=${from}&to_date=${to}&payment_method=${payment}&user_id=${user}`;
    window.location.href = url;
}
</script>


        <div class="legend-container">
            <div class="legend-section">
                <h3>Sales Report Level Legend</h3>
                <div class="legend-item"><div class="color-box color-orange"></div>Sales was returned</div>
            </div>
        </div>

        <section class="content">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php elseif(isset($_SESSION['error'])): ?>
                <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="sales-report-container">
                <!-- Filters -->
                <form method="GET" class="filters">
                    <label>From: <input type="date" name="from_date" value="<?= htmlspecialchars($from) ?>"></label>
                    <label>To: <input type="date" name="to_date" value="<?= htmlspecialchars($to) ?>"></label>
                    <label>Payment Method:
                        <select name="payment_method">
                            <option value="">All</option>
                            <option value="Cash" <?= $payment==='Cash'?'selected':'' ?>>Cash</option>
                            <option value="Gcash" <?= $payment==='Gcash'?'selected':'' ?>>Gcash</option>
                        </select>
                    </label>
                    <label>User:
                        <select name="user_id">
                            <option value="">All</option>
                            <?php foreach($users as $u): ?>
                                <option value="<?= $u['user_id'] ?>" <?= $user==$u['user_id']?'selected':'' ?>>
                                    <?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <button type="submit" class="filter-btn">Filter</button>
                </form>

<!-- Summary Cards -->
<div class="summary-cards">
    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <div class="card">
            <h3>Total Sales</h3>
            <p>₱<?= number_format($totalSales,2) ?></p>
        </div>

        <div class="card">
            <h3>Total Overall Markup</h3>
            <p>₱<?= number_format($totalOverallMarkup,2) ?></p>
        </div>
    <?php endif; ?>

    <div class="card">
        <h3>Total Transactions</h3>
        <p><?= $totalTransactions ?></p>
    </div>

    <?php if(isset($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <div class="card card-clickable" onclick="openReturnedItemsModal()">
            <h3>Total Returned Items</h3>
            <p><?= $totalReturnedItems ?></p>
        </div>
    <?php endif; ?>
</div>


                <!-- Sales Table -->
                <h2>Sales <a href="#" class="page-btn" onclick="openSeeAll('sales')">See All</a></h2>
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID</th>
                            <th>User</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Sale Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($sales)): $counter=$offset+1; ?>
                            <?php foreach($sales as $sale): 
                                $isReturned = isset($sale['status']) && $sale['status'] === 'Returned';
                            ?>
                                <tr class="<?= $isReturned?'returned-sale':'' ?>">
                                    <td><?= $counter++ ?></td>
                                    <td><?= $sale['id'] ?></td>
                                    <td><?= htmlspecialchars($sale['first_name'].' '.$sale['last_name']) ?></td>
                                    <td>₱<?= number_format($sale['total_amount'],2) ?></td>
                                    <td><?= htmlspecialchars($sale['payment_method']) ?></td>
                                    <td><?= htmlspecialchars($sale['sale_date']) ?></td>
                                    <td>
                                        <button class="view-btn" onclick="openSaleItemsModal(<?= $sale['id'] ?>)">View</button>
                                        <?php if($_SESSION['role']==='admin' && !$isReturned): ?>
<button type="button" class="delete-btn" onclick="openSaleReturnModal(<?= $sale['id'] ?>)">Return</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="no-data">No sales found for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if($currentPage>1): ?>
                        <a href="?page=<?= $currentPage-1 ?>&from_date=<?= $from ?>&to_date=<?= $to ?>&payment_method=<?= $payment ?>&user_id=<?= $user ?>" class="page-btn">Previous</a>
                    <?php endif; ?>
                    <span>Page <?= $currentPage ?></span>
                    <?php if(count($sales)==$limit): ?>
                        <a href="?page=<?= $currentPage+1 ?>&from_date=<?= $from ?>&to_date=<?= $to ?>&payment_method=<?= $payment ?>&user_id=<?= $user ?>" class="page-btn">Next</a>
                    <?php endif; ?>
                </div>

                <!-- Best Sellers Table -->
                <h2>Best Sellers <a href="#" class="page-btn" onclick="openSeeAll('best')">See All</a></h2>
                <table class="best-sellers-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <?php if($_SESSION['role']==='admin'): ?><th>Total Sales</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter=1; foreach($bestSellers as $b): ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($b['product_name']) ?></td>
                            <td><?= $b['quantity_sold'] ?></td>
                            <?php if($_SESSION['role']==='admin'): ?><td>₱<?= number_format($b['total_sales'],2) ?></td><?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Slow Moving Products Table -->
                <h2>Slow Moving Products <a href="#" class="page-btn" onclick="openSeeAll('slow')">See All</a></h2>
                <table class="slow-moving-products-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Quantity Sold</th>
                            <?php if($_SESSION['role']==='admin'): ?><th>Total Sales</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter=1; foreach($slowMovingProducts as $s): ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($s['product_name']) ?></td>
                            <td><?= $s['quantity_sold'] ?></td>
                            <?php if($_SESSION['role']==='admin'): ?><td>₱<?= number_format($s['total_sales'],2) ?></td><?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </section>
    </main>
</div>

<!-- Returned Items Modal -->
<div id="returned-items-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeReturnedItemsModal()">&times;</span>
        <h2>Returned Items</h2>
        <div id="returned-items-content" style="max-height:70vh; overflow:auto;"></div>
        <div id="returned-items-pagination" style="margin-top:10px;"></div> <!-- move inside -->
    </div>
</div>



<!-- See All Modal -->
<div id="see-all-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSeeAllModal()">&times;</span>
        <h2 id="see-all-title"></h2>
        <div id="see-all-content" style="max-height:70vh; overflow:auto;"></div>
    </div>
</div>

<!-- Sale Items Modal -->
<div id="sale-items-modal" class="modal">
    <div class="modal-content">
        
        <span class="close" onclick="closeSaleItemsModal()">&times;</span>
        <h2>Sale Details</h2>
        <div id="sale-items-content" style="max-height:70vh; overflow:auto;"></div>
    </div>
</div>

<!-- Item Return Modal -->
<div id="item-return-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeItemReturnModal()">&times;</span>
        <h2>Return Item</h2>
        <form id="item-return-form" method="POST" action="return_item.php" onsubmit="return confirmItemReturn()">
            <input type="hidden" name="sale_id" id="return-sale-id">
            <input type="hidden" name="return_item_id" id="return-item-id">
            <label for="item-return-reason"><strong>Reason for Item Return:</strong></label>
            <textarea id="item-return-reason" name="return_reason" rows="4" required placeholder="Enter reason for returning this item..."></textarea>
            <br><br>
            <button type="submit" class="delete-btn" id="item-return-confirm-btn">Confirm Return</button>
        </form>
    </div>
</div>

<!-- Return Sale Modal -->
<div id="sale-return-modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeSaleReturnModal()">&times;</span>
        <h2>Return Entire Sale</h2>
        <form id="sale-return-form" method="POST" action="return_sale.php" onsubmit="return confirmSaleReturn()">
            <input type="hidden" name="sale_id" id="return-sale-id-full">

            <label><strong>Reason for returning entire sale:</strong></label>
            <textarea name="return_reason" id="sale-return-reason" rows="4" required></textarea>

            <br><br>
            <button type="submit" class="delete-btn">Confirm Return</button>
        </form>
    </div>
</div>


<script>
const allSalesItems = <?= json_encode($allSalesItems) ?>;
const allSalesData = <?= json_encode($allSales) ?>;
const allBestSellersData = <?= json_encode($allBestSellers) ?>;
const allSlowProductsData = <?= json_encode($allSlowProducts) ?>;
const role = '<?= $_SESSION['role'] ?? '' ?>';
const returnedItemsData = <?= json_encode($returnedItems) ?>;

// Pagination data
let returnedItems = returnedItemsData;
let returnedPage = 1;
let returnedPerPage = 20;



function openReturnedItemsModal(page = 1) {
    returnedPage = page;
    renderReturnedItems();
    document.getElementById('returned-items-modal').style.display = 'flex';
}

function closeReturnedItemsModal() {
    document.getElementById('returned-items-modal').style.display = 'none';
}

function renderReturnedItems() {
    const content = document.getElementById('returned-items-content');

    let start = (returnedPage - 1) * returnedPerPage;
    let end = start + returnedPerPage;
    let dataSlice = returnedItems.slice(start, end);

    let html = `
        <table class="modal-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sale ID</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Sale Date</th>
                </tr>
            </thead>
            <tbody>
    `;

    dataSlice.forEach((item, index) => {
        html += `
            <tr>
                <td>${start + index + 1}</td>
                <td>${item.sale_id}</td>
                <td>${item.user_name}</td>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>₱${parseFloat(item.price).toFixed(2)}</td>
                <td>₱${parseFloat(item.total_amount).toFixed(2)}</td>
                <td>${item.sale_date}</td>
            </tr>
        `;
    });

    html += `</tbody></table>`;
    content.innerHTML = html;

    renderReturnedPagination();
}

function renderReturnedPagination() {
    const pagination = document.getElementById('returned-items-pagination');

    let totalPages = Math.ceil(returnedItems.length / returnedPerPage);
    let html = "";

    if (returnedPage > 1) {
        html += `<button class="page-btn" onclick="openReturnedItemsModal(${returnedPage - 1})">Previous</button>`;
    }

    for (let i = 1; i <= totalPages; i++) {
        html += `
            <button class="page-btn ${i === returnedPage ? 'active' : ''}"
            onclick="openReturnedItemsModal(${i})">${i}</button>
        `;
    }

    if (returnedPage < totalPages) {
        html += `<button class="page-btn" onclick="openReturnedItemsModal(${returnedPage + 1})">Next</button>`;
    }

    pagination.innerHTML = html;
}


function openSeeAll(type){
    const modal = document.getElementById('see-all-modal');
    const title = document.getElementById('see-all-title');
    const content = document.getElementById('see-all-content');
    let html='';

    if(type==='sales'){
        title.textContent='All Sales';
        html+=`<table class="modal-table">
        <thead><tr><th>#</th><th>ID</th><th>User</th><th>Total</th><th>Payment</th><th>Date</th></tr></thead><tbody>`;
        let c=1;
        allSalesData.forEach(s=>{
            const returned = s.status === 'Returned';
            html+=`<tr style="${returned?'background:#ffe6b3;':''}">
            <td>${c++}</td><td>${s.id}</td><td>${s.first_name} ${s.last_name}</td>
            <td>₱${parseFloat(s.total_amount).toFixed(2)}</td>
            <td>${s.payment_method}</td><td>${s.sale_date}</td></tr>`;
        });
        html+='</tbody></table>';
    }

    else if(type==='best'){
        title.textContent='All Best Sellers';
        html+=`<table class="modal-table">
        <thead><tr><th>#</th><th>Product</th><th>Qty Sold</th>${role==='admin'?'<th>Total Sales</th>':''}</tr></thead><tbody>`;
        let c=1;
        allBestSellersData.forEach(b=>{
            html+=`<tr><td>${c++}</td><td>${b.product_name}</td><td>${b.quantity_sold}</td>
            ${role==='admin'?`<td>₱${parseFloat(b.total_sales).toFixed(2)}</td>`:''}</tr>`;
        });
        html+='</tbody></table>';
    }

    else if(type==='slow'){
        title.textContent='All Slow Moving Products';
        html+=`<table class="modal-table">
        <thead><tr><th>#</th><th>Product</th><th>Qty Sold</th>${role==='admin'?'<th>Total Sales</th>':''}</tr></thead><tbody>`;
        let c=1;
        allSlowProductsData.forEach(s=>{
            html+=`<tr><td>${c++}</td><td>${s.product_name}</td><td>${s.quantity_sold}</td>
            ${role==='admin'?`<td>₱${parseFloat(s.total_sales).toFixed(2)}</td>`:''}</tr>`;
        });
        html+='</tbody></table>';
    }

    content.innerHTML = html;
    modal.style.display = 'flex';
}

function closeSeeAllModal(){ 
    document.getElementById('see-all-modal').style.display='none'; 
}


function openSaleItemsModal(saleId) {
    const items = allSalesItems[saleId] || [];
    let totalMarkup = 0;
    let totalGain = 0;

    let html = `<table class="modal-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Markup</th>
                <th>Total</th>
                ${role==='admin'?'<th>Action</th>':''}
            </tr>
        </thead>
        <tbody>`;

    items.forEach((item, index) => {
        const returned = item.status === 'Returned';
        const markup = parseFloat(item.price) - parseFloat(item.buy_price);
        const totalItemMarkup = markup * item.quantity;
        totalMarkup += totalItemMarkup;

        const gain = parseFloat(item.total_amount) - (parseFloat(item.buy_price) * item.quantity);
        totalGain += gain;

        html += `
            <tr style="${returned ? 'background:#ffe6b3;' : ''}">
                <td>${index + 1}</td>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>₱${parseFloat(item.price).toFixed(2)}</td>
                <td>₱${totalItemMarkup.toFixed(2)}</td>
                <td>₱${parseFloat(item.total_amount).toFixed(2)}</td>
                ${role==='admin' 
                    ? `<td>${returned ? '<span style="color:orange;">Returned</span>' 
                                     : '<button onclick="openItemReturnModal('+saleId+','+item.id+')" class="delete-btn">Return</button>'}</td>` 
                    : ''}
            </tr>`;
    });

    html += `
        <tr>
            <td colspan="4" style="text-align:right;font-weight:bold;">Total Markup:</td>
            <td colspan="3" style="font-weight:bold;">₱${totalMarkup.toFixed(2)}</td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:right;font-weight:bold;">Total Gain:</td>
            <td colspan="3" style="font-weight:bold;">₱${totalGain.toFixed(2)}</td>
        </tr>
    `;

    html += '</tbody></table>';

    document.getElementById('sale-items-content').innerHTML = html;
    document.getElementById('sale-items-modal').style.display = 'flex';
}

function closeSaleItemsModal(){ 
    document.getElementById('sale-items-modal').style.display='none'; 
}


function openItemReturnModal(saleId, itemId) {
    document.getElementById('return-sale-id').value = saleId;
    document.getElementById('return-item-id').value = itemId;
    document.getElementById('item-return-reason').value = '';
    document.getElementById('item-return-modal').style.display = 'flex';
}

function closeItemReturnModal() {
    document.getElementById('item-return-modal').style.display = 'none';
}

function confirmItemReturn() {
    const reason = document.getElementById('item-return-reason').value.trim();
    if(!reason){
        alert('Please enter a reason for return.');
        return false;
    }
    return confirm('Are you sure you want to return this item?');
}


function openSaleReturnModal(saleId) {
    document.getElementById('return-sale-id-full').value = saleId;
    document.getElementById('sale-return-reason').value = '';
    document.getElementById('sale-return-modal').style.display = 'flex';
}

function closeSaleReturnModal() {
    document.getElementById('sale-return-modal').style.display = 'none';
}

function confirmSaleReturn() {
    const reason = document.getElementById('sale-return-reason').value.trim();
    if (!reason) {
        alert("Please enter a reason for the sale return.");
        return false;
    }
    return confirm("Are you sure you want to return the entire sale?");
}


window.onclick = function(event){
    const modals = [
        'see-all-modal',
        'sale-items-modal',
        'item-return-modal',
        'sale-return-modal',
        'returned-items-modal'   
    ];

    modals.forEach(id=>{
        const modal = document.getElementById(id);
        if(event.target === modal) modal.style.display = 'none';
    });
};
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportPDF() {
    const element = document.querySelector('.sales-report-container');
    const opt = {
        margin:       0.5,
        filename:     'SalesReport.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
<script>
function printModal(id){
    const content = document.getElementById(id).innerHTML;
    const myWindow = window.open('', '', 'width=800,height=600');
    myWindow.document.write('<html><head><title>Print</title></head><body>');
    myWindow.document.write(content);
    myWindow.document.write('</body></html>');
    myWindow.document.close();
    myWindow.print();
}

function exportModalPDF(id, filename='ModalExport.pdf'){
    const element = document.getElementById(id);
    html2pdf().from(element).save(filename);
}
</script>


</body>
</html>
