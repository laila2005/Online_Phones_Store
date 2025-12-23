<?php
define("SECURE_ACCESS", true);
session_start();

$pageTitle = "Order Confirmation - TechHub Electronics";

$order = isset($_SESSION['last_order']) ? $_SESSION['last_order'] : null;

if (!$order) {
    header('Location: index.php');
    exit;
}

$customerName = htmlspecialchars($order['customer_name']);
$orderNumber = htmlspecialchars($order['order_number']);
$orderId = (int)$order['order_id'];
$subtotal = isset($order['subtotal']) ? (float)$order['subtotal'] : (float)$order['total_amount'];
$discountAmount = isset($order['discount_amount']) ? (float)$order['discount_amount'] : 0;
$shippingCost = isset($order['shipping_cost']) ? (float)$order['shipping_cost'] : 70.00;
$totalAmount = (float)$order['total_amount'];
$couponCode = isset($order['coupon_code']) ? $order['coupon_code'] : '';
$paymentMethod = $order['payment_method'] === 'cash_on_delivery' ? 'Cash on Delivery' : 'Card Payment';
$items = $order['items'];

$orderDate = new DateTime($order['order_date']);
$estimatedDelivery = clone $orderDate;
$estimatedDelivery->modify('+5 days');

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card shadow-sm border-success mb-4">
            <div class="card-header bg-success text-white text-center py-3">
                <h1 class="h4 mb-0">
                    <i class="bi bi-check-circle-fill me-2"></i>Order Confirmed!
                </h1>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <h2 class="h5 mb-2">Thank you, <?= $customerName ?>!</h2>
                    <p class="text-muted mb-0">Your order has been successfully placed and is being processed.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted mb-2">Order Number</h6>
                            <p class="mb-0 fw-bold"><?= $orderNumber ?></p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted mb-2">Order ID</h6>
                            <p class="mb-0 fw-bold">#<?= $orderId ?></p>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="text-muted mb-2">Order Date</h6>
                            <p class="mb-0"><?= $orderDate->format('F j, Y') ?></p>
                            <small class="text-muted"><?= $orderDate->format('g:i A') ?></small>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h6 class="text-muted mb-2">Estimated Delivery</h6>
                            <p class="mb-0 text-success fw-bold"><?= $estimatedDelivery->format('F j, Y') ?></p>
                            <small class="text-muted">Within 5 business days</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Order Summary</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): 
                                $itemId = (int)$item['id'];
                                $itemName = htmlspecialchars($item['name']);
                                $itemQty = (int)$item['quantity'];
                                $itemPrice = number_format((float)$item['price'], 2);
                                $itemSubtotal = number_format((float)$item['price'] * $itemQty, 2);
                            ?>
                            <tr>
                                <td><span class="badge bg-secondary">#<?= $itemId ?></span></td>
                                <td><?= $itemName ?></td>
                                <td class="text-center"><?= $itemQty ?></td>
                                <td class="text-end">EGP <?= $itemPrice ?></td>
                                <td class="text-end fw-bold">EGP <?= $itemSubtotal ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end">Subtotal:</td>
                                <td class="text-end">EGP <?= number_format($subtotal, 2) ?></td>
                            </tr>
                            <?php if ($discountAmount > 0 && !empty($couponCode)): ?>
                            <tr class="table-success">
                                <td colspan="4" class="text-end">
                                    <span class="badge bg-success me-2"><?= htmlspecialchars($couponCode) ?></span>
                                    Discount (<?= number_format(($discountAmount / $subtotal) * 100, 1) ?>%):
                                </td>
                                <td class="text-end text-success">-EGP <?= number_format($discountAmount, 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="4" class="text-end">Shipping:</td>
                                <td class="text-end">EGP <?= number_format($shippingCost, 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total Amount:</td>
                                <td class="text-end fw-bold text-success fs-5">EGP <?= number_format($totalAmount, 2) ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Payment Method:</td>
                                <td class="text-end"><?= $paymentMethod ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="alert alert-info mt-4 mb-0" role="alert">
                    <strong>What's next?</strong> You will receive an email confirmation at <strong><?= htmlspecialchars($order['customer_email']) ?></strong> and we'll call you at <strong><?= htmlspecialchars($order['customer_phone']) ?></strong> to confirm delivery details.
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mb-4">
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            <a href="index.php" class="btn btn-outline-secondary">View More Products</a>
        </div>

        <div class="text-center text-muted">
            <small>Need help? Contact us via email or call customer support.</small>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
include 'includes/template.php';
?>
