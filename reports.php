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

$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Prepare query joining sales_items and sales_transactions, including customer_name
$stmt = $conn->prepare("
    SELECT 
        si.product_name AS product,
        si.price,
        si.quantity,
        (si.price * si.quantity) AS total,
        st.date AS timestamp,
        st.customer_name,
        st.id AS transaction_id
    FROM sales_items si
    JOIN sales_transactions st ON si.transaction_id = st.id
    WHERE DATE(st.date) = ?
    ORDER BY st.date ASC, si.id ASC
");
$stmt->bind_param("s", $selectedDate);
$stmt->execute();
$result = $stmt->get_result();

$sales = [];
while ($row = $result->fetch_assoc()) {
    $sales[] = $row;
}

// Group sales by transaction_id and calculate transaction totals
$transactions = [];
foreach ($sales as $sale) {
    $tid = $sale['transaction_id'];
    if (!isset($transactions[$tid])) {
        $transactions[$tid] = [
            'items' => [],
            'total' => 0,
            'timestamp' => $sale['timestamp'],
            'customer_name' => $sale['customer_name']
        ];
    }
    $transactions[$tid]['items'][] = $sale;
    $transactions[$tid]['total'] += $sale['total'];
}

// Calculate grand total
$grandTotal = 0;
foreach ($transactions as $t) {
    $grandTotal += $t['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reports - Water Refilling Station</title>
    <link rel="stylesheet" href="reports.css" />
</head>
<body>

<div class="navbar">
    <div class="logo-title">
        <img src="assets/logo.png" alt="Logo" class="logo" />
        <h1>WATER REFILLING STATION</h1>
        <span class="current-user"><?= htmlspecialchars($_SESSION['user']) ?></span>
    </div>
    <div class="nav-links">
        <a href="pos.php">POS</a>
        <a href="inventory.php">Inventory</a>
        <a href="reports.php">Reports</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Sales Report</h2>

    <form method="GET" style="display:inline-block;">
        <label for="date">Select Date:</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" required />
        <button type="submit" class="submit-btn">View</button>
    </form>

    <!-- Export CSV button -->
    <a href="export_sales.php?date=<?= urlencode($selectedDate) ?>" class="export-btn">Export CSV</a>

    <table>
        <thead>
            <tr>
                <th>Customer</th>
                <th>Product</th>
                <th>Price (₱)</th>
                <th>Quantity</th>
                <th>Total (₱)</th>
                <th>Time</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($transactions) > 0): ?>
            <?php foreach ($transactions as $transactionId => $transaction): ?>
                <?php foreach ($transaction['items'] as $index => $sale): ?>
                    <tr>
                        <?php if ($index === 0): ?>
                            <td rowspan="<?= count($transaction['items']) ?>"><?= htmlspecialchars($transaction['customer_name']) ?></td>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($sale['product']) ?></td>
                        <td><?= number_format($sale['price'], 2) ?></td>
                        <td><?= (int)$sale['quantity'] ?></td>
                        <td>
                            <?php if ($index === 0): ?>
                                <?= number_format($transaction['total'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= date("h:i A", strtotime($transaction['timestamp'])) ?></td>
                        <td>
                            <?php if ($index === 0): ?>
                                <a href="receipt.php?id=<?= (int)$transactionId ?>" target="_blank">Print Receipt</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">No sales found for <?= htmlspecialchars($selectedDate) ?>.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="total">Total Sales: ₱<?= number_format($grandTotal, 2) ?></div>
</div>

</body>
</html>
