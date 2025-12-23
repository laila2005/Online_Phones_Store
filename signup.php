<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';

$pageTitle = "Sign Up - TechHub Electronics";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        if (!$stmt) {
            $errors[] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors[] = "Username or email already exists";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt) {
                    $errors[] = "Database error: " . $conn->error;
                } else {
                    $stmt->bind_param("sssss", $username, $email, $hashed_password, $full_name, $phone);
                    
                    if ($stmt->execute()) {
                        $success = true;
                        $newUserId = $stmt->insert_id;
                        $stmt->close();
                        
                        // Auto-login the user after signup
                        $_SESSION['user_id'] = $newUserId;
                        $_SESSION['username'] = $username;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['user_full_name'] = $full_name;
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
                        
                        $_SESSION['signup_success'] = "Account created successfully! Welcome!";
                        header("Location: index.php");
                        exit();
                    } else {
                        $errors[] = "Registration failed: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    }
}

ob_start();
?>

<div class="auth-container">
    <div class="auth-card auth-card-large">
        <!-- Auth Header -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="bi bi-person-plus-fill"></i>
            </div>
            <h2 class="auth-title">Create Your Account</h2>
            <p class="auth-subtitle">Join TechHub Electronics today</p>
        </div>

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

        <!-- Signup Form -->
        <form method="POST" action="signup.php" class="auth-form">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group-auth">
                        <label for="username" class="form-label-auth">
                            <i class="bi bi-at me-2"></i>Username *
                        </label>
                        <input type="text" class="form-control-auth" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                               placeholder="Choose a username" required>
                        <small class="form-hint-auth">Letters, numbers, and underscores only</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group-auth">
                        <label for="email" class="form-label-auth">
                            <i class="bi bi-envelope-fill me-2"></i>Email *
                        </label>
                        <input type="email" class="form-control-auth" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                               placeholder="your@email.com" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group-auth">
                        <label for="full_name" class="form-label-auth">
                            <i class="bi bi-person-fill me-2"></i>Full Name *
                        </label>
                        <input type="text" class="form-control-auth" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" 
                               placeholder="Enter your full name" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group-auth">
                        <label for="phone" class="form-label-auth">
                            <i class="bi bi-telephone-fill me-2"></i>Phone
                        </label>
                        <input type="tel" class="form-control-auth" id="phone" name="phone" 
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" 
                               placeholder="+20 123 456 7890">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group-auth">
                        <label for="password" class="form-label-auth">
                            <i class="bi bi-lock-fill me-2"></i>Password *
                        </label>
                        <input type="password" class="form-control-auth" id="password" name="password" 
                               placeholder="Create a password" required>
                        <small class="form-hint-auth">At least 6 characters</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group-auth">
                        <label for="confirm_password" class="form-label-auth">
                            <i class="bi bi-lock-fill me-2"></i>Confirm Password *
                        </label>
                        <input type="password" class="form-control-auth" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm your password" required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-auth-primary">
                <i class="bi bi-check-circle me-2"></i>Create Account
            </button>
        </form>

        <!-- Auth Footer -->
        <div class="auth-footer">
            <p class="auth-footer-text">
                Already have an account? 
                <a href="login.php" class="auth-link">Sign in here</a>
            </p>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
