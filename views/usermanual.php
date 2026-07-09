<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Manual - Sales and Inventory System</title>
    <link rel="stylesheet" href="/POSu/styles/stylee.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="/POSu/styles/usermanualcss.css?v=<?= time(); ?>">

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/POSu/includes/sidebar.php'; ?>

    <style>
        
        :root {
            --main-green: #2e7d32; 
            --light-green: #a5d6a7; 
            --accent-green: #66bb6a; 
            --background: #f5fff5;
            --text-dark: #2e2e2e;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--background);
            color: var(--text-dark);
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(46, 125, 50, 0.15);
            border-top: 8px solid var(--main-green);
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }

        h1 {
            color: var(--main-green);
            text-align: center;
            margin-bottom: 15px;
        }

        p {
            line-height: 1.7;
        }

        .section {
            margin-top: 30px;
            background-color: var(--light-green);
            border-left: 6px solid var(--main-green);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 100, 0, 0.1);
        }

        .section h2 {
            color: var(--main-green);
            border-bottom: 2px solid var(--accent-green);
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .steps {
            padding-left: 20px;
        }

        .step {
            background: #ffffff;
            margin-bottom: 12px;
            padding: 10px 15px;
            border-left: 4px solid var(--accent-green);
            border-radius: 5px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .step:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 100, 0, 0.15);
        }

        footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #555;
            background-color: var(--light-green);
            padding: 15px;
            border-radius: 0 0 12px 12px;
        }

        footer p {
            margin: 0;
        }

        /* Optional buttons (if you plan to add any) */
        .btn {
            background-color: var(--accent-green);
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--main-green);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>

<body>
<div class="container">
    <h1>Sales and Inventory System - User Manual</h1>
    <p>Welcome to the Sales and Inventory System. This guide will walk you through the main features of the system, including how to manage products, make sales, and keep track of inventory.</p>

    <!-- Adding Products Section -->
    <div class="section">
        <h2>Adding Products</h2>
        <p>To add a new product to the system, follow these steps:</p>
        <div class="steps">
            <div class="step"><strong>Step 1:</strong> Navigate to the <strong>Products</strong> page from the sidebar.</div>
            <div class="step"><strong>Step 2:</strong> Click the <strong>"Add Product"</strong> button located at the top left of the page.</div>
            <div class="step"><strong>Step 3:</strong> Fill in the required product details such as name, category, price, and stock quantity.</div>
            <div class="step"><strong>Step 4:</strong> After entering the details, click the <strong>"Save"</strong> button to add the product to the system.</div>
        </div>
    </div>

    <!-- Making Sales Section -->
    <div class="section">
        <h2>Making a Sale</h2>
        <p>To process a sale, follow these steps:</p>
        <div class="steps">
            <div class="step"><strong>Step 1:</strong> Go to the <strong>Sales</strong> page from the sidebar.</div>
            <div class="step"><strong>Step 2:</strong> Scan or input the barcode of the product you want to sell. You can also search for the product by name.</div>
            <div class="step"><strong>Step 3:</strong> Add the quantity of the product being sold.</div>
            <div class="step"><strong>Step 4:</strong> Review the list of items, total amount, and payment method.</div>
            <div class="step"><strong>Step 5:</strong> Click the <strong>"Proceed"</strong> button to view the sale summary.</div>
            <div class="step"><strong>Step 6:</strong> If the payment method is GCash, enter the <strong>Reference Number</strong> before confirming the sale.</div>
            <div class="step"><strong>Step 7:</strong> Finally, click <strong>"Confirm Sale"</strong> to complete the transaction.</div>
        </div>
    </div>

    <!-- Managing Inventory Section -->
    <div class="section">
        <h2>Managing Inventory</h2>
        <p>To keep track of your inventory, follow these steps:</p>
        <div class="steps">
            <div class="step"><strong>Step 1:</strong> Navigate to the <strong>Products</strong> page where all products are listed.</div>
            <div class="step"><strong>Step 2:</strong> You can view the current stock and reorder status next to each product.</div>
            <div class="step"><strong>Step 3:</strong> If a product is running low on stock, you can reorder it by clicking the <strong>"Reorder"</strong> button next to the product.</div>
        </div>
    </div>

    <!-- Troubleshooting Section -->
    <div class="section">
        <h2>Troubleshooting</h2>
        <p>If you encounter any issues, here are some common solutions:</p>
        <div class="steps">
            <div class="step">
                <strong>Issue 1:</strong> Product not showing up in the list after adding it.
                <p>✅ <em>Solution:</em> Make sure you clicked the <strong>"Save"</strong> button after entering product details.</p>
            </div>
            <div class="step">
                <strong>Issue 2:</strong> Unable to confirm a sale.
                <p>✅ <em>Solution:</em> Check that all required fields (such as product barcode, quantity, and payment method) are entered correctly.</p>
            </div>
        </div>
    </div>

    <footer>
        <p>If you need further assistance, please contact support.</p>
    </footer>
</div>
</body>
</html>