<?php
// Define secure access constant
define("SECURE_ACCESS", true);

session_start();

// Include the database connection file
include 'includes/db_connect.php';

// Set page title
$pageTitle = "Home - TechHub Electronics";

// Start output buffering to capture the page content
ob_start();
?>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-4">Welcome to TechHub Electronics</h1>
                <p class="lead">Your One-Stop Shop for All Electronics - Phones, Laptops, Gaming & More</p>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <strong>👋 Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</strong> 
        Happy shopping at TechHub Electronics.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php
    // Get the selected category from URL parameter (if any)
    $selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
    
    // Fetch only parent categories for the filter menu (no subcategories)
    $categorySql = "SELECT id, name, slug FROM categories WHERE is_active = 1 AND parent_id IS NULL ORDER BY display_order, name";
    $categoryResult = $conn->query($categorySql);
    ?>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-12">
                <label class="form-label"><i class="bi bi-grid-3x3-gap-fill me-2"></i>Shop by Category</label>
                <div class="d-flex flex-wrap gap-2" role="group" aria-label="Category Filter">
                <a href="index.php" class="btn btn-sm <?= empty($selectedCategory) ? 'btn-primary' : 'btn-outline-primary' ?>">
                    <i class="bi bi-grid-fill me-1"></i>All Categories
                </a>
                <?php
                if ($categoryResult && $categoryResult->num_rows > 0) {
                    while($catRow = $categoryResult->fetch_assoc()) {
                        $category = htmlspecialchars($catRow['name']);
                        $isActive = ($selectedCategory === $catRow['id']) ? 'btn-primary' : 'btn-outline-primary';
                        $url = 'index.php?category=' . $catRow['id'];
                        
                        // Map categories to Bootstrap icons
                        $iconMap = [
                            'smartphones' => 'phone-fill',
                            'laptops&computers' => 'laptop-fill',
                            'tablets' => 'tablet-fill',
                            'headphones&speakers' => 'headphones',
                            'smartwatches&wearables' => 'smartwatch',
                            'cameras&photography' => 'camera-fill',
                            'gaming' => 'controller',
                            'accessories' => 'usb-plug-fill'
                        ];
                        
                        $categorySlug = strtolower(str_replace(['&', ' '], '', $catRow['slug']));
                        $icon = isset($iconMap[$categorySlug]) ? $iconMap[$categorySlug] : 'tag-fill';
                        
                        echo '<a href="' . $url . '" class="btn btn-sm ' . $isActive . '" data-category="' . $categorySlug . '"><i class="bi bi-' . $icon . ' me-1"></i>' . $category . '</a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    // Build SQL query based on filters
    $sql = "SELECT p.id, p.name, p.slug, p.short_description, p.price, p.compare_at_price, 
                   p.stock_quantity, p.is_featured, b.name as brand_name, c.name as category_name,
                   pi.image_url, pi.alt_text
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.status = 'active'";
    
    $params = [];
    $types = '';
    
    if (!empty($selectedCategory)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $selectedCategory;
        $types .= 'i';
    }
    
    $sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }

    // Check if any products were found
    if ($result && $result->num_rows > 0) {
        $productCount = $result->num_rows;
        echo '<div class="row">';
        
        // Loop through each product
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm position-relative">
                    <?php if ($row["is_featured"]): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Featured</span>
                    <?php endif; ?>
                    
                    <?php if (!empty($row["compare_at_price"]) && $row["compare_at_price"] > $row["price"]): ?>
                        <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sale</span>
                    <?php endif; ?>
                    
                    <img class="card-img-top" 
                         src="<?= !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'https://via.placeholder.com/300x200?text=' . urlencode($row['name']) ?>" 
                         alt="<?= !empty($row['alt_text']) ? htmlspecialchars($row['alt_text']) : htmlspecialchars($row['name']) ?>"
                         style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="flex-grow-1">
                                <?php if (!empty($row["brand_name"])): ?>
                                    <small class="text-muted mb-1 d-block"><?= htmlspecialchars($row["brand_name"]) ?></small>
                                <?php endif; ?>
                                <h5 class="card-title mb-0"><?= htmlspecialchars($row["name"]) ?></h5>
                            </div>
                            <button class="btn btn-link p-0 wishlist-btn" 
                                    data-product-id="<?= $row["id"] ?>"
                                    style="margin-left: 0.5rem; text-decoration: none; box-shadow: none !important; outline: none !important;">
                                <i class="bi bi-heart" style="font-size: 1.5rem; color: #dc3545;"></i>
                            </button>
                        </div>
                        
                        <?php if (!empty($row["category_name"])): ?>
                            <span class="badge bg-info mb-2"><?= htmlspecialchars($row["category_name"]) ?></span>
                        <?php endif; ?>
                        
                        <div class="mb-2">
                            <?php if (!empty($row["compare_at_price"]) && $row["compare_at_price"] > $row["price"]): ?>
                                <span class="text-muted text-decoration-line-through small">EGP <?= number_format($row["compare_at_price"], 2) ?></span>
                                <h6 class="text-danger mb-0">EGP <?= number_format($row["price"], 2) ?></h6>
                            <?php else: ?>
                                <h6 class="text-primary mb-0">EGP <?= number_format($row["price"], 2) ?></h6>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($row["short_description"])): ?>
                            <p class="card-text text-muted flex-grow-1 small">
                                <?= htmlspecialchars($row["short_description"]) ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if ($row["stock_quantity"] <= 0): ?>
                            <span class="badge bg-secondary">Out of Stock</span>
                        <?php elseif ($row["stock_quantity"] < 10): ?>
                            <span class="badge bg-warning text-dark">Only <?= $row["stock_quantity"] ?> left</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="product_detail.php?id=<?= $row["id"] ?>" 
                           class="btn btn-primary btn-sm w-100 mb-2">
                            View Details
                        </a>
                        <?php if ($row["stock_quantity"] > 0): ?>
                            <button class="btn btn-success btn-sm w-100 add-to-cart" 
                                    data-id="<?= $row["id"] ?>">
                                Add to Cart
                            </button>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php
        }
        
        echo '</div>';
    } else {
        ?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <h4 class="alert-heading">No Products Found</h4>
                    <p>There are currently no products in our catalog. Please check back later!</p>
                </div>
            </div>
        </div>
        <?php
    }

    // Close the database connection
    $conn->close();
    ?>
</div>

<?php
// Capture the content and store it in $pageContent
$pageContent = ob_get_clean();

// Include the template
include 'includes/template.php';
?>
