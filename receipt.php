<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$database = "water_station";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$transactionId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($transactionId <= 0) {
    echo "Invalid transaction ID.";
    exit();
}

// Fetch date and customer name
$stmtTrans = $conn->prepare("SELECT date, customer_name FROM sales_transactions WHERE id = ?");
$stmtTrans->bind_param("i", $transactionId);
$stmtTrans->execute();
$resultTrans = $stmtTrans->get_result();
$transaction = $resultTrans->fetch_assoc();

if (!$transaction) {
    echo "Transaction not found.";
    exit();
}

// Fetch 
$stmtItems = $conn->prepare("SELECT product_name, price, quantity FROM sales_items WHERE transaction_id = ?");
$stmtItems->bind_param("i", $transactionId);
$stmtItems->execute();
$resultItems = $stmtItems->get_result();

$items = [];
while ($row = $resultItems->fetch_assoc()) {
    $items[] = $row;
}

if (count($items) === 0) {
    echo "No items found for this transaction.";
    exit();
}

// calc g total
$grandTotal = 0;
foreach ($items as $item) {
    $grandTotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Receipt - Water Refilling Station</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .receipt { max-width: 400px; margin: auto; border: 1px solid #000; padding: 20px; }
        .receipt-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .receipt-header img {
            height: 50px;
            margin-right: 15px;
        }
        .receipt-header h2 {
            margin: 0;
        }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        .total { font-weight: bold; font-size: 18px; margin-top: 15px; text-align: right; }
        button { margin-top: 20px; padding: 10px; width: 100%; cursor: pointer; }
    </style>
</head>
<body>

<div class="receipt">
    <div class="receipt-header">
        <img src="assets/logo.png" alt="Logo" />
        <h2>Water Refilling Station</h2>
    </div>

    <p><strong>Customer:</strong> <?= htmlspecialchars($transaction['customer_name']) ?></p>
    <p><strong>Date:</strong> <?= date("Y-m-d", strtotime($transaction['date'])) ?></p>
    <p><strong>Time:</strong> <?= date("h:i A", strtotime($transaction['date'])) ?></p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price (₱)</th>
                <th>Quantity</th>
                <th>Total (₱)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total">Grand Total: ₱<?= number_format($grandTotal, 2) ?></p>

    <button onclick="window.print()">Print Receipt</button>
</div>

</body>
</html>
