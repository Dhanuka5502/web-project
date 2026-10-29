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

// Get user profile information
$sql = "SELECT id, username, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

// Get order statistics
$stats_sql = "SELECT 
    COUNT(*) as total_orders,
    SUM(total_price) as total_spent,
    MAX(created_at) as last_order_date
    FROM orders 
    WHERE user_id = ?";
$stats_stmt = $conn->prepare($stats_sql);
$stats_stmt->bind_param("i", $user_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();

$stmt->close();
$stats_stmt->close();
$conn->close();

echo json_encode([
    'user' => $user,
    'stats' => $stats
]);
?>
