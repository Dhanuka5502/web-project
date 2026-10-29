<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
$logged_in = false;
$user_data = null;

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $logged_in = true;
    $user_data = [
        'id' => $_SESSION['id'],
        'username' => $_SESSION['username']
    ];
}

echo json_encode([
    'logged_in' => $logged_in,
    'user' => $user_data
]);
?> 