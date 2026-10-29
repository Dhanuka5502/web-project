<?php
session_start();
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../backend/db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: manage_orders.php");
    exit;
}

$order_id = $_GET['id'];

// Fetch order details
$sql = "SELECT o.id, o.order_date, o.status, u.username, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();
if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit;
}
$order = $order_result->fetch_assoc();
$stmt->close();

// Fetch order items
$sql_items = "SELECT p.name, p.price, oi.quantity 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Order Details</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        .wrapper { width: 800px; margin: 0 auto; padding-top: 20px; }
        .order-details, .items-table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        .order-details td, .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; }
        .items-table th { background-color: #f2f2f2; }
        h2, h3 { color: #333; }
        .btn-back { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Order Details</h2>
        <h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
        <table class="order-details">
            <tr>
                <td><strong>Username:</strong></td>
                <td><?php echo htmlspecialchars($order['username']); ?></td>
            </tr>
            <tr>
                <td><strong>Email:</strong></td>
                <td><?php echo htmlspecialchars($order['email']); ?></td>
            </tr>
            <tr>
                <td><strong>Order Date:</strong></td>
                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
            </tr>
        </table>

        <h3>Items in this Order</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                if ($items_result->num_rows > 0):
                    while($item = $items_result->fetch_assoc()): 
                        $subtotal = $item['quantity'] * $item['price'];
                        $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>LKR <?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    <td>LKR <?php echo htmlspecialchars(number_format($subtotal, 2)); ?></td>
                </tr>
                <?php 
                    endwhile;
                endif; 
                ?>
                <tr>
                    <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                    <td><strong>LKR <?php echo htmlspecialchars(number_format($total, 2)); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <a href="manage_orders.php" class="btn-back">Back to Orders</a>
    </div>
</body>
</html>
<?php
$stmt_items->close();
$conn->close();
?> 