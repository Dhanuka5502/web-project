<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../frontend/login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['cart_id'])) {
    echo "Invalid request.";
    exit();
}

$cart_id = intval($_POST['cart_id']);

$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("DELETE FROM cart_items WHERE id = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: view_cart.php");
exit();
?>
