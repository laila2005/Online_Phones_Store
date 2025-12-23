<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';

$pageTitle = "Sign Up - Online Phones Store";
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

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Create Account</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="signup.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        <small class="form-text text-muted">Letters, numbers, and underscores only</small>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>


                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <small class="form-text text-muted">At least 6 characters</small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                </form>

                <div class="text-center mt-3">
                    <p class="mb-0">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
