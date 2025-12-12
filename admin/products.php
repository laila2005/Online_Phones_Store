<?php
define("SECURE_ACCESS", true);
include "../includes/auth.php";
include "../includes/db_connect.php";

$query = "SELECT * FROM products";
$result = mysqli_query($conn, $query);
?>

<h2>Products</h2>
<a href="add.php">Add Product</a> | <a href="logout.php">Logout</a>
<br><br>

<table border="1" cellpadding="8">
<tr>
    <th>ID</th>
    <th>Image</th>
    <th>Name</th>
    <th>Price</th>
    <th>Category</th>
    <th>Description</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td>
        <?php if(!empty($row['image_url'])): ?>
            <img src="<?= $row['image_url']; ?>" alt="<?= $row['name']; ?>" style="width: 80px; height: 80px; object-fit: cover;">
        <?php else: ?>
            <em>No image</em>
        <?php endif; ?>
    </td>
    <td><?= $row['name']; ?></td>
    <td>$<?= number_format($row['price'], 2); ?></td>
    <td><?= $row['category']; ?></td>
    <td><?= substr($row['description'], 0, 50); ?><?= strlen($row['description']) > 50 ? '...' : ''; ?></td>
    <td>
        <a href="edit.php?id=<?= $row['id']; ?>">Edit</a> |
        <a href="delete.php?id=<?= $row['id']; ?>">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>
