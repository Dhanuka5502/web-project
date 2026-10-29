<?php
session_start();
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once '../backend/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("location: manage_users.php");
    exit;
}

$id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    if ($username && $email) {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $id);
        $stmt->execute();
        $stmt->close();
        $conn->close();
        header("location: manage_users.php");
        exit;
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($username, $email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        body { background: #f8f9fa; }
        .edit-container { max-width: 400px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px; }
        .edit-container h2 { text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
        .form-input { width: 100%; padding: 12px 15px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box; }
        .form-input:focus { outline: none; border-color: #007bff; }
        .form-btn { width: 100%; padding: 12px; border: none; border-radius: 5px; background-color: #007bff; color: white; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease; }
        .form-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
<div class="edit-container">
    <h2>Edit User</h2>
    <form method="POST">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-input" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-input" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <button type="submit" class="form-btn">Update User</button>
    </form>
</div>
</body>
</html> 