<?php
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
    <th>Name</th>
    <th>Price</th>
    <th>Description</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><?= $row['name']; ?></td>
    <td><?= $row['price']; ?></td>
    <td><?= $row['description']; ?></td>
    <td>
        <a href="edit.php?id=<?= $row['id']; ?>">Edit</a> |
        <a href="delete.php?id=<?= $row['id']; ?>">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>
