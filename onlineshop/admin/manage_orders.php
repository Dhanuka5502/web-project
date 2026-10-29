<?php
session_start();
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../backend/db.php';

// Fetch orders with user information
$sql = "SELECT o.id, o.order_date, o.status, u.username 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$orders_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - T-Store</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        header {
            background-color: #2c3e50;
            color: #ecf0f1;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            font-size: 24px;
            margin: 0;
        }
        header nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        header nav ul li {
            display: inline-block;
            margin-left: 25px;
        }
        header nav ul li a {
            color: #ecf0f1;
            text-decoration: none;
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        header nav ul li a:hover, header nav ul li a.active {
            background-color: #34495e;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
        main .container {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .orders-table th, .orders-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .orders-table th {
            background-color: #f8f9fa;
        }
        .action-btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
        }
        .view-btn { background-color: #17a2b8; }
        .delete-btn { background-color: #dc3545; }
        .status-form { display: inline-block; }
        .status-select { padding: 5px; }
        .update-btn {
            background-color: #28a745;
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="manage_product.php">Manage Products</a></li>
                    <li><a href="manage_orders.php" class="active">Manage Orders</a></li>
                    <li><a href="../backend/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
            <h2>Manage Orders</h2>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Username</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                        <?php while($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['username']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>
                                    <form action="update_order_status.php" method="POST" class="status-form">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" class="status-select">
                                            <option value="Pending" <?php if($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                            <option value="Processing" <?php if($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                            <option value="Shipped" <?php if($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                            <option value="Delivered" <?php if($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="Cancelled" <?php if($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" class="update-btn">Update</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="action-btn view-btn">View Details</a>
                                    <a href="delete_order.php?id=<?php echo $order['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this order? This action cannot be undone.');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 T-Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
<?php
$conn->close();
?>
