<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/POSu/controllers/ProductController.php';

$productController = new ProductController();
$products = $productController->getAllProducts();

// Initialize or load the sales session if not set
if (!isset($_SESSION['sales'])) {
    $_SESSION['sales'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding products to the sale
    if (isset($_POST['productCode'])) {
        $productCode = $_POST['productCode'];

        // Find product by code
        foreach ($products as $product) {
            if ($product['code'] === $productCode) {
                // Add or update the product in the sales session
                $existingProductIndex = array_search($productCode, array_column($_SESSION['sales'], 'code'));
                if ($existingProductIndex === false) {
                    $sell_price = isset($product['sell_price']) ? $product['sell_price'] : 0;
                    $_SESSION['sales'][] = [
                        'code' => $product['code'],
                        'description' => $product['description'],
                        'sell_price' => $sell_price,
                        'quantity' => 1 // Default quantity
                    ];
                }
                break;
            }
        }
    }

    // Handle quantity update
    if (isset($_POST['quantity'])) {
        $productCode = $_POST['productCode'];
        $quantity = $_POST['quantity'];

        foreach ($_SESSION['sales'] as $index => $saleItem) {
            if ($saleItem['code'] === $productCode) {
                $_SESSION['sales'][$index]['quantity'] = $quantity;
                break;
            }
        }
    }

    // Handle product removal
    if (isset($_POST['removeProduct'])) {
        $productCode = $_POST['productCode'];

        // Remove the product from the sales session
        foreach ($_SESSION['sales'] as $index => $saleItem) {
            if ($saleItem['code'] === $productCode) {
                unset($_SESSION['sales'][$index]);
                $_SESSION['sales'] = array_values($_SESSION['sales']); // Re-index the array
                break;
            }
        }
    }

    // Handle proceed and finalizing the sale
    if (isset($_POST['proceedSale'])) {
        // Get total sale amount
        $totalCost = 0;
        foreach ($_SESSION['sales'] as $saleItem) {
            $totalCost += $saleItem['sell_price'] * $saleItem['quantity'];
        }

        // Process the sale (e.g., store in the database)
        // Here you can add logic to store the sale in your database

        // Clear sales session after sale is complete
        $_SESSION['sales'] = [];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Sales</title>
    <link rel="stylesheet" href="/POSu/styles/addsales.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
 <!--  -->
</head>
<body>
<div class="top-bar">
    <a class="back-btn" href="http://localhost/POSu/dashboard">← Back</a>
    <span>Add Sales</span>
</div>

<div class="container">
    <div class="search-container">
        <form method="POST" id="salesForm">
            <select id="productDropdown" name="productCode" class="dropdown" required>
                <option value="">Search or Select a product...</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['code']; ?>">
                        <?php 
                        $sell_price = isset($product['sell_price']) ? $product['sell_price'] : 'N/A'; 
                        echo $product['code'] . " - " . $product['description'] . " (₱" . $sell_price . ")";
                        ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="table-container">
        <form method="POST">
            <table>
                <thead>
                    <tr>
                        <th>Item Code</th>
                        <th>Name & Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalCost = 0;
                    foreach ($_SESSION['sales'] as $saleItem) {
                        $itemTotal = $saleItem['sell_price'] * $saleItem['quantity'];
                        $totalCost += $itemTotal;
                        ?>
                        <tr>
                            <td><?php echo $saleItem['code']; ?></td>
                            <td><?php echo $saleItem['description']; ?></td>
                            <td>₱<?php echo $saleItem['sell_price']; ?></td>
                            <td>
                                <input type="number" name="quantity" value="<?php echo $saleItem['quantity']; ?>" min="1" onchange="this.form.submit()" required>
                                <input type="hidden" name="productCode" value="<?php echo $saleItem['code']; ?>">
                            </td>
                            <td>₱<?php echo number_format($itemTotal, 2); ?></td>
                            <td>
                                <button type="submit" name="removeProduct" value="1" class="remove-btn" data-product-code="<?php echo $saleItem['code']; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="summary">
                <span>Total: ₱<?php echo number_format($totalCost, 2); ?></span>
                <input type="number" name="cashGiven" min="0" placeholder="₱0.00" required>
                <span>Change: ₱<span id="changeAmount">0.00</span></span>
                <button type="submit" name="proceedSale" class="proceed-btn">Proceed</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2 for search functionality
        $('#productDropdown').select2({
            placeholder: "Search or Select a product...",
            allowClear: true
        });

        $('#productDropdown').on('select2:select', function (e) {
            $('#salesForm').submit();
            $('#productDropdown').select2('close'); // Closes the dropdown after selecting a product
            $('#productDropdown').focus(); // Focus back on the search bar
        });

        // Prompt when entered cash is less than total cost
        $("form").submit(function(event) {
            let totalCost = <?php echo $totalCost; ?>;
            let cashGiven = parseFloat($("input[name='cashGiven']").val()) || 0;
            if (cashGiven < totalCost) {
                event.preventDefault();
                alert("The amount entered is less than the total cost. Please enter a sufficient amount.");
            }
        });

        // Remove product handling
        $(".remove-btn").click(function(event) {
            let productCode = $(this).data("product-code");
            $("input[name='productCode']").val(productCode);
            $("button[name='removeProduct']").click();
        });
    });

    document.querySelector("input[name='cashGiven']").addEventListener('input', function() {
        let total = <?php echo $totalCost; ?>;
        let cashGiven = parseFloat(this.value) || 0;
        let change = cashGiven - total;
        document.getElementById("changeAmount").textContent = change >= 0 ? change.toFixed(2) : "0.00";
    });
</script>

</body>
</html>