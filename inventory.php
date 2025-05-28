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

// Add or update product
if (isset($_POST['submit'])) {
    $product = $_POST['product'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $imageName = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "uploads/";
        $targetFile = $targetDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    $check = $conn->prepare("SELECT id FROM products WHERE name = ?");
    $check->bind_param("s", $product);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        if (!empty($imageName)) {
            $stmt = $conn->prepare("UPDATE products SET price = ?, quantity = ?, image = ? WHERE name = ?");
            $stmt->bind_param("diss", $price, $quantity, $imageName, $product);
        } else {
            $stmt = $conn->prepare("UPDATE products SET price = ?, quantity = ? WHERE name = ?");
            $stmt->bind_param("dis", $price, $quantity, $product);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, quantity, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdis", $product, $price, $quantity, $imageName);
    }

    $stmt->execute();
    $stmt->close();
}

// Delete product
if (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$products = $conn->query("SELECT * FROM products")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Water Refilling Station - Inventory</title>
    <link rel="stylesheet" href="invntory.css" />
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

<!-- Inventory Content -->
<div class="container inventory">
    <h2>Inventory Management</h2>

    <div class="inventory-form-container">
        <form method="POST" class="inventory-form" enctype="multipart/form-data">
            <label>Select existing product:</label>
            <select id="productSelect" name="product" onchange="fillProductDetails()">
                <option value="">-- Select a product --</option>
                <?php foreach ($products as $prod): ?>
                    <option value="<?= htmlspecialchars($prod['name']) ?>" data-price="<?= $prod['price'] ?>" data-quantity="<?= $prod['quantity'] ?>">
                        <?= htmlspecialchars($prod['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Or type new product:</label>
            <input type="text" id="productInput" name="product" placeholder="Enter new product name" />

            <label>Price (₱):</label>
            <input type="number" step="0.01" name="price" id="priceField" required />

            <label>Quantity:</label>
            <input type="number" name="quantity" id="quantityField" required />

            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*" />

            <button type="submit" name="submit" class="save-btn">Save / Update</button>
        </form>
    </div>

    <h3>Current Inventory</h3>
    <table class="inventory-table">
        <tr>
            <th>Product</th>
            <th>Price (₱)</th>
            <th>Quantity</th>
            <th>Image</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $prod): ?>
        <tr>
            <td><?= htmlspecialchars($prod['name']) ?></td>
            <td><?= number_format($prod['price'], 2) ?></td>
            <td><?= (int)$prod['quantity'] ?></td>
            <td>
                <?php if (!empty($prod['image'])): ?>
                    <img src="uploads/<?= htmlspecialchars($prod['image']) ?>" class="product-img" alt="<?= htmlspecialchars($prod['name']) ?>" />
                <?php else: ?>
                    No image
                <?php endif; ?>
            </td>
            <td>
                <form method="POST" onsubmit="return confirm('Delete this product?');">
                    <button type="submit" name="delete" value="<?= (int)$prod['id'] ?>" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
    function fillProductDetails() {
        const select = document.getElementById("productSelect");
        const selected = select.options[select.selectedIndex];
        const price = selected.getAttribute("data-price");
        const qty = selected.getAttribute("data-quantity");

        document.getElementById("priceField").value = price || '';
        document.getElementById("quantityField").value = qty || '';
        document.getElementById("productInput").value = select.value;
    }

    document.getElementById("productInput").addEventListener("input", () => {
        document.getElementById("productSelect").value = '';
    });
</script>

</body>
</html>
