<?php
// Include db connection
require_once 'db.php';

// Initialize variables
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Server-side validation
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    die("Please fill all required fields.");
}

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

// Check if username or email already exists
$sql = "SELECT id FROM users WHERE username = ? OR email = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ss", $username, $email);
    if ($stmt->execute()) {
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            die("Username or email already exists.");
        }
    } else {
        die("Error: " . $conn->error);
    }
    $stmt->close();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user into database
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    if ($stmt->execute()) {
        // Redirect to login page
        header("location: ../frontend/login.html");
    } else {
        echo "Something went wrong. Please try again later.";
    }

    $stmt->close();
}

$conn->close();
?>
