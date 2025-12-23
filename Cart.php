
<?php
define("SECURE_ACCESS", true);

session_start();

include 'includes/db_connect.php';

$pageTitle = "Shopping Cart - Online Phones Store";

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $removeId = (int)$_POST['remove_id'];

    if ($removeId > 0) {
        if (isset($_SESSION['cart'][$removeId])) {
            unset($_SESSION['cart'][$removeId]);
        } else {
            foreach ($_SESSION['cart'] as $k => $item) {
                if (is_array($item) && isset($item['id']) && (int)$item['id'] === $removeId) {
                    unset($_SESSION['cart'][$k]);
                }
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    header('Location: Cart.php');
    exit;
}

$rawCart = $_SESSION['cart'];

$items = [];
$idsNeedingHydration = [];

foreach ($rawCart as $key => $value) {
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

    if (!isset($item['name']) || !isset($item['price'])) {
        $idsNeedingHydration[$item['id']] = true;
    }

    $items[] = $item;
}

if (!empty($idsNeedingHydration)) {
    $ids = array_keys($idsNeedingHydration);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT p.id, p.name, p.price, p.stock_quantity, b.name as brand_name,
                   pi.image_url, pi.alt_text
            FROM products p 
            LEFT JOIN brands b ON p.brand_id = b.id 
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.id IN ($placeholders) AND p.status = 'active'";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $result = $stmt->get_result();

        $productMap = [];
        while ($row = $result->fetch_assoc()) {
            $productMap[(int)$row['id']] = $row;
        }

        foreach ($items as &$it) {
            $pid = (int)$it['id'];
            if (isset($productMap[$pid])) {
                $p = $productMap[$pid];
                if (!isset($it['name'])) {
                    $it['name'] = $p['name'];
                }
                if (!isset($it['price'])) {
                    $it['price'] = (float)$p['price'];
                }
                if (!isset($it['brand_name']) && isset($p['brand_name'])) {
                    $it['brand_name'] = $p['brand_name'];
                }
                if (!isset($it['stock_quantity'])) {
                    $it['stock_quantity'] = (int)$p['stock_quantity'];
                }
                if (!isset($it['image_url']) && isset($p['image_url'])) {
                    $it['image_url'] = $p['image_url'];
                }
                if (!isset($it['alt_text']) && isset($p['alt_text'])) {
                    $it['alt_text'] = $p['alt_text'];
                }
            }
        }
        unset($it);

        $stmt->close();
    }
}

ob_start();
?>

<div class="row align-items-center mb-4">
    <div class="col-12 col-md-6">
        <h1 class="h3 mb-0">Shopping Cart</h1>
    </div>
    <div class="col-12 col-md-6 mt-3 mt-md-0 text-md-end d-flex flex-column flex-sm-row gap-2 justify-content-md-end">
        <a href="index.php" class="btn btn-outline-primary">Continue Shopping</a>
        <?php if (!empty($items)): ?>
            <a href="checkout.php" class="btn btn-success">Checkout</a>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="alert alert-info" role="alert">
        Your cart is empty.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="text-end">Price</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grandTotal = 0.0;
                foreach ($items as $item):
                    $id = (int)$item['id'];
                    $name = isset($item['name']) ? (string)$item['name'] : ('Product #' . $id);
                    $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                    $qty = (int)$item['quantity'];
                    $subtotal = $price * $qty;
                    $grandTotal += $subtotal;
                ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/60x60?text=' . urlencode($name) ?>" 
                                     alt="<?= !empty($item['alt_text']) ? htmlspecialchars($item['alt_text']) : htmlspecialchars($name) ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover;" 
                                     class="rounded border">
                                <div>
                                    <?php if (!empty($item['brand_name'])): ?>
                                        <div class="text-muted small"><?= htmlspecialchars($item['brand_name']) ?></div>
                                    <?php endif; ?>
                                    <div class="fw-semibold"><?= htmlspecialchars($name) ?></div>
                                    <?php if (isset($item['stock_quantity']) && $item['stock_quantity'] <= 0): ?>
                                        <div class="text-danger small">Out of Stock</div>
                                    <?php elseif (isset($item['stock_quantity']) && $item['stock_quantity'] < 10): ?>
                                        <div class="text-warning small">Only <?= $item['stock_quantity'] ?> left</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-end">EGP <?= number_format($price, 2) ?></td>
                        <td class="text-center"><?= $qty ?></td>
                        <td class="text-end">EGP <?= number_format($subtotal, 2) ?></td>
                        <td class="text-end">
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="remove_id" value="<?= $id ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end">EGP <?= number_format($grandTotal, 2) ?></th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>

<?php
$conn->close();

$pageContent = ob_get_clean();
include 'includes/template.php';
?>

