<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "water_station";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

$stmt = $conn->prepare("
    SELECT 
        si.product_name AS product,
        si.price,
        si.quantity,
        (si.price * si.quantity) AS total,
        st.date AS timestamp,
        st.customer_name
    FROM sales_items si
    JOIN sales_transactions st ON si.transaction_id = st.id
    WHERE DATE(st.date) = ?
    ORDER BY st.date ASC, si.id ASC
");
$stmt->bind_param("s", $selectedDate);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("No sales found for the selected date.");
}


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="sales_report_' . $selectedDate . '.csv"');


$output = fopen('php://output', 'w');


fputcsv($output, ['Customer', 'Product', 'Price (₱)', 'Quantity', 'Total (₱)', 'Timestamp']);


while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['customer_name'],
        $row['product'],
        number_format($row['price'], 2),
        $row['quantity'],
        number_format($row['total'], 2),
        $row['timestamp']
    ]);
}

fclose($output);
exit();
