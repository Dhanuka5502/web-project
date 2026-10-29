<?php
session_start();
if (isset($_SESSION["admin_loggedin"]) && $_SESSION["admin_loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - T-Store</title>
    <link rel="stylesheet" href="../frontend/stylelogin.css">
</head>
<body>
    <div class="container">
        <form action="../backend/login_process.php" method="POST">
            <h2>Admin Login</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <input type="hidden" name="role" value="admin">
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
