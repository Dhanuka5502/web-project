<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.html");
    exit;
}

$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['id'];

// Get user information
$user_sql = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($user_sql);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Get user's order history
$orders_sql = "SELECT o.*, o.order_date AS created_at, COUNT(oi.id) as item_count 
               FROM orders o 
               LEFT JOIN order_items oi ON o.id = oi.order_id 
               WHERE o.user_id = ? 
               GROUP BY o.id 
               ORDER BY o.order_date DESC";
$stmt_orders = $conn->prepare($orders_sql);
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$orders = $stmt_orders->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - T-STORE</title>
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
            max-width: 1200px;
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

        .nav-links {
            margin-top: 20px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            padding: 8px 16px;
            border-radius: 20px;
            transition: background 0.3s;
        }

        .nav-links a:hover {
            background: rgba(255,255,255,0.2);
        }

        .content {
            padding: 40px;
        }

        .profile-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;
            margin-bottom: 40px;
        }

        .profile-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3em;
            color: white;
            font-weight: bold;
        }

        .profile-info h3 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .profile-info p {
            margin: 5px 0;
            color: #666;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .orders-section h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8em;
        }

        .order-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-id {
            font-weight: bold;
            color: #667eea;
            font-size: 1.1em;
        }

        .order-date {
            color: #666;
            font-size: 0.9em;
        }

        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8em;
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

        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }

        .order-detail {
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
        }

        .order-total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            color: #667eea;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .no-orders {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-orders h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #ffd700;
            color: #000000;
            text-decoration: none;
            border-radius: 4px;
            border: 1px solid #d4af37;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background: #e0a800;
        }

        @media (max-width: 768px) {
            .profile-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .order-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="header">
            <h1>My Profile</h1>
        </div>

        <div class="content">
            <!-- Profile Information -->
            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                    </div>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                        <p>Member since: <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                    </div>
                </div>

                <div class="stats-grid">
                    <?php
                    // Get order statistics
                    $total_orders = $orders->num_rows;
                    $total_spent = 0;
                    $orders->data_seek(0);
                    while ($order = $orders->fetch_assoc()) {
                        $total_spent += $order['total_price'];
                    }
                    $orders->data_seek(0);
                    ?>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_orders; ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">LKR <?php echo number_format($total_spent, 2); ?></div>
                        <div class="stat-label">Total Spent</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">LKR <?php echo $total_orders > 0 ? number_format($total_spent / $total_orders, 2) : '0.00'; ?></div>
                        <div class="stat-label">Average Order</div>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="orders-section">
                <h2>Order History</h2>
                <?php if ($orders->num_rows > 0): ?>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <div class="order-id">Order #<?php echo $order['id']; ?></div>
                                    <div class="order-date"><?php echo date('F j, Y', strtotime($order['created_at'])); ?></div>
                                </div>
                                <div class="order-status status-<?php echo strtolower($order['status'] ?? 'pending'); ?>">
                                    <?php echo ucfirst($order['status'] ?? 'Pending'); ?>
                                </div>
                            </div>
                            
                            <div class="order-details">
                                <div class="order-detail">
                                    <div class="detail-label">Customer</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                </div>
                                <div class="order-detail">
                                    <div class="detail-label">Shipping Address</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($order['shipping_address']); ?></div>
                                </div>
                                <div class="order-detail">
                                    <div class="detail-label">Phone</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($order['contact_phone']); ?></div>
                                </div>
                                <div class="order-detail">
                                    <div class="detail-label">Items</div>
                                    <div class="detail-value"><?php echo $order['item_count']; ?> items</div>
                                </div>
                            </div>
                            
                            <div class="order-total">
                                Total: LKR <?php echo number_format($order['total_price'], 2); ?>
                            </div>
                            
                            <div style="text-align: right; margin-top: 15px;">
                                <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn">View Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-orders">
                        <h3>No Orders Yet</h3>
                        <p>You haven't placed any orders yet. Start shopping to see your order history here!</p>
                        <a href="products.php" class="btn">Browse Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$stmt_user->close();
$stmt_orders->close();
$conn->close();
?> 