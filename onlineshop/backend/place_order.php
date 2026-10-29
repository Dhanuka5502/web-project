<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../frontend/login.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "onlineshop");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['id'];
    $customer_name = $_POST['customer_name'];
    $shipping_address = $_POST['shipping_address'];
    $contact_phone = $_POST['contact_phone'];

    // Get cart items
    $cart_sql = "SELECT ci.product_id, ci.quantity, p.price, p.name as product_name, c.id as cart_id
                 FROM cart_items ci
                 JOIN products p ON ci.product_id = p.id
                 JOIN cart c ON ci.cart_id = c.id
                 WHERE c.user_id = ?";
    $stmt_cart = $conn->prepare($cart_sql);
    $stmt_cart->bind_param("i", $user_id);
    $stmt_cart->execute();
    $cart_items = $stmt_cart->get_result();

    if ($cart_items->num_rows === 0) {
        die("Your cart is empty. Cannot place order.");
    }

    // Calculate total price
    $total_price = 0;
    while ($item = $cart_items->fetch_assoc()) {
        $total_price += $item['price'] * $item['quantity'];
    }
    $cart_items->data_seek(0); // Reset result set pointer

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into orders table
        $order_sql = "INSERT INTO orders (user_id, customer_name, shipping_address, contact_phone, total_price) VALUES (?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($order_sql);
        $stmt_order->bind_param("isssd", $user_id, $customer_name, $shipping_address, $contact_phone, $total_price);
        $stmt_order->execute();
        $order_id = $conn->insert_id;
        $stmt_order->close();

        // Insert into order_items table
        $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt_item = $conn->prepare($order_item_sql);
        
        while ($item = $cart_items->fetch_assoc()) {
            $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_item->execute();
        }
        $stmt_item->close();

        // Clear the cart
        $cart_items->data_seek(0); // Reset pointer
        $cart_id = $cart_items->fetch_assoc()['cart_id']; // Get cart_id
        
        $clear_cart_sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt_clear = $conn->prepare($clear_cart_sql);
        $stmt_clear->bind_param("i", $cart_id);
        $stmt_clear->execute();
        $stmt_clear->close();

        // Commit transaction
        $conn->commit();

        header("Location: order_success.php?order_id=" . $order_id);
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        die("Error placing order. Please try again. " . $exception->getMessage());
    }

    $stmt_cart->close();
    $conn->close();
}
?> 