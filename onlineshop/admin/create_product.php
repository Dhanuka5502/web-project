<?php
// Include database connection
require_once '../backend/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $image = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = basename($_FILES['image']['name']);
        $target_dir = '../frontend/img/products/';
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . uniqid() . '_' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Save the path relative to the frontend folder
            $image = 'img/products/' . basename($target_file);
        } else {
            $message = '<div class="alert alert-danger">Image upload failed.</div>';
        }
    }

    // Insert into database if no upload error
    if ($message === '') {
        $sql = "INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('ssds', $name, $description, $price, $image);
            if ($stmt->execute()) {
                $message = '<div class="alert alert-success">Product added successfully!</div>';
            } else {
                $message = '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
            }
            $stmt->close();
        } else {
            $message = '<div class="alert alert-danger">Database error: ' . $conn->error . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="../frontend/style.css">
    <style>
        body { background: #f8f9fa; }
        .product-container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 10px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px; }
        .product-container h2 { text-align: center; margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; margin-bottom: 6px; font-weight: 600; color: #333; }
        .form-input, .form-textarea { width: 100%; padding: 12px 15px; border-radius: 5px; border: 1px solid #ddd; box-sizing: border-box; }
        .form-textarea { resize: vertical; min-height: 80px; }
        .form-input:focus, .form-textarea:focus { outline: none; border-color: #007bff; }
        .form-btn { width: 100%; padding: 12px; border: none; border-radius: 5px; background-color: #007bff; color: white; font-size: 16px; font-weight: bold; cursor: pointer; transition: background-color 0.3s ease; }
        .form-btn:hover { background-color: #0056b3; }
        .alert { padding: 12px; border-radius: 5px; margin-bottom: 18px; font-weight: 600; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
<div class="product-container">
    <h2>Add New Product</h2>
    <?php if ($message) echo $message; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" class="form-input" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-textarea" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="price" class="form-label">Price ($)</label>
            <input type="number" step="0.01" class="form-input" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="image" class="form-label">Product Image</label>
            <input class="form-input" type="file" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="form-btn">Add Product</button>
    </form>
</div>
</body>
</html> 