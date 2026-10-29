<?php
session_start();
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: ../frontend/login.html");
    exit;
}

require_once '../backend/db.php';

// Fetch statistics
$total_users = $conn->query("SELECT COUNT(id) as count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_products = $conn->query("SELECT COUNT(id) as count FROM products")->fetch_assoc()['count'];
$total_orders = $conn->query("SELECT COUNT(id) as count FROM orders")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(id) as count FROM orders WHERE status = 'Pending'")->fetch_assoc()['count'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - T-Store</title>
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

        main .container {
            padding: 2rem 1rem;
        }
        .dashboard-header h2 {
            margin-bottom: 1rem;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #555;
        }
        .stat-card .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #333;
        }
        .actions-container {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .actions-container h3 {
            margin-top: 0;
            margin-bottom: 20px;
        }
        .action-btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            margin-right: 10px;
            margin-bottom: 10px;
            text-align: center;
        }
        .add-product-btn { background-color: #28a745; }
        .manage-users-btn { background-color: #007bff; }
        .manage-orders-btn { background-color: #17a2b8; }
    </style>
</head>
<body>
    <header>
        <div class="container">
        <h1>Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="manage_users.php">Manage Users</a></li>
                <li><a href="manage_product.php">Manage Products</a></li>
                <li><a href="manage_orders.php">Manage Orders</a></li>
                <li><a href="../backend/logout.php">Logout</a></li>
            </ul>
        </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="dashboard-header">
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <span class="stat-number"><?php echo $total_users; ?></span>
                </div>
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <span class="stat-number"><?php echo $total_products; ?></span>
                </div>
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <span class="stat-number"><?php echo $total_orders; ?></span>
                </div>
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <span class="stat-number"><?php echo $pending_orders; ?></span>
                </div>
            </div>

            <div class="actions-container">
                <h3>Quick Actions</h3>
                <a href="manage_product.php" class="action-btn add-product-btn">Add New Product</a>
                <a href="manage_users.php" class="action-btn manage-users-btn">Manage All Users</a>
                <a href="manage_orders.php" class="action-btn manage-orders-btn">Manage All Orders</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 T-Store. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
