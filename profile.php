<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';
require_once 'includes/user_auth.php';

require_login('profile.php');

$pageTitle = "My Profile - TechHub Electronics";
$errors = [];
$success = false;

$user_id = get_current_user_id();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }

    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required to set a new password";
        } elseif (strlen($new_password) < 6) {
            $errors[] = "New password must be at least 6 characters";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords do not match";
        }
    }

    if (empty($errors)) {
        if (!empty($new_password)) {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!password_verify($current_password, $user['password'])) {
                $errors[] = "Current password is incorrect";
            } else {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ?, password = ? WHERE id = ?");
                if (!$stmt) {
                    $errors[] = "Database error: " . $conn->error;
                } else {
                    $stmt->bind_param("sssi", $full_name, $phone, $hashed_password, $user_id);
                
                    if ($stmt->execute()) {
                        $success = true;
                        $_SESSION['user_full_name'] = $full_name;
                    } else {
                        $errors[] = "Failed to update profile";
                    }
                    $stmt->close();
                }
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
            if (!$stmt) {
                $errors[] = "Database error: " . $conn->error;
            } else {
                $stmt->bind_param("ssi", $full_name, $phone, $user_id);
            
                if ($stmt->execute()) {
                    $success = true;
                    $_SESSION['user_full_name'] = $full_name;
                } else {
                    $errors[] = "Failed to update profile";
                }
                $stmt->close();
            }
        }
    }
}

$stmt = $conn->prepare("SELECT username, email, full_name, phone, created_at FROM users WHERE id = ?");
if (!$stmt) {
    die("Database error: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

ob_start();
?>

<div class="row">
    <div class="col-md-3 mb-4 profile-sidebar">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Account Menu</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="profile.php" class="text-decoration-none fw-bold" style="color: white; background-color: #764ba2; padding: 0.5rem 1rem; border-radius: 0.5rem; display: block;">My Profile</a></li>
                    <li class="mb-2"><a href="orders.php" class="text-decoration-none" style="color: #764ba2;">My Orders</a></li>
                    <li class="mb-2"><a href="wishlist.php" class="text-decoration-none" style="color: #764ba2;"><i class="bi bi-heart me-1"></i>My Wishlist</a></li>
                    <li class="mb-2"><a href="Cart.php" class="text-decoration-none" style="color: #764ba2;">My Cart</a></li>
                    <li class="mb-2"><a href="logout.php" class="text-decoration-none text-danger">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9 profile-content">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">My Profile</h2>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Profile updated successfully!
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="profile.php">
                    <h5 class="mb-3">Account Information</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user_data['username']) ?>" disabled>
                            <small class="form-text text-muted">Username cannot be changed</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user_data['email']) ?>" disabled>
                            <small class="form-text text-muted">Email cannot be changed</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Change Password (Optional)</h5>
                    <p class="text-muted small">Leave blank if you don't want to change your password</p>

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <small class="form-text text-muted">At least 6 characters</small>
                        </div>
                        <div class="col-md-6">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">Member since: <?= date('F j, Y', strtotime($user_data['created_at'])) ?></small>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
