<?php
define("SECURE_ACCESS", true);
include "../includes/auth.php";
include "../includes/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST["name"];
    $price = $_POST["price"];
    $desc  = $_POST["description"];
    $category = $_POST["category"];
    
    // Handle image upload
    $image_url = "";
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_dir = "../uploads/products/";
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
            $image_url = "/uploads/products/" . $new_filename;
        }
    } elseif (!empty($_POST["image_url"])) {
        // Use URL if provided
        $image_url = $_POST["image_url"];
    }

    $stmt = $conn->prepare("INSERT INTO products (name, price, description, image_url, category) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsss", $name, $price, $desc, $image_url, $category);
    $stmt->execute();

    header("Location: products.php");
    exit();
}
?>

<h2>Add Product</h2>
<form method="POST" enctype="multipart/form-data">
    Name: <input type="text" name="name" required><br><br>
    Price: <input type="number" step="0.01" name="price" required><br><br>
    Description:<br>
    <textarea name="description" rows="4" cols="50"></textarea><br><br>
    
    Upload Image: <input type="file" name="product_image" accept="image/*"><br>
    <small>Upload a product image from your computer</small><br><br>
    
    OR Image URL: <input type="text" name="image_url" size="50"><br>
    <small>Enter the full URL of the product image (e.g., https://example.com/image.jpg)</small><br><br>
    
    Category: <input type="text" name="category"><br>
    <small>e.g., Apple, Samsung, Google, etc.</small><br><br>

    <button type="submit">Add</button>
    <a href="products.php"><button type="button">Cancel</button></a>
</form>