<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';

$pageTitle = "Login - TechHub Electronics";
$errors = [];
$signup_success = $_SESSION['signup_success'] ?? '';
unset($_SESSION['signup_success']);

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username_or_email = trim($_POST['username_or_email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username_or_email)) {
        $errors[] = "Username or email is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, email, password, full_name FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username_or_email, $username_or_email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_full_name'] = $user['full_name'];
                $_SESSION['user_logged_in'] = true;

                // Check if there's a pending cart item to add
                if (isset($_SESSION['pending_cart_product'])) {
                    $productId = (int)$_SESSION['pending_cart_product'];
                    $quantity = isset($_SESSION['pending_cart_quantity']) ? (int)$_SESSION['pending_cart_quantity'] : 1;
                    
                    // Fetch product details
                    $stmt2 = $conn->prepare('SELECT p.id, p.name, p.price, p.stock_quantity, b.name as brand_name,
                                             pi.image_url, pi.alt_text
                                             FROM products p 
                                             LEFT JOIN brands b ON p.brand_id = b.id 
                                             LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                                             WHERE p.id = ? AND p.status = "active"');
                    if ($stmt2) {
                        $stmt2->bind_param('i', $productId);
                        $stmt2->execute();
                        $stmt2->bind_result($id, $name, $price, $stockQuantity, $brandName, $imageUrl, $altText);
                        
                        if ($stmt2->fetch() && $stockQuantity > 0) {
                            $stmt2->close();
                            
                            // Add to cart
                            if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
                                $_SESSION['cart'] = [];
                            }
                            
                            $_SESSION['cart'][] = [
                                'id' => $id,
                                'name' => $name,
                                'price' => (float)$price,
                                'image_url' => $imageUrl ?? '',
                                'alt_text' => $altText ?? '',
                                'quantity' => $quantity,
                            ];
                        } else {
                            $stmt2->close();
                        }
                    }
                    
                    // Clear pending cart items
                    unset($_SESSION['pending_cart_product']);
                    unset($_SESSION['pending_cart_quantity']);
                }

                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                
                header("Location: " . $redirect);
                exit();
            } else {
                $errors[] = "Invalid username/email or password";
            }
        } else {
            $errors[] = "Invalid username/email or password";
        }
        $stmt->close();
    }
}

ob_start();
?>

<div class="auth-container">
    <div class="auth-card">
        <!-- Auth Header -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Sign in to your TechHub Electronics account</p>
        </div>

        <?php if ($signup_success): ?>
            <div class="alert alert-success auth-alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= htmlspecialchars($signup_success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger auth-alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="login.php" class="auth-form">
            <div class="form-group-auth">
                <label for="username_or_email" class="form-label-auth">
                    <i class="bi bi-person-circle me-2"></i>Username or Email
                </label>
                <input type="text" class="form-control-auth" id="username_or_email" name="username_or_email" 
                       value="<?= htmlspecialchars($_POST['username_or_email'] ?? '') ?>" 
                       placeholder="Enter your username or email" required>
            </div>

            <div class="form-group-auth">
                <label for="password" class="form-label-auth">
                    <i class="bi bi-lock-fill me-2"></i>Password
                </label>
                <input type="password" class="form-control-auth" id="password" name="password" 
                       placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn-auth-primary">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <!-- Auth Footer -->
        <div class="auth-footer">
            <p class="auth-footer-text">
                Don't have an account? 
                <a href="signup.php" class="auth-link">Create one now</a>
            </p>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
