<?php
session_start();
// Redirect to login page if not logged in as admin
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database connection
require_once '../backend/db.php';

// Fetch users from the database
$sql = "SELECT id, username, email FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
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

        .manage-users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .manage-users-table th, .manage-users-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .manage-users-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }

        .manage-users-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .manage-users-table tr:hover {
            background-color: #e9ecef;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
            font-size: 14px;
        }

        .edit-btn {
            background-color: #007bff;
        }
        
        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
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
                    <li><a href="manage_users.php" class="active">Manage Users</a></li>
                    <li><a href="manage_product.php">Manage Products</a></li>
                    <li><a href="manage_orders.php">Manage Orders</a></li>
                    <li><a href="../backend/logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <a href="dashboard.php" class="btn-back">← Back to Dashboard</a>
            <h2>Manage Users</h2>
            <table class="manage-users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No users found.</td>
                        </tr>
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
if(isset($result)) { $result->free(); }
$conn->close();
?>
