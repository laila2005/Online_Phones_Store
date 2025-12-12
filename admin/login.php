<?php
session_start();
define("SECURE_ACCESS", true);
include "../includes/db_connect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Query database for admin user
    $stmt = $conn->prepare("SELECT id, username, password, role FROM admin_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (supports both hashed and plain text for backward compatibility)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            $_SESSION["logged_in"] = true;
            $_SESSION["admin_id"] = $user['id'];
            $_SESSION["admin_username"] = $user['username'];
            $_SESSION["admin_role"] = $user['role'];
            
            // Update last login time
            $update_stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();
            
            header("Location: products.php");
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
    
    $stmt->close();
}
?>
<form method="POST">
    <h2>Admin Login</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    Username: <input type="text" name="username"><br><br>
    Password: <input type="password" name="password"><br><br>

    <button type="submit">Login</button>
</form>