<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';
require_once 'includes/user_auth.php';

require_login('orders.php');

$pageTitle = "My Orders - TechHub Electronics";

$user_id = get_current_user_id();

$stmt = $conn->prepare("
    SELECT o.id, o.order_number, o.total_amount, o.status, o.payment_method, o.order_date,
           COUNT(oi.id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id, o.order_number, o.total_amount, o.status, o.payment_method, o.order_date
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

ob_start();
?>

<div class="row">
    <div class="col-md-3 mb-4 profile-sidebar">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Account Menu</h5>
                <ul class="list-unstyled profile-menu">
                    <li class="mb-2"><a href="profile.php" class="profile-menu-link"><i class="bi bi-person-circle me-2"></i>My Profile</a></li>
                    <li class="mb-2"><a href="orders.php" class="profile-menu-link active"><i class="bi bi-box-seam me-2"></i>My Orders</a></li>
                    <li class="mb-2"><a href="wishlist.php" class="profile-menu-link"><i class="bi bi-heart me-2"></i>My Wishlist</a></li>
                    <li class="mb-2"><a href="Cart.php" class="profile-menu-link"><i class="bi bi-cart3 me-2"></i>My Cart</a></li>
                    <li class="mb-2"><a href="logout.php" class="profile-menu-link logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">My Orders</h2>

                <?php if (empty($orders)): ?>
                    <div class="alert alert-info">
                        <p class="mb-0">You haven't placed any orders yet.</p>
                        <a href="index.php" class="alert-link">Start shopping now!</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                    $status_class = [
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $badge_class = $status_class[$order['status']] ?? 'secondary';
                                    ?>
                                    <tr>
                                        <td class="fw-semibold"><?= htmlspecialchars($order['order_number']) ?></td>
                                        <td><?= date('M j, Y', strtotime($order['order_date'])) ?></td>
                                        <td><?= $order['item_count'] ?> item<?= $order['item_count'] != 1 ? 's' : '' ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $badge_class ?>">
                                                <?= ucfirst(htmlspecialchars($order['status'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="order_details.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
