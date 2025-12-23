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
    $sql = "SELECT p.id, p.sku, p.name, p.slug, p.description, p.short_description, 
                   p.price, p.compare_at_price, p.stock_quantity, p.specs, p.warranty_period,
                   p.is_featured, b.name as brand_name, b.slug as brand_slug,
                   c.name as category_name, c.id as category_id
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = ? AND p.status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        if (!empty($product['specs'])) {
            $product['specs_array'] = json_decode($product['specs'], true);
        }
        
        // Fetch product images
        $imageSql = "SELECT image_url, alt_text, is_primary FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order ASC";
        $imageStmt = $conn->prepare($imageSql);
        $imageStmt->bind_param("i", $productId);
        $imageStmt->execute();
        $imageResult = $imageStmt->get_result();
        $product['images'] = $imageResult->fetch_all(MYSQLI_ASSOC);
        $imageStmt->close();
        
        $pageTitle = htmlspecialchars($product['name']) . " - TechHub Electronics";
    } else {
        $error = "Product not found.";
        $pageTitle = "Product Not Found - TechHub Electronics";
    }
    $stmt->close();
} else {
    $error = "Invalid product ID.";
    $pageTitle = "Error - TechHub Electronics";
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
                        <?php if (!empty($product['category_name'])): ?>
                            <li class="breadcrumb-item">
                                <a href="index.php?category=<?= $product['category_id'] ?>">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($product['brand_name'])): ?>
                            <li class="breadcrumb-item">
                                <a href="index.php?brand=<?= urlencode($product['brand_slug']) ?>">
                                    <?= htmlspecialchars($product['brand_name']) ?>
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
                    <?php if (!empty($product['images'])): ?>
                        <img id="mainProductImage" 
                             src="<?= htmlspecialchars($product['images'][0]['image_url']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($product['images'][0]['alt_text'] ?? $product['name']) ?>"
                             style="max-height: 500px; object-fit: contain; padding: 20px; transition: opacity 0.3s ease;">
                        
                        <?php if (count($product['images']) > 1): ?>
                            <div class="card-body">
                                <div class="row g-2">
                                    <?php foreach ($product['images'] as $index => $image): ?>
                                        <div class="col-3">
                                            <img src="<?= htmlspecialchars($image['image_url']) ?>" 
                                                 class="img-thumbnail product-thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                                                 alt="<?= htmlspecialchars($image['alt_text'] ?? $product['name']) ?>"
                                                 data-image-url="<?= htmlspecialchars($image['image_url']) ?>"
                                                 onclick="changeMainImage(this)"
                                                 style="cursor: pointer; height: 80px; object-fit: cover; transition: all 0.3s ease;">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <img src="https://via.placeholder.com/500x500?text=<?= urlencode($product['name']) ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             style="max-height: 500px; object-fit: contain; padding: 20px;">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Information -->
            <div class="col-md-6">
                <?php if (!empty($product['brand_name'])): ?>
                    <p class="text-muted mb-2"><?= htmlspecialchars($product['brand_name']) ?></p>
                <?php endif; ?>
                
                <h1 class="display-5 mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <?php if ($product['is_featured']): ?>
                    <span class="badge bg-warning text-dark mb-2">Featured Product</span>
                <?php endif; ?>
                
                <?php if (!empty($product['category_name'])): ?>
                    <p class="mb-3">
                        <span class="badge bg-info fs-6">
                            <?= htmlspecialchars($product['category_name']) ?>
                        </span>
                    </p>
                <?php endif; ?>

                <div class="mb-4">
                    <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                        <p class="text-muted text-decoration-line-through mb-1">EGP <?= number_format($product['compare_at_price'], 2) ?></p>
                        <h2 class="text-danger mb-0">EGP <?= number_format($product['price'], 2) ?></h2>
                        <small class="text-success">Save EGP <?= number_format($product['compare_at_price'] - $product['price'], 2) ?></small>
                    <?php else: ?>
                        <h2 class="text-primary mb-0">EGP <?= number_format($product['price'], 2) ?></h2>
                    <?php endif; ?>
                </div>

                <?php if (!empty($product['short_description'])): ?>
                    <div class="mb-4">
                        <p class="lead"><?= htmlspecialchars($product['short_description']) ?></p>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <h5>Availability</h5>
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <p class="text-success">
                            <strong>In Stock</strong> (<?= $product['stock_quantity'] ?> available)
                        </p>
                    <?php else: ?>
                        <p class="text-danger">
                            <strong>Out of Stock</strong>
                        </p>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($product['warranty_period'])): ?>
                    <div class="mb-4">
                        <p><strong>Warranty:</strong> <?= $product['warranty_period'] ?> months</p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($product['description'])): ?>
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($product['specs_array'])): ?>
                    <div class="mb-4">
                        <h5>Specifications</h5>
                        <table class="table table-sm table-bordered">
                            <tbody>
                                <?php foreach ($product['specs_array'] as $key => $value): ?>
                                    <tr>
                                        <th style="width: 40%;"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?></th>
                                        <td><?= htmlspecialchars($value) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <?php if ($product['stock_quantity'] > 0): ?>
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

        <!-- Related Products Section -->
        <?php if (!empty($product['category_id'])): ?>
            <hr class="my-5">
            <div class="row">
                <div class="col-12">
                    <h3 class="mb-4">Related Products</h3>
                </div>
            </div>

            <?php
            // Fetch related products from the same category
            $relatedSql = "SELECT p.id, p.name, p.price, p.compare_at_price, b.name as brand_name,
                                  pi.image_url, pi.alt_text
                          FROM products p
                          LEFT JOIN brands b ON p.brand_id = b.id
                          LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                          WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
                          LIMIT 4";
            $relatedStmt = $conn->prepare($relatedSql);
            $relatedStmt->bind_param("ii", $product['category_id'], $productId);
            $relatedStmt->execute();
            $relatedResult = $relatedStmt->get_result();

            if ($relatedResult->num_rows > 0):
            ?>
                <div class="row">
                    <?php while($relatedProduct = $relatedResult->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                <img src="<?= !empty($relatedProduct['image_url']) ? htmlspecialchars($relatedProduct['image_url']) : 'https://via.placeholder.com/200x150?text=' . urlencode($relatedProduct['name']) ?>" 
                                     class="card-img-top" 
                                     alt="<?= !empty($relatedProduct['alt_text']) ? htmlspecialchars($relatedProduct['alt_text']) : htmlspecialchars($relatedProduct['name']) ?>"
                                     style="height: 150px; object-fit: cover;">
                                
                                <div class="card-body">
                                    <?php if (!empty($relatedProduct['brand_name'])): ?>
                                        <small class="text-muted"><?= htmlspecialchars($relatedProduct['brand_name']) ?></small>
                                    <?php endif; ?>
                                    <h6 class="card-title"><?= htmlspecialchars($relatedProduct['name']) ?></h6>
                                    <?php if (!empty($relatedProduct['compare_at_price']) && $relatedProduct['compare_at_price'] > $relatedProduct['price']): ?>
                                        <p class="mb-0">
                                            <small class="text-muted text-decoration-line-through">EGP <?= number_format($relatedProduct['compare_at_price'], 2) ?></small><br>
                                            <span class="text-danger">EGP <?= number_format($relatedProduct['price'], 2) ?></span>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-primary mb-0">EGP <?= number_format($relatedProduct['price'], 2) ?></p>
                                    <?php endif; ?>
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

<style>
.product-thumbnail {
    border: 3px solid transparent;
    opacity: 0.7;
}

.product-thumbnail:hover {
    opacity: 1;
    border-color: #0d6efd;
}

.product-thumbnail.active {
    opacity: 1;
    border-color: #0d6efd;
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
}
</style>

<script>
function changeMainImage(thumbnail) {
    // Get the main image element
    const mainImage = document.getElementById('mainProductImage');
    const newImageUrl = thumbnail.getAttribute('data-image-url');
    
    // Fade out effect
    mainImage.style.opacity = '0';
    
    // Change image after fade out
    setTimeout(() => {
        mainImage.src = newImageUrl;
        mainImage.alt = thumbnail.alt;
        
        // Fade in effect
        mainImage.style.opacity = '1';
    }, 150);
    
    // Update active state on thumbnails
    const allThumbnails = document.querySelectorAll('.product-thumbnail');
    allThumbnails.forEach(thumb => {
        thumb.classList.remove('active');
    });
    thumbnail.classList.add('active');
}
</script>

<?php
// Close database connection
$conn->close();

// Capture the content and store it in $pageContent
$pageContent = ob_get_clean();

// Include the template
include 'includes/template.php';
?>
