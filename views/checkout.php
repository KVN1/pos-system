<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="/css/stylee.css">
</head>
<body>

<div class="top-bar">
    <button class="back-btn" onclick="window.history.back()">← Back</button>
    <h1>Checkout</h1>
</div>

<div class="container">
    <div class="table-container">
        <table class="styled-table">
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Name & Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="checkoutTable">
            </tbody>
        </table>
    </div>
    <div class="checkout-section">
        <label>Total Amount: ₱<span id="totalAmount">0.00</span></label>

        <label for="cashInput">Cash Amount:</label>
        <input type="number" id="cashInput" placeholder="Enter cash received" oninput="calculateChange()">

        <label>Change: ₱<span id="changeAmount">0.00</span></label>
    </div>
</div>

<div class="summary">
    <span>Total: ₱<span id="finalTotal">0.00</span></span>
    <button class="confirm-btn" id="confirmBtn" disabled onclick="finalizeTransaction()">Confirm & Print</button>
</div>

<script>
let totalCost = 0;
let cartItems = [
    { code: "12345", name: "Nike Air Max", price: 5000, quantity: 1 },
    { code: "67890", name: "Carhartt Hoodie", price: 4000, quantity: 2 }
];

function loadCartItems() {
    let table = document.getElementById("checkoutTable");
    table.innerHTML = ""; // Clear table first

    totalCost = 0;

    cartItems.forEach(item => {
        let row = document.createElement("tr");
        let total = item.price * item.quantity;
        totalCost += total;

        row.innerHTML = `
            <td>${item.code}</td>
            <td>${item.name}</td>
            <td>₱${item.price.toFixed(2)}</td>
            <td>${item.quantity}</td>
            <td>₱${total.toFixed(2)}</td>
        `;

        table.appendChild(row);
    });

    document.getElementById("totalAmount").innerText = totalCost.toFixed(2);
    document.getElementById("finalTotal").innerText = totalCost.toFixed(2);
}

function calculateChange() {
    let cashInput = parseFloat(document.getElementById("cashInput").value) || 0;
    let change = cashInput - totalCost;

    document.getElementById("changeAmount").innerText = change.toFixed(2);

    let confirmBtn = document.getElementById("confirmBtn");
    confirmBtn.disabled = cashInput < totalCost;
}

function finalizeTransaction() {
    alert("Transaction successful! Printing receipt...");
    window.location.href = "/views/dashboard.php";
}

loadCartItems();
</script>

</body>
</html>
