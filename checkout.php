<?php
define("SECURE_ACCESS", true);

session_start();

require_once 'includes/user_auth.php';
require_once 'includes/db_connect.php';

require_login('checkout.php');

$pageTitle = "Checkout - TechHub Electronics";

$user_id = get_current_user_id();
$stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Fetch default address if exists
$address = '';
$stmt2 = $conn->prepare("SELECT address_line1, address_line2, city, state, postal_code FROM user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1");
if ($stmt2) {
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    if ($addr_data = $result2->fetch_assoc()) {
        $address = trim($addr_data['address_line1'] . ' ' . $addr_data['address_line2']) . ', ' . 
                   $addr_data['city'] . ', ' . $addr_data['state'] . ' ' . $addr_data['postal_code'];
    }
    $stmt2->close();
}
$user_data['address'] = $address;

// Get cart items
$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$subtotal = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "SELECT p.id, p.name, p.price, pi.image_url 
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $quantity = $cart[$row['id']];
        $itemTotal = $row['price'] * $quantity;
        $subtotal += $itemTotal;
        $cartItems[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $quantity,
            'image_url' => $row['image_url'],
            'total' => $itemTotal
        ];
    }
    $stmt->close();
}

$shippingCost = 70.00;
$total = $subtotal + $shippingCost;

ob_start();
?>

<!-- Progress Steps -->
<div class="checkout-progress">
    <div class="container">
        <div class="progress-steps">
            <div class="progress-step completed">
                <div class="step-icon"><i class="bi bi-cart-check-fill"></i></div>
                <div class="step-label">Shopping Cart</div>
            </div>
            <div class="progress-line completed"></div>
            <div class="progress-step active">
                <div class="step-icon"><i class="bi bi-credit-card-fill"></i></div>
                <div class="step-label">Shipping & Payment</div>
            </div>
            <div class="progress-line"></div>
            <div class="progress-step">
                <div class="step-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="step-label">Order Complete</div>
            </div>
        </div>
    </div>
</div>

<!-- Back to Cart Link -->
<div class="container mb-3">
    <a href="Cart.php" class="btn-back-link"><i class="bi bi-arrow-left me-2"></i>Back to Cart</a>
</div>

<!-- Two Column Layout -->
<div class="container checkout-container">
    <div class="row g-4">
        <!-- Left Column: Checkout Form (70%) -->
        <div class="col-lg-8">
            <div class="checkout-card">
                <h2 class="checkout-title"><i class="bi bi-shield-lock-fill me-2"></i>Secure Checkout</h2>
                
                <form method="POST" action="checkout_process.php" id="checkoutForm" novalidate>
                    <!-- Shipping Information -->
                    <div class="checkout-section">
                        <h3 class="section-heading"><i class="bi bi-truck me-2"></i>Shipping Information</h3>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-checkout">
                                    <label for="customer_name" class="form-label-checkout">Full Name *</label>
                                    <input type="text" class="form-control-checkout" id="customer_name" name="customer_name" 
                                           value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-checkout">
                                    <label for="customer_email" class="form-label-checkout">Email Address *</label>
                                    <input type="email" class="form-control-checkout" id="customer_email" name="customer_email" 
                                           value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-checkout">
                                    <label for="customer_phone" class="form-label-checkout">Phone Number *</label>
                                    <input type="tel" class="form-control-checkout" id="customer_phone" name="customer_phone" 
                                           value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" placeholder="+20 123 456 7890" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-checkout">
                                    <label for="customer_address" class="form-label-checkout">Shipping Address *</label>
                                    <textarea class="form-control-checkout" id="customer_address" name="customer_address" rows="3" required><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-section">
                        <h3 class="section-heading"><i class="bi bi-credit-card-2-front me-2"></i>Payment Method</h3>
                        
                        <div class="payment-methods">
                            <div class="payment-method-box" data-method="card">
                                <input type="radio" name="payment_method" id="payment_card" value="card" checked>
                                <label for="payment_card">
                                    <div class="payment-method-header">
                                        <div class="payment-method-icon"><i class="bi bi-credit-card-fill"></i></div>
                                        <div class="payment-method-info">
                                            <div class="payment-method-title">Debit/Credit Card</div>
                                            <div class="payment-method-subtitle">Secure payment via card</div>
                                        </div>
                                    </div>
                                    <div class="payment-logos">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard">
                                    </div>
                                </label>
                            </div>

                            <div class="payment-method-box" data-method="cod">
                                <input type="radio" name="payment_method" id="payment_cod" value="cash_on_delivery">
                                <label for="payment_cod">
                                    <div class="payment-method-header">
                                        <div class="payment-method-icon"><i class="bi bi-cash-coin"></i></div>
                                        <div class="payment-method-info">
                                            <div class="payment-method-title">Cash on Delivery</div>
                                            <div class="payment-method-subtitle">Pay when you receive</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Card Fields -->
                        <div id="card_fields" class="card-fields">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group-checkout">
                                        <label for="card_number" class="form-label-checkout">Card Number *</label>
                                        <div class="input-with-icon">
                                            <i class="bi bi-lock-fill input-icon-left"></i>
                                            <input type="text" class="form-control-checkout with-icon" id="card_number" name="card_number" 
                                                   placeholder="1234 5678 9012 3456" maxlength="19">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-checkout">
                                        <label for="card_expiry" class="form-label-checkout">Expiry Date *</label>
                                        <input type="text" class="form-control-checkout" id="card_expiry" name="card_expiry" 
                                               placeholder="MM/YY" maxlength="5">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-checkout">
                                        <label for="card_cvv" class="form-label-checkout">CVV *</label>
                                        <input type="text" class="form-control-checkout" id="card_cvv" name="card_cvv" 
                                               placeholder="123" maxlength="4">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Place Order Button -->
                    <div class="checkout-footer">
                        <button type="submit" class="btn-place-order">
                            <i class="bi bi-shield-check me-2"></i>Place Order Securely
                        </button>
                        <div class="security-note">
                            <i class="bi bi-lock-fill me-2"></i>
                            Your payment information is encrypted and secure
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Column: Order Summary (30%) -->
        <div class="col-lg-4">
            <div class="order-summary-card sticky-summary">
                <h3 class="summary-title">Order Summary</h3>
                
                <!-- Cart Items -->
                <div class="summary-items">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="summary-item">
                        <div class="item-image">
                            <img src="<?= !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/60' ?>" 
                                 alt="<?= htmlspecialchars($item['name']) ?>">
                        </div>
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="item-quantity">Qty: <?= $item['quantity'] ?></div>
                        </div>
                        <div class="item-price">EGP <?= number_format($item['total'], 2) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Promo Code -->
                <div class="promo-section">
                    <label for="promo_code" class="promo-label">Have a promo code?</label>
                    <div class="promo-input-group">
                        <input type="text" class="promo-input" id="promo_code" name="promo_code" placeholder="Enter code" form="checkoutForm">
                        <button type="button" class="promo-apply-btn">Apply</button>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="price-breakdown">
                    <div class="price-row">
                        <span>Subtotal</span>
                        <span>EGP <?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="price-row">
                        <span>Shipping</span>
                        <span>EGP <?= number_format($shippingCost, 2) ?></span>
                    </div>
                    <div class="price-row total">
                        <span>Total</span>
                        <span>EGP <?= number_format($total, 2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardRadio = document.getElementById('payment_card');
    const codRadio = document.getElementById('payment_cod');
    const cardFields = document.getElementById('card_fields');
    const cardInputs = cardFields.querySelectorAll('input');
    const paymentBoxes = document.querySelectorAll('.payment-method-box');

    function toggleCardFields() {
        if (cardRadio.checked) {
            cardFields.style.display = 'block';
            cardInputs.forEach(input => input.required = true);
        } else {
            cardFields.style.display = 'none';
            cardInputs.forEach(input => input.required = false);
        }
    }

    function updatePaymentBoxes() {
        paymentBoxes.forEach(box => {
            const radio = box.querySelector('input[type="radio"]');
            if (radio.checked) {
                box.classList.add('selected');
            } else {
                box.classList.remove('selected');
            }
        });
    }

    cardRadio.addEventListener('change', function() {
        toggleCardFields();
        updatePaymentBoxes();
    });
    
    codRadio.addEventListener('change', function() {
        toggleCardFields();
        updatePaymentBoxes();
    });
    
    toggleCardFields();
    updatePaymentBoxes();
});
</script>

<?php
$pageContent = ob_get_clean();
include 'includes/template.php';
?>
