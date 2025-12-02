<?php
define("SECURE_ACCESS", true);
include "../includes/auth.php";
include "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST["name"];
    $price = $_POST["price"];
    $desc  = $_POST["description"];

    mysqli_query($conn,
        "INSERT INTO products (name, price, description)
         VALUES ('$name', '$price', '$desc')"
    );

    header("Location: products.php");
    exit();
}
?>

<h2>Add Product</h2>
<form method="POST">
    Name: <input type="text" name="name"><br><br>
    Price: <input type="number" name="price"><br><br>
    Description:<br>
    <textarea name="description"></textarea><br><br>

    <button type="submit">Add</button>
</form>