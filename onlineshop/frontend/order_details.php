<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}

if (!isset($_GET['order_id'])) {
    header("Location: profile.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];
$order_id = $_GET['order_id'];

// Get order information
$order_sql = "SELECT *, order_date AS created_at FROM orders WHERE id = ? AND user_id = ?";
$stmt_order = $conn->prepare($order_sql);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();

if (!$order) {
    header("Location: profile.php");
    exit;
}

// Get order items
$items_sql = "SELECT oi.*, p.name as product_name, p.price as product_price, p.image 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$stmt_items = $conn->prepare($items_sql);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - T-STORE</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8f6fc;
            margin: 0;
            padding: 20px;
            padding-top: 100px;
            min-height: 100vh;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #ddd;
        }

        .header {
            background: #673ab7;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 300;
        }

        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-top: 15px;
            padding: 8px 16px;
            border-radius: 20px;
            transition: background 0.3s;
        }

        .back-link:hover {
            background: rgba(255,255,255,0.2);
        }

        .content {
            padding: 40px;
        }

        .order-info {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-id {
            font-size: 1.5em;
            font-weight: bold;
            color: #673ab7;
        }

        .order-status {
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background: #cce5ff;
            color: #004085;
        }

        .status-shipped {
            background: #d4edda;
            color: #155724;
        }

        .status-delivered {
            background: #d1ecf1;
            color: #0c5460;
        }

        .order-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .detail-group {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.8em;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
            font-size: 1.1em;
        }

        .items-section h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.8em;
        }

        .item-card {
            display: flex;
            align-items: center;
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }

        .item-card:hover {
            transform: translateY(-2px);
        }

        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 20px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2em;
            color: #ccc;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            font-size: 1.1em;
        }

        .item-price {
            color: #667eea;
            font-weight: 600;
        }

        .item-quantity {
            color: #666;
            font-size: 0.9em;
        }

        .item-total {
            font-weight: bold;
            color: #333;
            font-size: 1.2em;
        }

        .order-summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-top: 30px;
            text-align: right;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 1.1em;
        }

        .summary-total {
            font-size: 1.5em;
            font-weight: bold;
            color: #673ab7;
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            margin-top: 15px;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .order-details-grid {
                grid-template-columns: 1fr;
            }

            .item-card {
                flex-direction: column;
                text-align: center;
            }

            .item-image {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="header">
            <h1>Order Details</h1>
            <a href="profile.php" class="back-link">← Back to Profile</a>
        </div>

        <div class="content">
            <!-- Order Information -->
            <div class="order-info">
                <div class="order-header">
                    <div class="order-id">Order #<?php echo $order['id']; ?></div>
                    <div class="order-status status-<?php echo strtolower($order['status'] ?? 'pending'); ?>">
                        <?php echo ucfirst($order['status'] ?? 'Pending'); ?>
                    </div>
                </div>
                
                <div class="order-details-grid">
                    <div class="detail-group">
                        <div class="detail-label">Order Date</div>
                        <div class="detail-value"><?php echo date('F j, Y \a\t g:i A', strtotime($order['created_at'])); ?></div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">Customer Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">Shipping Address</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                    </div>
                    <div class="detail-group">
                        <div class="detail-label">Phone Number</div>
                        <div class="detail-value"><?php echo htmlspecialchars($order['contact_phone']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="items-section">
                <h2>Order Items</h2>
                <?php if ($items->num_rows > 0): ?>
                    <?php while ($item = $items->fetch_assoc()): ?>
                        <div class="item-card">
                            <div class="item-image">
                                <?php if ($item['image']): ?>
                                    <img src="img/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    📦
                                <?php endif; ?>
                            </div>
                            <div class="item-details">
                                <div class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                <div class="item-price">LKR <?php echo number_format($item['product_price'], 2); ?> each</div>
                                <div class="item-quantity">Quantity: <?php echo $item['quantity']; ?></div>
                            </div>
                            <div class="item-total">
                                LKR <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No items found for this order.</p>
                <?php endif; ?>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>LKR <?php echo number_format($order['total_price'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total:</span>
                    <span>LKR <?php echo number_format($order['total_price'], 2); ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$stmt_order->close();
$stmt_items->close();
$conn->close();
?> 