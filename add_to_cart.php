<?php
define("SECURE_ACCESS", true);

ob_start();

session_start();

include 'includes/db_connect.php';

header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_OFF);

$fail = function (int $status, string $message) {
    if (ob_get_length()) {
        ob_clean();
    }
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
};

register_shutdown_function(function () use ($fail) {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        $fail(500, 'Server error: ' . $err['message']);
    }
});

set_error_handler(function ($severity, $message, $file, $line) use ($fail) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    $fail(500, 'Server error: ' . $message);
});

set_exception_handler(function (Throwable $e) use ($fail) {
    $fail(500, 'Server error: ' . $e->getMessage());
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    $fail(405, 'Method not allowed');
}

$productId = isset($_REQUEST['product_id']) ? (int)$_REQUEST['product_id'] : 0;
$quantity = isset($_REQUEST['quantity']) ? (int)$_REQUEST['quantity'] : 1;

if ($productId <= 0) {
    $fail(400, 'Invalid product id');
}

if ($quantity <= 0) {
    $quantity = 1;
}

$stmt = $conn->prepare('SELECT p.id, p.name, p.price, p.stock_quantity, b.name as brand_name 
                         FROM products p 
                         LEFT JOIN brands b ON p.brand_id = b.id 
                         WHERE p.id = ? AND p.status = "active"');
if (!$stmt) {
    $fail(500, 'Server error (prepare failed): ' . $conn->error);
}

$stmt->bind_param('i', $productId);

if (!$stmt->execute()) {
    $stmt->close();
    $fail(500, 'Server error (execute failed): ' . $conn->error);
}

$stmt->bind_result($id, $name, $price, $stockQuantity, $brandName);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    $fail(404, 'Product not found');
}

if ($stockQuantity <= 0) {
    $stmt->close();
    $conn->close();
    $fail(400, 'Product is out of stock');
}

$product = [
    'id' => $id,
    'name' => $name,
    'price' => $price,
    'stock_quantity' => $stockQuantity,
    'brand_name' => $brandName,
];

$stmt->close();
$conn->close();

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$found = false;
foreach ($_SESSION['cart'] as $k => $item) {
    if (is_array($item) && isset($item['id']) && (int)$item['id'] === $productId) {
        $currentQty = isset($item['quantity']) ? (int)$item['quantity'] : (isset($item['qty']) ? (int)$item['qty'] : 1);
        $newQty = $currentQty + $quantity;
        $_SESSION['cart'][$k]['quantity'] = $newQty;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['cart'][] = [
        'id' => (int)$product['id'],
        'name' => (string)$product['name'],
        'price' => (float)$product['price'],
        'image_url' => (string)$product['image_url'],
        'quantity' => $quantity,
    ];
}

$cartCount = 0;
foreach ($_SESSION['cart'] as $it) {
    if (is_array($it)) {
        $cartCount += isset($it['quantity']) ? (int)$it['quantity'] : (isset($it['qty']) ? (int)$it['qty'] : 1);
    }
}

echo json_encode([
    'success' => true,
    'message' => 'Added to cart',
    'cart_count' => $cartCount,
]);
