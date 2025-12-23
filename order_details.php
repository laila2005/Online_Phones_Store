<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';
require_once 'includes/user_auth.php';

require_login('order_details.php');

$pageTitle = "Order Details - TechHub Electronics";

$user_id = get_current_user_id();
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch order details
$stmt = $conn->prepare("
    SELECT o.*, 
           sa.full_name as shipping_name, sa.address_line1, sa.address_line2, 
           sa.city, sa.state, sa.postal_code, sa.phone
    FROM orders o
    LEFT JOIN user_addresses sa ON o.shipping_address_id = sa.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Fetch order items
$stmt = $conn->prepare("
    SELECT oi.*, p.name as product_name, p.sku, pi.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

ob_start();
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Account Menu</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="profile.php" class="text-decoration-none" style="color: #764ba2;">My Profile</a></li>
                    <li class="mb-2"><a href="orders.php" class="text-decoration-none fw-bold" style="color: white; background-color: #764ba2; padding: 0.5rem 1rem; border-radius: 0.5rem; display: block;">My Orders</a></li>
                    <li class="mb-2"><a href="wishlist.php" class="text-decoration-none" style="color: #764ba2;"><i class="bi bi-heart me-1"></i>My Wishlist</a></li>
                    <li class="mb-2"><a href="Cart.php" class="text-decoration-none" style="color: #764ba2;">My Cart</a></li>
                    <li class="mb-2"><a href="logout.php" class="text-decoration-none text-danger">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="orders.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>

        <!-- Order Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3 class="mb-3">Order #<?= htmlspecialchars($order['order_number']) ?></h3>
                        <p class="text-muted mb-1">
                            <strong>Order Date:</strong> <?= date('F j, Y g:i A', strtotime($order['order_date'])) ?>
                        </p>
                        <p class="text-muted mb-1">
                            <strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?>
                        </p>
                        <p class="text-muted mb-0">
                            <strong>Payment Status:</strong> 
                            <span class="badge bg-<?= $order['payment_status'] == 'paid' ? 'success' : 'warning' ?>">
                                <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <?php
                        $status_class = [
                            'pending' => 'warning',
                            'processing' => 'info',
                            'shipped' => 'primary',
                            'delivered' => 'success',
                            'cancelled' => 'danger',
                            'returned' => 'secondary'
                        ];
                        $badge_class = $status_class[$order['status']] ?? 'secondary';
                        ?>
                        <h5 class="mb-2">Order Status</h5>
                        <span class="badge bg-<?= $badge_class ?> fs-6 px-3 py-2">
                            <?= ucfirst(htmlspecialchars($order['status'])) ?>
                        </span>
                        <?php if (!empty($order['tracking_number'])): ?>
                            <p class="text-muted mt-2 mb-0">
                                <strong>Tracking:</strong> <?= htmlspecialchars($order['tracking_number']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-truck me-2"></i>Shipping Address</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($order['shipping_name'])): ?>
                    <p class="mb-1"><strong><?= htmlspecialchars($order['shipping_name']) ?></strong></p>
                    <p class="mb-1"><?= htmlspecialchars($order['address_line1']) ?></p>
                    <?php if (!empty($order['address_line2'])): ?>
                        <p class="mb-1"><?= htmlspecialchars($order['address_line2']) ?></p>
                    <?php endif; ?>
                    <p class="mb-1"><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> <?= htmlspecialchars($order['postal_code']) ?></p>
                    <p class="mb-0"><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                <?php else: ?>
                    <p class="text-muted mb-0">No shipping address available</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead>
                            <tr class="border-bottom">
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr class="border-bottom">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if (!empty($item['image_url'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                                                     alt="<?= htmlspecialchars($item['product_name']) ?>"
                                                     class="me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="mb-1"><?= htmlspecialchars($item['product_name']) ?></h6>
                                                <small class="text-muted">SKU: <?= htmlspecialchars($item['sku']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle"><?= $item['quantity'] ?></td>
                                    <td class="text-end align-middle">EGP <?= number_format($item['unit_price'], 2) ?></td>
                                    <td class="text-end align-middle">
                                        <strong>EGP <?= number_format($item['subtotal'], 2) ?></strong>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 ms-auto">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-end">Subtotal:</td>
                                <td class="text-end"><strong>EGP <?= number_format($order['subtotal'], 2) ?></strong></td>
                            </tr>
                            <?php if ($order['discount_amount'] > 0): ?>
                                <tr class="text-success">
                                    <td class="text-end">Discount:</td>
                                    <td class="text-end"><strong>- EGP <?= number_format($order['discount_amount'], 2) ?></strong></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($order['tax_amount'] > 0): ?>
                                <tr>
                                    <td class="text-end">Tax:</td>
                                    <td class="text-end"><strong>EGP <?= number_format($order['tax_amount'], 2) ?></strong></td>
                                </tr>
                            <?php endif; ?>
                            <?php if ($order['shipping_cost'] > 0): ?>
                                <tr>
                                    <td class="text-end">Shipping:</td>
                                    <td class="text-end"><strong>EGP <?= number_format($order['shipping_cost'], 2) ?></strong></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="border-top">
                                <td class="text-end fs-5"><strong>Total:</strong></td>
                                <td class="text-end fs-5 text-primary"><strong>EGP <?= number_format($order['total_amount'], 2) ?></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if (!empty($order['notes'])): ?>
                    <hr>
                    <div class="mt-3">
                        <h6>Order Notes:</h6>
                        <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($order['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
