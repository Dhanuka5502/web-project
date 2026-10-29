<?php
session_start();

// For demo/testing: sample cart if empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['cart'] = [
        1 => ['name' => 'Premium Cool', 'price' => 2000, 'quantity' => 2],
        3 => ['name' => 'Red Challenge T-shirt', 'price' => 2300, 'quantity' => 1]
    ];
}

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your Cart - T-STORE</title>
    <link rel="stylesheet" href="../frontend/style.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f6fc;
            margin: 0;
            padding: 20px;
            color: #333333;
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #673ab7;
            font-weight: bold;
        }
        table {
            width: 90%;
            max-width: 900px;
            margin: 0 auto 30px auto;
            border-collapse: collapse;
            background: #ffffff;
            border: 1px solid #dddddd;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }
        thead {
            background-color: #f3effa;
            color: #673ab7;
            font-weight: bold;
        }
        th, td {
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #dddddd;
        }
        tbody tr:hover {
            background-color: #fcfbfe;
        }
        .total-row td {
            font-weight: bold;
            font-size: 1.2rem;
            border-top: 2px solid #673ab7;
            color: #673ab7;
        }
        .btn-checkout {
            display: block;
            width: 200px;
            margin: 0 auto;
            padding: 12px 0;
            background-color: #ffd700;
            color: #000000;
            border: 1px solid #d4af37;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .btn-checkout:hover {
            background-color: #e0a800;
        }
        .empty-cart {
            text-align: center;
            font-size: 1.3rem;
            color: #666666;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <h2>Your Shopping Cart</h2>

    <?php if (empty($_SESSION['cart'])): ?>
        <p class="empty-cart">Your cart is empty.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th><th>Price (LKR)</th><th>Quantity</th><th>Subtotal (LKR)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>LKR <?= number_format($item['price'], 2) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>LKR <?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total</td>
                    <td>LKR <?= number_format($total, 2) ?></td>
                </tr>
            </tbody>
        </table>

        <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
    <?php endif; ?>
</body>
</html>
