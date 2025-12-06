<?php
// Define secure access constant
define("SECURE_ACCESS", true);

// Include the database connection file
include 'includes/db_connect.php';

// Get product ID from URL parameter
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize variables
$product = null;
$error = '';

// Fetch product details from database
if ($productId > 0) {
    $sql = "SELECT id, name, description, price, image_url, category, stock FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $pageTitle = htmlspecialchars($product['name']) . " - Online Phones Store";
    } else {
        $error = "Product not found.";
        $pageTitle = "Product Not Found - Online Phones Store";
    }
    $stmt->close();
} else {
    $error = "Invalid product ID.";
    $pageTitle = "Error - Online Phones Store";
}

// Start output buffering
ob_start();
?>

<div class="container">
    <?php if ($error): ?>
        <!-- Error Message -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <h4 class="alert-heading">Error!</h4>
                    <p><?= htmlspecialchars($error) ?></p>
                    <hr>
                    <a href="index.php" class="btn btn-primary">Back to Shop</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Product Detail -->
        <div class="row mb-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <?php if (!empty($product['category'])): ?>
                            <li class="breadcrumb-item">
                                <a href="index.php?category=<?= urlencode($product['category']) ?>">
                                    <?= htmlspecialchars($product['category']) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= htmlspecialchars($product['name']) ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6 mb-4">
                <div class="card">
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             style="max-height: 500px; object-fit: contain; padding: 20px;">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                             style="height: 500px;">
                            <span class="text-white display-4">No Image Available</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-md-6">
                <h1 class="display-5 mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <?php if (!empty($product['category'])): ?>
                    <p class="mb-3">
                        <span class="badge bg-info fs-6">
                            <?= htmlspecialchars($product['category']) ?>
                        </span>
                    </p>
                <?php endif; ?>

                <h2 class="text-primary mb-4">$<?= number_format($product['price'], 2) ?></h2>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <?php if (isset($product['stock'])): ?>
                    <div class="mb-4">
                        <h5>Availability</h5>
                        <?php if ($product['stock'] > 0): ?>
                            <p class="text-success">
                                <strong>In Stock</strong> (<?= $product['stock'] ?> available)
                            </p>
                        <?php else: ?>
                            <p class="text-danger">
                                <strong>Out of Stock</strong>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <?php if (!isset($product['stock']) || $product['stock'] > 0): ?>
                        <button class="btn btn-success btn-lg add-to-cart" 
                                data-id="<?= $product['id'] ?>">
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg" disabled>
                            Out of Stock
                        </button>
                    <?php endif; ?>
                    
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Optional Enhancement) -->
        <?php if (!empty($product['category'])): ?>
            <hr class="my-5">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4">Related Products</h3>
                </div>
            </div>

            <?php
            // Fetch related products from the same category
            $relatedSql = "SELECT id, name, price, image_url FROM products 
                          WHERE category = ? AND id != ? LIMIT 4";
            $relatedStmt = $conn->prepare($relatedSql);
            $relatedStmt->bind_param("si", $product['category'], $productId);
            $relatedStmt->execute();
            $relatedResult = $relatedStmt->get_result();

            if ($relatedResult->num_rows > 0):
            ?>
                <div class="row">
                    <?php while($relatedProduct = $relatedResult->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                <?php if (!empty($relatedProduct['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($relatedProduct['image_url']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($relatedProduct['name']) ?>"
                                         style="height: 150px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                                         style="height: 150px;">
                                        <span class="text-white">No Image</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body">
                                    <h6 class="card-title"><?= htmlspecialchars($relatedProduct['name']) ?></h6>
                                    <p class="text-primary">$<?= number_format($relatedProduct['price'], 2) ?></p>
                                </div>
                                
                                <div class="card-footer">
                                    <a href="product_detail.php?id=<?= $relatedProduct['id'] ?>" 
                                       class="btn btn-sm btn-primary w-100">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php
            endif;
            $relatedStmt->close();
            ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
// Close database connection
$conn->close();

// Capture the content and store it in $pageContent
$pageContent = ob_get_clean();

// Include the template
include 'includes/template.php';
?>
