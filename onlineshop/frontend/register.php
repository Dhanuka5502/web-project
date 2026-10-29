<?php
// Turn on error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include db connection
    require_once '../backend/db.php';

    // Initialize variables
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Server-side validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_msg = "Please fill all required fields.";
    } elseif ($password !== $confirm_password) {
        $error_msg = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $error_msg = "Username or email already exists.";
                }
            } else {
                $error_msg = "Error checking user: " . $conn->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Error preparing check: " . $conn->error;
        }
    }

    if ($error_msg === "") {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                // Redirect to login page
                header("location: login.html");
                exit();
            } else {
                $error_msg = "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Error preparing query: " . $conn->error;
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - T-Store</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-color: #f8f6fc;
        font-family: Arial, sans-serif;
        margin: 0;
    }
    .container {
        background: #ffffff;
        padding: 40px;
        border-radius: 8px;
        border: 1px solid #dddddd;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 450px;
    }
    h2 {
        text-align: center;
        margin-bottom: 30px;
        color: #673ab7;
    }
    .input-group {
        margin-bottom: 20px;
        position: relative;
    }
    .input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #555555;
    }
    .input-group input {
        width: 100%;
        padding: 12px 15px;
        border-radius: 4px;
        border: 1px solid #cccccc;
        box-sizing: border-box;
        font-size: 14px;
    }
    .input-group input:focus {
        outline: none;
        border-color: #673ab7;
    }
    button[type="submit"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #d4af37;
        border-radius: 4px;
        background-color: #ffd700;
        color: #000000;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }
    button[type="submit"]:hover {
        background-color: #e0a800;
    }
    p {
        text-align: center;
        margin-top: 20px;
        color: #666666;
    }
    p a {
        color: #673ab7;
        text-decoration: none;
        font-weight: bold;
    }
    p a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <form id="registerForm" action="register.php" method="POST" onsubmit="return validateForm()">
      <h2>Register</h2>
      
      <?php if (!empty($error_msg)): ?>
        <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 12px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; text-align: center; font-weight: 600;">
          ❌ <?php echo htmlspecialchars($error_msg); ?>
        </div>
      <?php endif; ?>

      <div class="input-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
      </div>
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="6">
      </div>
      <div class="input-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <button type="submit">Register</button>
      <p>Already have an account? <a href="login.html">Login here</a></p>
    </form>
  </div>

  <script>
    function validateForm() {
      var password = document.getElementById("password").value;
      var confirm_password = document.getElementById("confirm_password").value;

      if (password !== confirm_password) {
        alert("Passwords do not match.");
        return false;
      }
      return true;
    }
  </script>
</body>
</html> 