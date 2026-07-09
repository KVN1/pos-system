<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 if (isset($_SESSION['flash_message'])): ?>
    <div class="flash-message <?= htmlspecialchars($_SESSION['flash_type'] ?? 'success') ?>">
        <span class="close-btn">&times;</span>
        <?= htmlspecialchars($_SESSION['flash_message']); ?>
    </div>
<?php
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
endif;


require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/controllers/ProductController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/models/DiscountModel.php';

$productController = new ProductController();
$products = $productController->indexForSales();
$discountModel = new DiscountModel();
$discounts = $discountModel->getAllDiscounts();

$_SESSION['sales'] = $_SESSION['sales'] ?? [];
$_SESSION['totalAmount'] = $_SESSION['totalAmount'] ?? 0;
$_SESSION['changeAmount'] = $_SESSION['changeAmount'] ?? 0;
$_SESSION['discountType'] = $_SESSION['discountType'] ?? '';
$_SESSION['discountPercent'] = $_SESSION['discountPercent'] ?? 0;
$_SESSION['cashGiven'] = $_SESSION['cashGiven'] ?? 0;
$_SESSION['referenceNumber'] = $_SESSION['referenceNumber'] ?? '';
$_SESSION['paymentMethod'] = $_SESSION['paymentMethod'] ?? '';   // ➜ ADD THIS


$totalCost = 0;
$discountAmt = 0;
$totalAfterDiscount = 0;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productCode = $_POST['productCode'] ?? null;

    // 1️⃣ Compute total stock for each product (main stock + batches)
    foreach ($products as &$product) {
        $mainStock = $product['stock'] ?? 0;
        $batchStock = !empty($product['batches']) ? array_sum(array_column($product['batches'], 'stock')) : 0;
        $product['total_stock'] = $mainStock + $batchStock;
    }
    unset($product);

if (isset($_POST['addProduct']) && $productCode) {
    foreach ($products as $product) {
        if (trim($product['code']) === trim($productCode)) {

            $isNP = in_array(strtolower($product['perishability'] ?? 'n/p'), ['n/p', 'non-perishable']);
            $addQty = $isNP ? 1 : 0.01; // 2 decimals for perishable

            // Build an array of all stocks (main + batches) for FIFO
            $allStocks = [];

            // Main stock (if > 0, treat as earliest expiry)
            if (($product['stock'] ?? 0) > 0) {
                $allStocks[] = [
                    'sell_price' => $product['sell_price'] ?? 0,
                    'stock' => $product['stock'],
                    'expiry_date' => '9999-12-31' // treat main stock as last to expire
                ];
            }

            // Add batches
            if (!empty($product['batches'])) {
                foreach ($product['batches'] as $b) {
                    $allStocks[] = [
                        'sell_price' => $b['sell_price'] ?? $product['sell_price'],
                        'stock' => $b['stock'],
                        'expiry_date' => $b['expiry_date'] ?? '9999-12-31'
                    ];
                }
            }

            // Sort by expiry ascending
            usort($allStocks, function($a, $b) {
                return strtotime($a['expiry_date']) <=> strtotime($b['expiry_date']);
            });

            // Pick the first stock with available quantity
            $priceToUse = 0;
            $availableQty = 0;
            foreach ($allStocks as $stock) {
                if ($stock['stock'] > 0) {
                    $priceToUse = $stock['sell_price'];
                    $availableQty = $stock['stock'];
                    break;
                }
            }

            if ($availableQty <= 0) {
                // No stock available
                $_SESSION['flash_message'] = "Product out of stock!";
                $_SESSION['flash_type'] = "error";
                break;
            }

            // Check if product already in cart
            $index = false;
            foreach ($_SESSION['sales'] as $i => $item) {
                if (trim($item['code']) === trim($productCode)) {
                    $index = $i;
                    break;
                }
            }

            if ($index === false) {
                $_SESSION['sales'][] = [
                    'code' => $product['code'],
                    'description' => $product['description'],
                    'sell_price' => $priceToUse,
                    'quantity' => min($addQty, $availableQty),
                    'perishability' => $product['perishability'] ?? 'N/P'
                ];
            } else {
                $_SESSION['sales'][$index]['quantity'] = min($_SESSION['sales'][$index]['quantity'] + $addQty, $availableQty);
            }

            break;
        }
    }
}



    // 3️⃣ Update quantity
    if (isset($_POST['updateQuantity']) && $productCode) {
        foreach ($_SESSION['sales'] as $i => $item) {
            if ($item['code'] === $productCode) {
                $isNP = in_array(strtolower($item['perishability'] ?? 'n/p'), ['n/p', 'non-perishable']);
                $newQty = (float)($_POST['quantity'] ?? 1);

                $stockQty = 0;
                foreach ($products as $p) {
                    if ($p['code'] === $item['code']) {
                        $stockQty = $p['total_stock'] ?? 0;
                        break;
                    }
                }

                $_SESSION['sales'][$i]['quantity'] = $isNP
                    ? max(1, min(round($newQty), $stockQty))
                    : max(0.01, min(round($newQty, 2), $stockQty));
                break;
            }
        }
    }

    // 4️⃣ Remove product
    if (isset($_POST['removeProduct']) && $productCode) {
        foreach ($_SESSION['sales'] as $i => $item) {
            if ($item['code'] === $productCode) {
                unset($_SESSION['sales'][$i]);
                $_SESSION['sales'] = array_values($_SESSION['sales']);
                break;
            }
        }
    }

    // 5️⃣ Apply discount
    if (isset($_POST['setDiscount'])) {
        $selected = $_POST['setDiscount'];
        $_SESSION['discountType'] = $selected;
        $_SESSION['discountPercent'] = 0;
        foreach ($discounts as $d) {
            if ($d['name'] === $selected) {
                $_SESSION['discountPercent'] = $d['percentage'];
                break;
            }
        }
    }

    // 6️⃣ Set payment method
    if (isset($_POST['setPaymentMethod'])) {
        $_SESSION['paymentMethod'] = $_POST['setPaymentMethod'];
        $_SESSION['cashGiven'] = 0;
        $_SESSION['referenceNumber'] = '';
    }

    $_SESSION['cashGiven'] = isset($_POST['cashGiven']) ? (float)$_POST['cashGiven'] : $_SESSION['cashGiven'];
    $_SESSION['referenceNumber'] = isset($_POST['referenceNumber']) ? trim($_POST['referenceNumber']) : $_SESSION['referenceNumber'];

    // 7️⃣ Compute totals
    $totalCost = 0;
    foreach ($_SESSION['sales'] as $item) {
        $totalCost += $item['sell_price'] * $item['quantity'];
    }
    $discountAmt = $totalCost * ($_SESSION['discountPercent'] / 100);
    $totalAfterDiscount = $totalCost - $discountAmt;

    $_SESSION['totalAmount'] = $totalAfterDiscount;
    $_SESSION['changeAmount'] = max(0, $_SESSION['cashGiven'] - $totalAfterDiscount);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Sales</title>
<link rel="stylesheet" href="/POSu/styles/addsales.css?v=<?= time(); ?>">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<!-- Summary Modal -->
<div id="summaryModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span id="closeSummaryBtn" class="close-btn">&times;</span>
        <h2 style="text-align:center;">Sales Summary</h2>
        <div id="summaryContent"></div>
        <hr>
        <p><strong>Date:</strong> <?= date('Y-m-d H:i:s'); ?></p>
        <p><strong>Payment:</strong> <?= htmlspecialchars($_SESSION['paymentMethod']); ?></p>
        <?php if ($_SESSION['paymentMethod']==='gcash'): ?>
            <p><strong>Ref No.:</strong> <?= htmlspecialchars($_SESSION['referenceNumber']); ?></p>
        <?php endif; ?>
        <p><strong>Total:</strong> ₱<?= number_format($totalCost,2); ?></p>
        <?php if ($_SESSION['discountPercent']>0): ?>
            <p><strong>Discount:</strong> ₱<?= number_format($discountAmt,2); ?> (<?= $_SESSION['discountType']; ?>)</p>
        <?php endif; ?>
        <p><strong>To Pay:</strong> ₱<?= number_format($totalAfterDiscount,2); ?></p>
        <p><strong>Cash:</strong> ₱<?= number_format($_SESSION['cashGiven'],2); ?></p>
        <p><strong>Change:</strong> ₱<?= number_format($_SESSION['changeAmount'],2); ?></p>
        <form method="POST" action="/POSu/controllers/SalesController.php">
            <input type="hidden" name="proceedSale" value="1">
            <button type="submit" class="proceed-btn">CONFIRM</button>
            <button type="button" id="cancelSaleBtn" class="cancel-btn">Cancel</button>
        </form>
    </div>
</div>

<div class="top-bar">
    <a class="back-btn" href="/POSu/dashboard">Back</a>
    <span>Add Sales</span>
</div>

<div class="container">
<div class="search-container">
    <input type="text" id="barcodeInput" placeholder="Scan or enter product/barcode" autofocus>
</div>


<!-- Add jQuery UI for autocomplete -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(function(){
const products = [
    <?php foreach($products as $p): ?>
        { label: "<?= htmlspecialchars($p['code'].' - '.$p['description']); ?>", value: "<?= htmlspecialchars($p['code']); ?>" },
    <?php endforeach; ?>
];


    $('#barcodeInput').autocomplete({
        source: products,
        select: function(event, ui) {
            const code = ui.item.value;
            if(!code) return;
            $.post('', { addProduct:1, productCode:code }, ()=>location.reload());
            $(this).val(''); // clear input after selection
            return false; // prevent default behavior
        },
        minLength: 0 // show all suggestions when focused
    }).focus(function(){
        $(this).autocomplete("search", ""); // show suggestions on focus
    });

    // Also handle Enter key for manual barcode input
    $('#barcodeInput').on('keypress', function(e){
        if(e.which === 13){
            e.preventDefault();
            const code = $(this).val().trim();
            if(!code) return;
            $.post('', { addProduct:1, productCode:code }, ()=>location.reload());
            $(this).val('');
        }
    });
});
</script>


    <div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Description</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Stock</th> <!-- New Stock Column -->
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($_SESSION['sales'] as $item):
                $isNP = in_array(strtolower($item['perishability'] ?? 'n/p'), ['n/p', 'non-perishable']);
                $total = $item['sell_price'] * $item['quantity'];

                // Get actual stock from $products array
$stockQty = 0;
foreach($products as $p) {
    if ($p['code'] === $item['code']) {
        $stockQty = $p['total_stock'] ?? 0; // use total_stock from all batches
        break;
    }
}

            ?>
            <tr>
                <td><?= htmlspecialchars($item['code']); ?></td>
                <td><?= htmlspecialchars($item['description']); ?></td>
                <td>₱<?= number_format($item['sell_price'],2); ?></td>
                <td>
                    <div class="quantity-wrapper">
                        <button type="button" class="qty-btn minus" data-code="<?= $item['code']; ?>">−</button>
                        <input type="number" class="qty-input" value="<?= $item['quantity']; ?>"
                               step="<?= $isNP?1:0.01; ?>" min="<?= $isNP?1:0.01; ?>" data-code="<?= $item['code']; ?>">
                        <button type="button" class="qty-btn plus" data-code="<?= $item['code']; ?>">+</button>
                    </div>
                </td>
                <td><?= $stockQty; ?></td> <!-- Display stock -->
                <td>₱<?= number_format($total,2); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="removeProduct" value="1">
                        <input type="hidden" name="productCode" value="<?= htmlspecialchars($item['code']); ?>">
                        <button type="submit" class="remove-btn">Remove</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($_SESSION['sales'])): ?>
                <tr><td colspan="7" style="text-align:center;">No items</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


    <div class="summary">
        <span class="total">Total: ₱<?= number_format($totalAfterDiscount,2); ?></span>

        <!-- Discount -->
        <form method="POST">
            <label><strong>Discount:</strong></label>
            <select name="setDiscount" onchange="this.form.submit()">
                <option disabled>Select discount</option>
                <?php foreach($discounts as $d): ?>
                    <option value="<?= $d['name']; ?>" <?= $_SESSION['discountType']===$d['name']?'selected':''; ?>>
                        <?= $d['name']; ?> (<?= $d['percentage']; ?>%)
                    </option>
                <?php endforeach; ?>
                <option value="N/A" <?= $_SESSION['discountType']==='N/A'?'selected':''; ?>>N/A</option>
            </select>
        </form>

        <!-- Payment -->
        <form method="POST">
            <label><strong>Payment:</strong></label>
            <select name="setPaymentMethod" onchange="this.form.submit()" required>
                <option disabled <?= empty($_SESSION['paymentMethod'])?'selected':''; ?>>Select</option>
                <option value="cash" <?= $_SESSION['paymentMethod']==='cash'?'selected':''; ?>>Cash</option>
                <option value="gcash" <?= $_SESSION['paymentMethod']==='gcash'?'selected':''; ?>>GCash</option>
            </select>
        </form>

        <?php if ($_SESSION['paymentMethod']==='cash'): ?>
            <form method="POST">
                <label>Cash:</label>
<input type="text" name="cashGiven" value="<?= $_SESSION['cashGiven']; ?>" 
       onchange="this.form.submit()" 
       oninput="this.value=this.value.replace(/[^0-9.]/g,'')">
            </form>
        <?php endif; ?>

        <?php if ($_SESSION['paymentMethod']==='gcash'): ?>
            <form method="POST">
                <label>Ref No.:</label>
                <input type="text" name="referenceNumber" value="<?= $_SESSION['referenceNumber']; ?>" onchange="this.form.submit()">
            </form>
        <?php endif; ?>

        <span>Change: ₱<?= number_format($_SESSION['changeAmount'],2); ?></span>
        <button type="button" id="proceedButton" class="proceed-btn" <?= empty($_SESSION['sales'])?'disabled':''; ?>>Proceed</button>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>




<script>
$(function(){

    // Prepare products array for autocomplete
    const products = [
        <?php foreach($products as $p): ?>
        { 
            label: "<?= htmlspecialchars($p['code'].' - '.$p['description'].' (₱'.$p['sell_price'].')'); ?>", 
            value: "<?= htmlspecialchars($p['code']); ?>" 
        },
        <?php endforeach; ?>
    ];

    // Single input autocomplete for barcode/product code/name
    $('#barcodeInput').autocomplete({
        source: products,
        minLength: 0, // show all suggestions on focus
        select: function(event, ui){
            const code = ui.item.value;
            if(!code) return;
            $.post('', { addProduct:1, productCode:code }, ()=>location.reload());
            $(this).val(''); // clear input
            return false; // prevent default
        }
    }).focus(function(){
        $(this).autocomplete("search", ""); // show suggestions on focus
    });

    // Handle Enter key for manual barcode input
    $('#barcodeInput').on('keypress', function(e){
        if(e.which === 13){
            e.preventDefault();
            const code = $(this).val().trim();
            if(!code) return;
            $.post('', { addProduct:1, productCode:code }, ()=>location.reload());
            $(this).val('');
        }
    });

    // Proceed button shows modal
// Proceed button click
$('#proceedButton').on('click', function(){
    const paymentMethod = $('select[name="setPaymentMethod"]').val();
    let totalToPay = parseFloat($('.total').text().replace(/[^0-9.]/g, '')) || 0;

    if (!paymentMethod) {
        alert('Please select a payment method.');
        return;
    }

    if (paymentMethod === 'cash') {
        const cashGiven = parseFloat($('input[name="cashGiven"]').val());
        if (isNaN(cashGiven) || cashGiven < totalToPay) {
            alert(`Insufficient cash. Total to pay is ₱${totalToPay.toFixed(2)}.`);
            return;
        }
    }

    if (paymentMethod === 'gcash') {
        const refNo = $('input[name="referenceNumber"]').val().trim();
        if (!/^\d{10,19}$/.test(refNo)) {
            alert('Invalid GCash reference number. It must be 10–19 digits.');
            return;
        }
    }

    // Build summary content
    let summary = '';
    <?php foreach($_SESSION['sales'] as $item): ?>
        summary += `<p><?= htmlspecialchars($item['description']); ?> × <?= $item['quantity']; ?> = ₱<?= number_format($item['sell_price']*$item['quantity'],2); ?></p>`;
    <?php endforeach; ?>
    $('#summaryContent').html(summary);

    // Show modal
    $('#summaryModal').fadeIn();
});

// Close modal
$('#closeSummaryBtn,#cancelSaleBtn').on('click', ()=>$('#summaryModal').fadeOut());


    // Quantity buttons
    $('.qty-btn').on('click', function(){
        const code = $(this).data('code');
        const input = $(`.qty-input[data-code='${code}']`);
        const step = parseFloat(input.attr('step')) || 1;
        let val = parseFloat(input.val()) || 0;
        const stockQty = parseFloat(input.closest('tr').find('td:nth-child(5)').text()) || Infinity;

        if($(this).hasClass('plus')) val += step; else val -= step;
        val = Math.max(parseFloat(input.attr('min')), val);
        if(val > stockQty){
            alert('Cannot exceed available stock!');
            val = stockQty;
        }
val = (step === 1) ? Math.round(val) : Math.round(val*100)/100;
        input.val(val);
        $.post('', { updateQuantity:1, productCode:code, quantity:val }, ()=>location.reload());
    });

    // Quantity input manual typing
    $('.qty-input').on('change', function(){
        const input = $(this);
        const code = input.data('code');
        let val = parseFloat(input.val()) || 0;
        const step = parseFloat(input.attr('step')) || 1;
        const stockQty = parseFloat(input.closest('tr').find('td:nth-child(5)').text()) || Infinity;
        const minVal = parseFloat(input.attr('min')) || 0;

        if(val > stockQty){
            alert('Cannot exceed available stock!');
            val = stockQty;
        }
        if(val < minVal) val = minVal;

        val = (step === 1) ? Math.round(val) : Math.round(val*10)/10;
        input.val(val);

        $.post('', { updateQuantity:1, productCode:code, quantity:val }, ()=>location.reload());
    });

    
    setTimeout(() => {
    $('.flash-message').fadeOut('slow');
}, 3000);
$(function(){
    const flash = $('.flash-message');
    if(flash.length){
        flash.addClass('show');           // Animate in
        setTimeout(()=>flash.removeClass('show'), 4000); // Auto-hide after 4s
    }

    // Close button click
    $('.flash-message .close-btn').on('click', function(){
        $(this).parent().removeClass('show');
    });
});



});



</script>
</body>
</html>