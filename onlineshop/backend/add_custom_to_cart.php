<?php
session_start();
header('Content-Type: application/json');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

// Initialize guest cart in session if it doesn't exist
if (!isset($_SESSION['guest_cart'])) {
    $_SESSION['guest_cart'] = [];
}

try {
    // Create a unique ID for the new item
    $newItemId = uniqid('item_', true);

    // Prepare the new item's data
    $newItem = [
        'id' => $newItemId,
        'name' => $input['name'],
        'price' => $input['price'],
        'quantity' => $input['quantity'],
        'size' => $input['size'],
        'color' => $input['color'],
        'text' => $input['text'],
        'design' => $input['design'],
        'textColor' => $input['textColor'],
        'fontSize' => $input['fontSize'],
        'is_custom' => true
    ];

    // Add the new item to the guest cart
    $_SESSION['guest_cart'][] = $newItem;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Custom T-shirt added to your cart!'
    ]);
    
} catch (Exception $e) {
    error_log("Error adding custom item to guest cart: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again.'
    ]);
}
?> 