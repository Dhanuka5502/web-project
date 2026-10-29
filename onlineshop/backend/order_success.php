<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: ../frontend/login.html");
    exit;
}
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful - T-STORE</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f6fc; text-align: center; color: #333333; }
        .container { max-width: 600px; margin: 100px auto; background: #ffffff; padding: 40px; border-radius: 8px; border: 1px solid #dddddd; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        h1 { color: #673ab7; font-size: 36px; }
        p { color: #555555; font-size: 18px; line-height: 1.6; }
        .order-number { font-weight: bold; color: #673ab7; }
        .btn { display: inline-block; margin-top: 30px; padding: 12px 25px; background-color: #ffd700; color: #000000; text-decoration: none; border: 1px solid #d4af37; border-radius: 4px; font-weight: bold; }
        .btn:hover { background-color: #e0a800; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Thank You!</h1>
        <p>Your order has been placed successfully.</p>
        <?php if ($order_id > 0): ?>
            <p>Your order number is: <span class="order-number">#<?php echo $order_id; ?></span></p>
        <?php endif; ?>
        <p>We have received your order and will begin processing it shortly.</p>
        <a href="../frontend/products.php" class="btn">Continue Shopping</a>
    </div>
</body>
</html> 