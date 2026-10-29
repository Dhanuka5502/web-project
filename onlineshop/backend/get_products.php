<?php
$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) die("DB connection failed");

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $sql = "SELECT id, name, description, price, image FROM products WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, name, description, price, image FROM products";
    $result = $conn->query($sql);
}

$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);

