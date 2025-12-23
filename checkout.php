<?php
define("SECURE_ACCESS", true);

session_start();

require_once 'includes/user_auth.php';
require_once 'includes/db_connect.php';

require_login('checkout.php');

$pageTitle = "Checkout - Online Phones Store";

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

ob_start();
?>

<div class="row align-items-center mb-4">
    <div class="col-12 col-md-8">
        <h1 class="h3 mb-1">Checkout</h1>
        <div class="text-muted">Enter your details to place the order.</div>
    </div>
    <div class="col-12 col-md-4 mt-3 mt-md-0 text-md-end">
        <a href="Cart.php" class="btn btn-outline-secondary">Back to Cart</a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="checkout_process.php" novalidate>
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="customer_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                   value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="customer_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" 
                                   value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="customer_phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" 
                                   value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" placeholder="+1 (555) 123-4567" required>
                            <div class="form-text">We'll use this to contact you about your order.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="promo_code" class="form-label">Promo Code <span class="text-muted">(Optional)</span></label>
                            <input type="text" class="form-control" id="promo_code" name="promo_code" placeholder="Enter promo code">
                            <div class="form-text">Have a discount code? Enter it here.</div>
                        </div>

                        <div class="col-12">
                            <label for="customer_address" class="form-label">Address</label>
                            <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required><?= htmlspecialchars($user_data['address'] ?? '') ?></textarea>
                        </div>

                        <div class="col-12">
                            <hr class="my-4">
                            <h5 class="mb-3">Payment Method</h5>
                        </div>

                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="card" checked>
                                <label class="form-check-label" for="payment_card">
                                    <strong>Debit/Credit Card</strong>
                                </label>
                            </div>

                            <div id="card_fields" class="row g-3 ms-4 mb-3">
                                <div class="col-12">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="card_expiry" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="col-12 col-md-6">
                                    <label for="card_cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="card_cvv" name="card_cvv" placeholder="123" maxlength="4">
                                </div>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cod" value="cash_on_delivery">
                                <label class="form-check-label" for="payment_cod">
                                    <strong>Cash on Delivery</strong>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 d-flex flex-column flex-sm-row gap-2 justify-content-end mt-2">
                            <a href="index.php" class="btn btn-outline-primary">Continue Shopping</a>
                            <button type="submit" class="btn btn-success">Place Order</button>
                        </div>
                    </div>
                </form>
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

    function toggleCardFields() {
        if (cardRadio.checked) {
            cardFields.style.display = '';
            cardInputs.forEach(input => input.required = true);
        } else {
            cardFields.style.display = 'none';
            cardInputs.forEach(input => input.required = false);
        }
    }

    cardRadio.addEventListener('change', toggleCardFields);
    codRadio.addEventListener('change', toggleCardFields);
    
    toggleCardFields();
});
</script>

<?php
$pageContent = ob_get_clean();
include 'includes/template.php';
?>
