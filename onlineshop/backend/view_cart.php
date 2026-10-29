<?php
session_start();

// --- Final, Corrected Item Removal Logic ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $item_id_to_remove = $_POST['item_id'];
    
    // Handle removal from the guest cart in the session
    if (isset($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
        $new_guest_cart = [];
        foreach ($_SESSION['guest_cart'] as $item) {
            // If the current item's ID does not match the one to remove, add it to the new cart
            if ($item['id'] != $item_id_to_remove) {
                $new_guest_cart[] = $item;
            }
        }
        // Replace the old cart with the newly filtered one
        $_SESSION['guest_cart'] = $new_guest_cart;
    }

    // Handle removal from the database cart for logged-in users
    if (isset($_SESSION['id']) && is_numeric($item_id_to_remove)) {
        $conn = new mysqli("localhost", "root", "", "onlineshop");
        if (!$conn->connect_error) {
            $user_id = $_SESSION['id'];
            $item_id_to_remove_int = intval($item_id_to_remove);
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)");
            $stmt->bind_param("ii", $item_id_to_remove_int, $user_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
        }
    }

    // Refresh the page to show the updated cart
    header("Location: view_cart.php");
    exit;
}
// --- End of Removal Logic ---

$db_items = [];
$session_items = [];
$total = 0;

// Fetch items for logged-in users from the database
if (isset($_SESSION['id'])) {
    $conn = new mysqli("localhost", "root", "", "onlineshop");
    if ($conn->connect_error) {
        die("DB connection failed: " . $conn->connect_error);
    }
    $user_id = $_SESSION['id'];
    
    // Handle item updates or deletions
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Update quantity
        if (isset($_POST['update_quantity'])) {
            $item_id = intval($_POST['item_id']);
            $new_qty = max(1, intval($_POST['quantity']));
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)");
            $stmt->bind_param("iii", $new_qty, $item_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        // Remove item
        if (isset($_POST['remove_item'])) {
            $item_id = intval($_POST['item_id']);
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ? AND cart_id IN (SELECT id FROM cart WHERE user_id = ?)");
            $stmt->bind_param("ii", $item_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
        header("Location: view_cart.php"); // Refresh page
        exit;
    }

    // Fetch cart items for the user (both regular products and custom items)
    $sql = "SELECT 
                ci.id as item_id, 
                ci.product_name,
                ci.price, 
                ci.quantity,
                ci.size,
                ci.color,
                ci.custom_text,
                ci.custom_design,
                ci.text_color,
                ci.font_size,
                ci.is_custom,
                p.image,
                p.name as product_name_original
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.id
            LEFT JOIN products p ON ci.product_id = p.id
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $db_items[] = $row;
    }
    $stmt->close();
    $conn->close();
}

// Fetch items from the guest cart in the session
if (isset($_SESSION['guest_cart']) && is_array($_SESSION['guest_cart'])) {
    foreach ($_SESSION['guest_cart'] as $item) {
        // Match the structure of database items for consistent display
        $session_items[] = [
            'item_id' => $item['id'],
            'product_name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'size' => $item['size'],
            'color' => $item['color'],
            'custom_text' => $item['text'],
            'custom_design' => $item['design'],
            'text_color' => $item['textColor'],
            'font_size' => $item['fontSize'],
            'is_custom' => true,
            'image' => null, 
            'product_name_original' => $item['name']
        ];
    }
}

$all_items = array_merge($db_items, $session_items);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart - T-STORE</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f6fc; padding-top: 100px; color: #333333; }
        .container { width: 85%; margin: 40px auto; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #dddddd; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .cart-header h2 { margin: 0; color: #673ab7; }
        .back-link { color: #673ab7; text-decoration: none; font-weight: bold; }
        .back-link:hover { text-decoration: underline; }
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th, .cart-table td { padding: 15px; border-bottom: 1px solid #dddddd; text-align: left; }
        .cart-table th { background-color: #f3effa; color: #673ab7; font-weight: bold; }
        .product-info { display: flex; align-items: center; }
        .product-info img { border-radius: 4px; margin-right: 20px; border: 1px solid #dddddd; }
        .custom-tshirt-preview { 
            width: 80px; 
            height: 100px; 
            background: #ffffff; 
            border-radius: 4px; 
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            border: 1px solid #cccccc;
        }
        .custom-tshirt-base {
            width: 60px;
            height: 80px;
            border-radius: 4px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .custom-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            max-width: 50px;
            word-wrap: break-word;
            font-weight: bold;
        }
        .custom-design {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 16px;
        }
        .custom-details {
            font-size: 12px;
            color: #666666;
            margin-top: 5px;
        }
        .quantity-form input { width: 60px; text-align: center; padding: 5px; border: 1px solid #cccccc; border-radius: 4px; }
        .btn { 
            display: inline-block; 
            padding: 8px 15px; 
            border: 1px solid #d4af37; 
            border-radius: 4px; 
            text-decoration: none; 
            color: #000000; 
            background-color: #ffd700;
            font-weight: bold;
            cursor: pointer; 
            text-align: center;
        }
        .btn:hover { background-color: #e0a800; }
        .update-btn { margin-left: 5px; }
        .remove-btn { background-color: #f1f1f1; color: #333333; border: 1px solid #cccccc; }
        .remove-btn:hover { background-color: #e0e0e0; }
        .cart-summary { text-align: right; margin-top: 30px; }
        .total-price { font-size: 24px; font-weight: bold; color: #673ab7; }
        .checkout-btn { font-size: 18px; padding: 12px 25px; margin-top: 10px; }
        .empty-cart { text-align: center; font-size: 18px; color: #666666; padding: 50px; }
    </style>
</head>
<body>
    <?php include '../frontend/nav.php'; ?>
    <div class="container">
        <div class="cart-header">
            <h2>Your Shopping Cart</h2>
            <a href="../frontend/products.php" class="back-link">Continue Shopping →</a>
        </div>
        <?php if (!empty($all_items)): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_items as $row):
                        $price = $row['price'] ?? 0;
                        $quantity = $row['quantity'] ?? 1;
                        $subtotal = $price * $quantity;
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <?php if ($row['is_custom']): ?>
                                        <div class="custom-tshirt-preview">
                                            <div class="custom-tshirt-base" style="background-color: <?php echo htmlspecialchars($row['color']); ?>;">
                                                <?php if ($row['custom_text']): ?>
                                                    <div class="custom-text" style="color: <?php echo htmlspecialchars($row['text_color']); ?>; font-size: <?php echo htmlspecialchars($row['font_size']); ?>px;">
                                                        <?php echo htmlspecialchars($row['custom_text']); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if ($row['custom_design']): ?>
                                                    <div class="custom-design">
                                                        <?php echo htmlspecialchars($row['custom_design']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <strong><?php echo htmlspecialchars($row['product_name']); ?></strong>
                                            <div class="custom-details">
                                                Size: <?php echo htmlspecialchars($row['size']); ?> | 
                                                Color: <span style="display: inline-block; width: 15px; height: 15px; background-color: <?php echo htmlspecialchars($row['color']); ?>; border-radius: 50%; border: 1px solid #ddd;"></span>
                                                <?php if ($row['custom_text']): ?>
                                                    <br>Text: "<?php echo htmlspecialchars($row['custom_text']); ?>"
                                                <?php endif; ?>
                                                <?php if ($row['custom_design']): ?>
                                                    <br>Design: <?php echo htmlspecialchars($row['custom_design']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <img src="../frontend/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['product_name_original']); ?>" width="80">
                                        <span><?php echo htmlspecialchars($row['product_name_original']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>LKR <?php echo number_format($price, 2); ?></td>
                            <td>
                                <form action="view_cart.php" method="POST" class="quantity-form">
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1">
                                    <button type="submit" name="update_quantity" class="btn update-btn" <?php if ($row['is_custom']) echo 'disabled'; ?>>Update</button>
                                </form>
                            </td>
                            <td>LKR <?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form action="view_cart.php" method="POST">
                                    <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                    <button type="submit" name="remove_item" class="btn remove-btn">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="cart-summary">
                <p class="total-price">Grand Total: LKR <?php echo number_format($total, 2); ?></p>
                <a href="checkout.php" class="btn checkout-btn">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is empty.</p>
                <a href="../frontend/products.php" class="btn checkout-btn">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
