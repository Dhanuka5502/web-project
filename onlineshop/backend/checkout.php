<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../frontend/login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];

// Fetch cart items for the user
$sql = "SELECT p.name, p.price, ci.quantity
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        JOIN cart c ON ci.cart_id = c.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$item_count = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - T-STORE</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f6fc; padding-top: 100px; color: #333333; }
        .container { width: 70%; margin: 40px auto; display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        .checkout-form, .order-summary { background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #dddddd; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .back-link { display: block; margin-bottom: 15px; color: #673ab7; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
        h2 { color: #673ab7; margin-bottom: 20px; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555555; }
        .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid #cccccc; border-radius: 4px; box-sizing: border-box; }
        .input-group input:focus, .input-group textarea:focus { outline: none; border-color: #673ab7; }
        .order-summary ul { list-style: none; padding: 0; }
        .order-summary li { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { font-size: 20px; font-weight: bold; margin-top: 20px; text-align: right; color: #673ab7; }
        .btn { width: 100%; padding: 15px; border: 1px solid #d4af37; border-radius: 4px; background-color: #ffd700; color: #000000; font-size: 18px; font-weight: bold; cursor: pointer; }
        .btn:hover { background-color: #e0a800; }
        .btn:disabled { background-color: #cccccc; border-color: #aaaaaa; color: #666666; cursor: not-allowed; }
        .empty-cart { text-align: center; padding: 50px; }
    </style>
</head>
<body>
    <?php include '../frontend/nav.php'; ?>
    <div class="container">
        <div class="checkout-form">
            <a href="view_cart.php" class="back-link">← Back to Cart</a>
            <h2>Shipping Information</h2>
            <form action="place_order.php" method="POST">
                <div class="input-group">
                    <label for="customer_name">Full Name</label>
                    <input type="text" id="customer_name" name="customer_name" required>
                </div>
                <div class="input-group">
                    <label for="shipping_address">Shipping Address</label>
                    <textarea id="shipping_address" name="shipping_address" rows="4" required></textarea>
                </div>
                <div class="input-group">
                    <label for="contact_phone">Phone Number</label>
                    <input type="tel" id="contact_phone" name="contact_phone" required>
                </div>
                <button type="submit" class="btn" <?php if($item_count == 0) echo 'disabled'; ?>>Place Order</button>
            </form>
        </div>
        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php if ($item_count > 0): ?>
                <ul>
                <?php while ($row = $result->fetch_assoc()):
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;
                ?>
                    <li><span><?php echo htmlspecialchars($row['name']); ?> (x<?php echo $row['quantity']; ?>)</span> <span>LKR <?php echo number_format($subtotal, 2); ?></span></li>
                <?php endwhile; ?>
                </ul>
                <hr>
                <p class="total">Grand Total: LKR <?php echo number_format($total, 2); ?></p>
            <?php else: ?>
                <div class="empty-cart">
                    <p>Your cart is empty. Add items to proceed.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>
