<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "onlineshop");
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$user_id = $_SESSION['id'];

// Get user's orders with details
$sql = "SELECT o.*, o.order_date AS created_at, 
        GROUP_CONCAT(CONCAT(p.name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
        FROM orders o 
        LEFT JOIN order_items oi ON o.id = oi.order_id 
        LEFT JOIN products p ON oi.product_id = p.id 
        WHERE o.user_id = ? 
        GROUP BY o.id 
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'id' => $row['id'],
        'customer_name' => $row['customer_name'],
        'shipping_address' => $row['shipping_address'],
        'contact_phone' => $row['contact_phone'],
        'total_price' => $row['total_price'],
        'status' => $row['status'],
        'items' => $row['items'],
        'created_at' => $row['created_at']
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['orders' => $orders]);
?>
