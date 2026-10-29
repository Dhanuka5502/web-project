<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Ensure the user is logged in
if (!isset($_SESSION['id'])) {
    // Redirect to login page if not logged in
    header("Location: ../frontend/login.html");
    exit;
}

if (isset($_POST['product_id'], $_POST['quantity'])) {
    $user_id = $_SESSION['id'];
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity < 1) $quantity = 1;

    $conn = new mysqli("localhost", "root", "", "onlineshop");
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // First, verify that the product exists
    $stmt_verify = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt_verify->bind_param("i", $product_id);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();
    if ($result_verify->num_rows === 0) {
        die("Error: Product with ID " . htmlspecialchars($product_id) . " does not exist. The product may have been removed.");
    }
    $stmt_verify->close();

    // Find the user's cart or create a new one
    $cart_id = null;
    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cart_id = $row['id'];
    } else {
        // No cart found, create one
        $stmt_create = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $stmt_create->bind_param("i", $user_id);
        $stmt_create->execute();
        $cart_id = $conn->insert_id;
        $stmt_create->close();
    }
    $stmt->close();

    // Check if item is already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $cart_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        // Item exists, update quantity
        $new_quantity = $row['quantity'] + $quantity;
        $stmt_update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt_update->bind_param("ii", $new_quantity, $row['id']);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Item does not exist, insert new
        $stmt_insert = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iii", $cart_id, $product_id, $quantity);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt->close();
    $conn->close();

    // Redirect to the cart view page
    header("Location: view_cart.php");
    exit;
} else {
    echo "Invalid request.";
}
