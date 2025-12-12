<?php
define("SECURE_ACCESS", true);
include "../includes/auth.php";
include "../includes/db_connect.php";

$id = $_GET["id"];
$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST["name"];
    $price = $_POST["price"];
    $desc  = $_POST["description"];

    mysqli_query($conn,
        "UPDATE products SET name='$name', price='$price', description='$desc'
         WHERE id=$id"
    );

    header("Location: products.php");
    exit();
}
?>

<h2>Edit Product</h2>
<form method="POST">
    Name: <input type="text" name="name" value="<?= $product['name']; ?>"><br><br>
    Price: <input type="number" name="price" value="<?= $product['price']; ?>"><br><br>
    Description:<br>
    <textarea name="description"><?= $product['description']; ?></textarea><br><br>

    <button type="submit">Update</button>
</form>
