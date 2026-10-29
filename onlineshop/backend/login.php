<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "onlineshop";

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get submitted login data
$email = $_POST['email'];
$password = $_POST['password'];

// Look up user by email
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Check password
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        
        echo "✅ Login successful! Welcome, " . $user['fullname'] . ". <a href='profile.html'>Go to Profile</a>";
    } else {
        echo "❌ Invalid password.";
    }
} else {
    echo "❌ No account found with that email.";
}

$stmt->close();
$conn->close();
?>
