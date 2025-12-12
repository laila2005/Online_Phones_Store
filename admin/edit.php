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
    $image_url = $_POST["image_url"];
    $category = $_POST["category"];

    mysqli_query($conn,
        "UPDATE products SET name='$name', price='$price', description='$desc', image_url='$image_url', category='$category'
         WHERE id=$id"
    );

    header("Location: products.php");
    exit();
}
?>

<h2>Edit Product</h2>
<form method="POST">
    Name: <input type="text" name="name" value="<?= $product['name']; ?>" required><br><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $product['price']; ?>" required><br><br>
    Description:<br>
    <textarea name="description" rows="4" cols="50"><?= $product['description']; ?></textarea><br><br>
    
    Image URL: <input type="text" name="image_url" value="<?= $product['image_url']; ?>" size="50"><br>
    <small>Enter the full URL of the product image (e.g., https://example.com/image.jpg)</small><br><br>
    
    Category: <input type="text" name="category" value="<?= $product['category']; ?>"><br>
    <small>e.g., Apple, Samsung, Google, etc.</small><br><br>

    <button type="submit">Update</button>
    <a href="products.php"><button type="button">Cancel</button></a>
</form>
