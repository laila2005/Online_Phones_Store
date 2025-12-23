<?php
define("SECURE_ACCESS", true);
session_start();

require_once 'includes/db_connect.php';
require_once 'includes/user_auth.php';

require_login('wishlist.php');

$pageTitle = "My Wishlist - TechHub Electronics";

$user_id = get_current_user_id();

// Fetch wishlist items with product details
$stmt = $conn->prepare("
    SELECT w.id as wishlist_id, p.id, p.name, p.slug, p.short_description, p.price, 
           p.compare_at_price, p.stock_quantity, b.name as brand_name, c.name as category_name,
           pi.image_url, pi.alt_text
    FROM wishlists w
    JOIN products p ON w.product_id = p.id
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlist_items = $result->fetch_all(MYSQLI_ASSOC);
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
                    <li class="mb-2"><a href="orders.php" class="text-decoration-none" style="color: #764ba2;">My Orders</a></li>
                    <li class="mb-2"><a href="wishlist.php" class="text-decoration-none fw-bold" style="color: white; background-color: #764ba2; padding: 0.5rem 1rem; border-radius: 0.5rem; display: block;">My Wishlist</a></li>
                    <li class="mb-2"><a href="Cart.php" class="text-decoration-none" style="color: #764ba2;">My Cart</a></li>
                    <li class="mb-2"><a href="logout.php" class="text-decoration-none text-danger">Logout</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4"><i class="bi bi-heart-fill text-danger me-2"></i>My Wishlist</h2>

                <?php if (empty($wishlist_items)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-heart me-2"></i>
                        <strong>Your wishlist is empty!</strong>
                        <p class="mb-0 mt-2">Start adding products you love to your wishlist.</p>
                        <a href="index.php" class="alert-link">Browse products now!</a>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-4">You have <?= count($wishlist_items) ?> item<?= count($wishlist_items) != 1 ? 's' : '' ?> in your wishlist</p>
                    
                    <div class="row">
                        <?php foreach ($wishlist_items as $item): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 shadow-sm position-relative">
                                    <button class="btn btn-link position-absolute top-0 end-0 m-2 remove-from-wishlist" 
                                            data-wishlist-id="<?= $item['wishlist_id'] ?>"
                                            data-product-id="<?= $item['id'] ?>"
                                            style="z-index: 10; padding: 0.25rem 0.5rem;">
                                        <i class="bi bi-x-circle-fill" style="font-size: 1.5rem; color: #dc3545;"></i>
                                    </button>
                                    
                                    <?php if (!empty($item["compare_at_price"]) && $item["compare_at_price"] > $item["price"]): ?>
                                        <span class="badge bg-danger position-absolute top-0 start-0 m-2">Sale</span>
                                    <?php endif; ?>
                                    
                                    <img class="card-img-top" 
                                         src="<?= !empty($item['image_url']) ? htmlspecialchars($item['image_url']) : 'https://via.placeholder.com/300x200?text=' . urlencode($item['name']) ?>" 
                                         alt="<?= !empty($item['alt_text']) ? htmlspecialchars($item['alt_text']) : htmlspecialchars($item['name']) ?>"
                                         style="height: 200px; object-fit: cover;">
                                    
                                    <div class="card-body d-flex flex-column">
                                        <?php if (!empty($item["brand_name"])): ?>
                                            <small class="text-muted mb-1"><?= htmlspecialchars($item["brand_name"]) ?></small>
                                        <?php endif; ?>
                                        
                                        <h5 class="card-title"><?= htmlspecialchars($item["name"]) ?></h5>
                                        
                                        <?php if (!empty($item["category_name"])): ?>
                                            <span class="badge bg-info mb-2"><?= htmlspecialchars($item["category_name"]) ?></span>
                                        <?php endif; ?>
                                        
                                        <div class="mb-2">
                                            <?php if (!empty($item["compare_at_price"]) && $item["compare_at_price"] > $item["price"]): ?>
                                                <span class="text-muted text-decoration-line-through small">EGP <?= number_format($item["compare_at_price"], 2) ?></span>
                                                <h6 class="text-danger mb-0">EGP <?= number_format($item["price"], 2) ?></h6>
                                            <?php else: ?>
                                                <h6 class="text-primary mb-0">EGP <?= number_format($item["price"], 2) ?></h6>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($item["stock_quantity"] <= 0): ?>
                                            <span class="badge bg-secondary mb-2">Out of Stock</span>
                                        <?php elseif ($item["stock_quantity"] < 10): ?>
                                            <span class="badge bg-warning text-dark mb-2">Only <?= $item["stock_quantity"] ?> left</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent border-top-0">
                                        <a href="product_detail.php?id=<?= $item["id"] ?>" 
                                           class="btn btn-primary btn-sm w-100 mb-2">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </a>
                                        <?php if ($item["stock_quantity"] > 0): ?>
                                            <button class="btn btn-success btn-sm w-100 add-to-cart" 
                                                    data-id="<?= $item["id"] ?>">
                                                <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                                Out of Stock
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Remove from wishlist functionality
document.querySelectorAll('.remove-from-wishlist').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const productId = this.getAttribute('data-product-id');
        
        if (!confirm('Remove this item from your wishlist?')) {
            return;
        }

        const formData = new FormData();
        formData.append('product_id', productId);

        fetch('add_to_wishlist.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                // Reload page to show updated wishlist
                window.location.reload();
            } else {
                alert('Failed to remove item from wishlist');
            }
        })
        .catch(err => {
            alert('Could not remove item from wishlist');
        });
    });
});
</script>

<?php
$pageContent = ob_get_clean();
require_once 'includes/template.php';
?>
