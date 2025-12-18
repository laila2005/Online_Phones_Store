<?php
define("SECURE_ACCESS", true);
session_start();

include 'includes/db_connect.php';

echo "<h1>Cart Flow Test</h1>";
echo "<hr>";

// Test 1: Check if products exist
echo "<h2>Test 1: Products in Database</h2>";
$result = $conn->query("SELECT id, name, price FROM products LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<p style='color:green;'>✓ Found products:</p>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>ID: {$row['id']} - {$row['name']} - \${$row['price']}</li>";
    }
    echo "</ul>";
    $firstProductId = null;
    $result->data_seek(0);
    if ($row = $result->fetch_assoc()) {
        $firstProductId = $row['id'];
    }
} else {
    echo "<p style='color:red;'>✗ No products found in database</p>";
    $firstProductId = null;
}

// Test 2: Check current cart session
echo "<h2>Test 2: Current Cart Session</h2>";
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    echo "<p style='color:green;'>✓ Cart has items:</p>";
    echo "<pre>";
    print_r($_SESSION['cart']);
    echo "</pre>";
} else {
    echo "<p style='color:orange;'>Cart is empty</p>";
}

// Test 3: Simulate adding to cart
if ($firstProductId) {
    echo "<h2>Test 3: Simulate Add to Cart</h2>";
    echo "<p><a href='add_to_cart.php?product_id={$firstProductId}&quantity=1' target='_blank'>Click here to add product #{$firstProductId} to cart</a></p>";
    echo "<p>After clicking, refresh this page to see updated cart.</p>";
}

// Test 4: View cart page
echo "<h2>Test 4: View Cart Page</h2>";
echo "<p><a href='Cart.php' target='_blank'>Open Cart.php</a></p>";

// Test 5: Clear cart
if (isset($_GET['clear_cart'])) {
    unset($_SESSION['cart']);
    echo "<p style='color:green;'>✓ Cart cleared! <a href='test_cart_flow.php'>Refresh</a></p>";
} else {
    echo "<p><a href='test_cart_flow.php?clear_cart=1'>Clear Cart</a></p>";
}

$conn->close();
?>
