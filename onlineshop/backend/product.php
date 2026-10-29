<?php
// frontend/products.php
session_start();
$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch all products
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Our Products - T-STORE</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>

<h2>Our Products</h2>
<div id="product-container" style="display:flex; flex-wrap: wrap; gap: 20px;">

<?php while ($product = $result->fetch_assoc()) : ?>
    <div style="border:1px solid #ccc; padding: 15px; width: 200px;">
        <h3><?= htmlspecialchars($product['name']) ?></h3>
        <img src="img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%; height:auto;">
        <p>Rs. <?= number_format($product['price'], 2) ?></p>

        <form action="../backend/add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="<?= $product['id'] ?>" />
            <input type="number" name="quantity" value="1" min="1" style="width:50px;" />
            <button type="submit" style="background-color: darkorange; color: white; border: none; padding: 5px 10px; cursor: pointer;">Add to Cart</button>
        </form>
    </div>
<?php endwhile; ?>

</div>

</body>
</html>

<?php $conn->close(); ?>
