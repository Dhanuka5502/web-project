<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Include db connection
    require_once 'db.php';

    $username = $_POST['username']; // This can be username or email from the form
    $password = $_POST['password'];

    // Check if the input is an email or a username
    $field_type = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    $sql = "SELECT id, username, password, role FROM users WHERE $field_type = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $db_username, $hashed_password, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            
            if ($role === 'admin') {
                // Admin user
                $_SESSION["admin_loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $db_username;
                header("location: ../admin/dashboard.php");
                exit;
            } else {
                // Regular user
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["username"] = $db_username;
                header("location: ../frontend/index.php");
                exit;
            }

        } else {
            // Incorrect password redirect
            header("location: ../frontend/login.html?error=incorrect_password&username=" . urlencode($username));
            exit;
        }
    } else {
        // User not found redirect
        header("location: ../frontend/login.html?error=user_not_found");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?> 