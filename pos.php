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

$message = "";
$lastTransactionId = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart'])) {
    $cart = json_decode($_POST['cart'], true);
    $customerName = isset($_POST['customer_name']) && trim($_POST['customer_name']) !== '' 
                    ? trim($_POST['customer_name']) 
                    : 'Walk-in Customer';

    if (!$cart || count($cart) === 0) {
        $message = "Order list is empty!";
    } else {
        // Check stock availability
        $stockOk = true;
        foreach ($cart as $item) {
            $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
            $stmt->bind_param("i", $item['product_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            if (!$product || $product['quantity'] < $item['quantity']) {
                $stockOk = false;
                $message = "Not enough stock for product: " . htmlspecialchars($item['product_name']);
                break;
            }
        }

        if ($stockOk) {
            // Insert sale record (transaction) with customer name
            $insertSale = $conn->prepare("INSERT INTO sales_transactions (user, customer_name, date) VALUES (?, ?, NOW())");
            $insertSale->bind_param("ss", $_SESSION['user'], $customerName);
            $insertSale->execute();
            $lastTransactionId = $conn->insert_id;

            // Insert sale items and update stock
            foreach ($cart as $item) {
                $insertItem = $conn->prepare("INSERT INTO sales_items (transaction_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
                $insertItem->bind_param("iisdi", $lastTransactionId, $item['product_id'], $item['product_name'], $item['price'], $item['quantity']);
                $insertItem->execute();

                $updateStock = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                $updateStock->bind_param("ii", $item['quantity'], $item['product_id']);
                $updateStock->execute();
            }

            $message = "Purchase successful!";
        }
    }
}

$products = $conn->query("SELECT * FROM products")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>POS</title>
    <link rel="stylesheet" href="pos.css">
</head>
<body>

<div class="navbar">
    <div class="logo-title">
        <img src="assets/logo.png" alt="Logo" class="logo">
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

<?php if (!empty($message)): ?>
    <p class="<?= (strpos($message, 'successful') !== false) ? 'message-success' : 'message-error' ?>"
       style="text-align: center; font-size: 1.5em; font-weight: bold; margin: 25px 0;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<form id="cartForm" method="POST" onsubmit="return submitCart()" style="width: 90%; margin: auto;">

    <div class="inventory-dashboard">
      <?php foreach ($products as $prod): ?>
        <div class="inventory-card">
          <img src="uploads/<?= htmlspecialchars($prod['image']) ?>" alt="<?= htmlspecialchars($prod['name']) ?>" />
          <h3><?= htmlspecialchars($prod['name']) ?></h3>
          <p>₱<?= number_format($prod['price'], 2) ?></p>
          <small><?= $prod['quantity'] ?> available</small>
          <button type="button" onclick="addToCart(<?= $prod['id'] ?>, '<?= htmlspecialchars(addslashes($prod['name'])) ?>', <?= $prod['price'] ?>, <?= $prod['quantity'] ?>)">Add to Order</button>
        </div>
      <?php endforeach; ?>
    </div>

    <h2 style="text-align:center; margin-top: 40px;">Order List</h2>
    <table id="cartTable" border="1" cellpadding="5" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #5D5DEF; color: white;">
          <tr>
            <th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Remove</th>
          </tr>
        </thead>
        <tbody></tbody>
    </table>
    <p style="text-align: right; font-size: 18px; margin-top: 10px;">Total: ₱<span id="cartTotal">0.00</span></p>

    <div style="margin: 15px 0 5px 0; max-width: 300px;">
      <label for="customer_name" style="display: block; font-weight: bold; margin-bottom: 5px;">Customer Name:</label>
      <input type="text" id="customer_name" name="customer_name" placeholder="Enter customer name" required style="width: 100%; padding: 6px; border-radius: 4px; border: 1px solid #ccc;">
    </div>

    <input type="hidden" name="cart" id="cartInput" />
    <button type="submit" style="background-color: orange; color: #012A6A; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; font-weight: bold; display: block; margin: 20px auto; transition: background-color 0.3s;">Confirm Purchase</button>
</form>

<script>
  let cart = [];

  function addToCart(id, name, price, stock) {
    const existing = cart.find(item => item.product_id === id);
    if (existing) {
      if (existing.quantity < stock) {
        existing.quantity++;
      } else {
        alert('No more stock available for ' + name);
        return;
      }
    } else {
      cart.push({product_id: id, product_name: name, price: price, quantity: 1});
    }
    renderCart();
  }

  function removeFromCart(id) {
    cart = cart.filter(item => item.product_id !== id);
    renderCart();
  }

  function changeQuantity(id, qty) {
    const item = cart.find(i => i.product_id === id);
    if (!item) return;
    qty = parseInt(qty);
    if (isNaN(qty) || qty < 1) qty = 1;
    item.quantity = qty;
    renderCart();
  }

  function renderCart() {
    const tbody = document.querySelector('#cartTable tbody');
    tbody.innerHTML = '';
    let total = 0;
    cart.forEach(item => {
      const row = document.createElement('tr');
      const itemTotal = item.price * item.quantity;
      total += itemTotal;
      row.innerHTML = `
        <td>${item.product_name}</td>
        <td>₱${item.price.toFixed(2)}</td>
        <td><input type="number" min="1" value="${item.quantity}" onchange="changeQuantity(${item.product_id}, this.value)" style="width:60px;"></td>
        <td>₱${itemTotal.toFixed(2)}</td>
        <td><button type="button" onclick="removeFromCart(${item.product_id})">Remove</button></td>
      `;
      tbody.appendChild(row);
    });
    document.getElementById('cartTotal').textContent = total.toFixed(2);
  }

  function submitCart() {
    if (cart.length === 0) {
      alert('Order list is empty!');
      return false;
    }
    document.getElementById('cartInput').value = JSON.stringify(cart);
    return true;
  }
</script>

</body>
</html>
