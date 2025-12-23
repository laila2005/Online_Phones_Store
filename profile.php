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
                <ul class="list-unstyled profile-menu">
                    <li class="mb-2"><a href="profile.php" class="profile-menu-link active"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
                    <li class="mb-2"><a href="orders.php" class="profile-menu-link"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                    <li class="mb-2"><a href="wishlist.php" class="profile-menu-link"><i class="bi bi-heart me-2"></i>My Wishlist</a></li>
                    <li class="mb-2"><a href="Cart.php" class="profile-menu-link"><i class="bi bi-cart3 me-2"></i>My Cart</a></li>
                    <li class="mb-2"><a href="logout.php" class="profile-menu-link logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9 profile-content">
        <div class="card">
            <div class="card-body">
                <!-- Profile Header with Avatar -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php
                        $initials = '';
                        $name_parts = explode(' ', $user_data['full_name'] ?? $user_data['username']);
                        foreach ($name_parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        }
                        $initials = substr($initials, 0, 2);
                        ?>
                        <div class="avatar-circle"><?= $initials ?></div>
                    </div>
                    <div class="profile-greeting">
                        <h2 class="greeting-text">Hello, <?= htmlspecialchars(explode(' ', $user_data['full_name'] ?? $user_data['username'])[0]) ?>!</h2>
                        <p class="greeting-subtext">Manage your account information and settings</p>
                    </div>
                </div>

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
                    <!-- Account Information Section -->
                    <div class="profile-section">
                        <h5 class="section-title"><i class="bi bi-person-badge me-2"></i>Account Information</h5>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Username</label>
                                    <div class="input-with-icon">
                                        <i class="bi bi-lock-fill input-icon"></i>
                                        <input type="text" class="form-control-modern" value="<?= htmlspecialchars($user_data['username']) ?>" disabled>
                                    </div>
                                    <small class="form-hint">Username cannot be changed</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">Email</label>
                                    <div class="input-with-icon">
                                        <i class="bi bi-lock-fill input-icon"></i>
                                        <input type="email" class="form-control-modern" value="<?= htmlspecialchars($user_data['email']) ?>" disabled>
                                    </div>
                                    <small class="form-hint">Email cannot be changed</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="full_name" class="form-label-modern">Full Name *</label>
                                    <input type="text" class="form-control-modern" id="full_name" name="full_name" 
                                           value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="phone" class="form-label-modern">Phone</label>
                                    <input type="tel" class="form-control-modern" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password Section -->
                    <div class="profile-section mt-5">
                        <h5 class="section-title"><i class="bi bi-shield-lock me-2"></i>Change Password</h5>
                        <p class="section-subtitle">Leave blank if you don't want to change your password</p>

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="form-group-modern">
                                    <label for="current_password" class="form-label-modern">Current Password</label>
                                    <input type="password" class="form-control-modern" id="current_password" name="current_password" placeholder="Enter your current password">
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="new_password" class="form-label-modern">New Password</label>
                                    <input type="password" class="form-control-modern" id="new_password" name="new_password" placeholder="At least 6 characters">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-modern">
                                    <label for="confirm_password" class="form-label-modern">Confirm New Password</label>
                                    <input type="password" class="form-control-modern" id="confirm_password" name="confirm_password" placeholder="Re-enter new password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Member Info & Submit Button -->
                    <div class="profile-footer">
                        <div class="member-since">
                            <i class="bi bi-calendar-check me-2"></i>
                            <small>Member since <?= date('F j, Y', strtotime($user_data['created_at'])) ?></small>
                        </div>
                        <button type="submit" class="btn btn-update-profile">
                            <i class="bi bi-check-circle me-2"></i>Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
