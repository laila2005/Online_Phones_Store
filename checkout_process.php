<?php
define("SECURE_ACCESS", true);

session_start();

include 'includes/db_connect.php';

$pageTitle = "Checkout Processing - Online Phones Store";

$name = isset($_POST['customer_name']) ? trim((string)$_POST['customer_name']) : '';
$email = isset($_POST['customer_email']) ? trim((string)$_POST['customer_email']) : '';
$phone = isset($_POST['customer_phone']) ? trim((string)$_POST['customer_phone']) : '';
$address = isset($_POST['customer_address']) ? trim((string)$_POST['customer_address']) : '';
$paymentMethod = isset($_POST['payment_method']) ? trim((string)$_POST['payment_method']) : 'card';
$promoCode = isset($_POST['promo_code']) ? trim((string)$_POST['promo_code']) : '';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $errors[] = 'Invalid request method.';
}

if ($name === '' || mb_strlen($name) < 2) {
    $errors[] = 'Please enter a valid name.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($phone === '' || mb_strlen($phone) < 10) {
    $errors[] = 'Please enter a valid phone number.';
}

if ($address === '' || mb_strlen($address) < 5) {
    $errors[] = 'Please enter a valid address.';
}

if (!in_array($paymentMethod, ['card', 'cash_on_delivery'], true)) {
    $errors[] = 'Invalid payment method.';
}

$cart = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? $_SESSION['cart'] : [];
if (empty($cart)) {
    $errors[] = 'Your cart is empty.';
}

$items = [];
$idsNeedingHydration = [];

foreach ($cart as $key => $value) {
    $item = null;

    if (is_array($value)) {
        $item = $value;
        if (!isset($item['id']) && is_numeric($key)) {
            $item['id'] = (int)$key;
        }
    } else {
        if (is_numeric($key)) {
            $item = [
                'id' => (int)$key,
                'quantity' => (int)$value,
            ];
        }
    }

    if (!$item || !isset($item['id'])) {
        continue;
    }

    $item['id'] = (int)$item['id'];
    $item['quantity'] = isset($item['quantity']) ? (int)$item['quantity'] : (isset($item['qty']) ? (int)$item['qty'] : 1);
    if ($item['quantity'] <= 0) {
        $item['quantity'] = 1;
    }

    if (!isset($item['price']) || !isset($item['name'])) {
        $idsNeedingHydration[$item['id']] = true;
    }

    $items[] = $item;
}

if (empty($items)) {
    $errors[] = 'Your cart contains invalid items.';
}

if (!empty($idsNeedingHydration)) {
    $ids = array_keys($idsNeedingHydration);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT id, price FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        $errors[] = 'Failed to prepare product lookup.';
    } else {
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();

        $priceMap = [];
        while ($row = $result->fetch_assoc()) {
            $priceMap[(int)$row['id']] = (float)$row['price'];
        }

        foreach ($items as &$it) {
            $pid = (int)$it['id'];
            if (!isset($it['price'])) {
                if (isset($priceMap[$pid])) {
                    $it['price'] = $priceMap[$pid];
                } else {
                    $it['price'] = 0.0;
                }
            }
        }
        unset($it);

        $stmt->close();
    }
}

$subtotal = 0.0;
foreach ($items as $it) {
    $price = isset($it['price']) ? (float)$it['price'] : 0.0;
    $qty = (int)$it['quantity'];
    $subtotal += ($price * $qty);
}

if ($subtotal <= 0) {
    $errors[] = 'Unable to calculate order total.';
}

// Validate and apply coupon code
$discountAmount = 0.0;
$couponCode = '';
$couponDescription = '';

if (!empty($promoCode)) {
    $stmt = $conn->prepare("SELECT code, description, discount_type, discount_value, min_purchase_amount, max_discount_amount, usage_limit, usage_count, is_active, valid_from, valid_until 
                            FROM coupons 
                            WHERE code = ? AND is_active = 1");
    if ($stmt) {
        $stmt->bind_param("s", $promoCode);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($coupon = $result->fetch_assoc()) {
            $now = date('Y-m-d H:i:s');
            $validFrom = $coupon['valid_from'];
            $validUntil = $coupon['valid_until'];
            
            // Check if coupon is valid
            // Skip valid_from check for now due to database issue
            if (!empty($validUntil) && $now > $validUntil) {
                $errors[] = 'This coupon has expired.';
            } elseif ($coupon['usage_limit'] && $coupon['usage_count'] >= $coupon['usage_limit']) {
                $errors[] = 'This coupon has reached its usage limit.';
            } elseif ($subtotal < $coupon['min_purchase_amount']) {
                $errors[] = 'Minimum purchase amount of EGP ' . number_format($coupon['min_purchase_amount'], 2) . ' required for this coupon.';
            } else {
                // Apply discount
                $couponCode = $coupon['code'];
                $couponDescription = $coupon['description'];
                
                if ($coupon['discount_type'] === 'percentage') {
                    $discountAmount = ($subtotal * $coupon['discount_value']) / 100;
                    if ($coupon['max_discount_amount'] && $discountAmount > $coupon['max_discount_amount']) {
                        $discountAmount = $coupon['max_discount_amount'];
                    }
                } else { // fixed_amount
                    $discountAmount = $coupon['discount_value'];
                    if ($discountAmount > $subtotal) {
                        $discountAmount = $subtotal;
                    }
                }
                
                // Update coupon usage count
                $updateStmt = $conn->prepare("UPDATE coupons SET usage_count = usage_count + 1 WHERE code = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param("s", $couponCode);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
        } else {
            $errors[] = 'Invalid coupon code.';
        }
        $stmt->close();
    }
}

$totalAmount = $subtotal - $discountAmount;
if ($totalAmount < 0) {
    $totalAmount = 0;
}

if (!empty($errors)) {
    ob_start();
    ?>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Checkout Error</h4>
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="checkout.php" class="btn btn-primary">Back to Checkout</a>
                <a href="Cart.php" class="btn btn-outline-secondary">Back to Cart</a>
            </div>
        </div>
    </div>
    <?php
    $pageContent = ob_get_clean();
    include 'includes/template.php';
    exit;
}

try {
    $conn->begin_transaction();

    $orderNumber = 'ORD-' . date('Ymd-His') . '-' . random_int(1000, 9999);

    $userId = null;
    if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
    }

    // Store shipping address in notes field for now (or create address record)
    $notes = "Shipping Address: " . $address . "\nCustomer: " . $name . "\nEmail: " . $email . "\nPhone: " . $phone;
    if (!empty($couponCode)) {
        $notes .= "\nCoupon Applied: " . $couponCode . " - " . $couponDescription;
    }

    $sqlOrder = "INSERT INTO orders (user_id, order_number, subtotal, discount_amount, total_amount, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtOrder = $conn->prepare($sqlOrder);
    if (!$stmtOrder) {
        throw new Exception('Failed to prepare order insert: ' . $conn->error);
    }

    $stmtOrder->bind_param('isdddss', $userId, $orderNumber, $subtotal, $discountAmount, $totalAmount, $paymentMethod, $notes);
    if (!$stmtOrder->execute()) {
        throw new Exception('Failed to create the order.');
    }

    $orderId = (int)$conn->insert_id;
    $stmtOrder->close();

    $sqlItem = "INSERT INTO order_items (order_id, product_id, product_name, sku, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmtItem = $conn->prepare($sqlItem);
    if (!$stmtItem) {
        throw new Exception('Failed to prepare order items insert: ' . $conn->error);
    }

    foreach ($items as $it) {
        $productId = (int)$it['id'];
        $productName = isset($it['name']) ? (string)$it['name'] : 'Product #' . $productId;
        $sku = isset($it['sku']) ? (string)$it['sku'] : '';
        $quantity = (int)$it['quantity'];
        $price = (float)$it['price'];
        $subtotal = $price * $quantity;

        $stmtItem->bind_param('iissidd', $orderId, $productId, $productName, $sku, $quantity, $price, $subtotal);
        if (!$stmtItem->execute()) {
            throw new Exception('Failed to add an item to the order.');
        }
    }

    $stmtItem->close();

    $_SESSION['last_order'] = [
        'order_number' => $orderNumber,
        'order_id' => $orderId,
        'customer_name' => $name,
        'customer_email' => $email,
        'customer_phone' => $phone,
        'subtotal' => $subtotal,
        'discount_amount' => $discountAmount,
        'total_amount' => $totalAmount,
        'coupon_code' => $couponCode,
        'payment_method' => $paymentMethod,
        'items' => $items,
        'order_date' => date('Y-m-d H:i:s')
    ];

    unset($_SESSION['cart']);

    $conn->commit();

    header('Location: thank_you.php');
    exit;
} catch (Throwable $e) {
    if ($conn && $conn->errno === 0) {
        // no-op
    }

    if ($conn) {
        $conn->rollback();
    }

    ob_start();
    ?>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">Checkout Failed</h4>
                <p class="mb-0"><?= htmlspecialchars($e->getMessage()) ?></p>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="checkout.php" class="btn btn-primary">Try Again</a>
                <a href="Cart.php" class="btn btn-outline-secondary">Back to Cart</a>
            </div>
        </div>
    </div>
    <?php
    $pageContent = ob_get_clean();
    include 'includes/template.php';
    exit;
}
