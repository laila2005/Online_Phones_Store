<?php
define("SECURE_ACCESS", true);
include "../includes/auth.php";
include "../includes/db_connect.php";

$id = $_GET["id"];

mysqli_query($conn, "DELETE FROM products WHERE id=$id");

header("Location: products.php");
exit();
?>
