<?php
session_start();
define("SECURE_ACCESS", true);
include "../includes/db_connect.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $admin_user = "admin";
    $admin_pass = "1234";

    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION["logged_in"] = true;
        header("Location: products.php");
        exit();
    } else {
        $error = "Invalid login";
    }
}
?>
<form method="POST">
    <h2>Admin Login</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    Username: <input type="text" name="username"><br><br>
    Password: <input type="password" name="password"><br><br>

    <button type="submit">Login</button>
</form>