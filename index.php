<?php
// Define secure access constant
define("SECURE_ACCESS", true);

// Include the database connection file
include 'includes/db_connect.php';

// Set page title
$pageTitle = "Home - Online Phones Store";

// Start output buffering to capture the page content
ob_start();
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4">Welcome to our store</h1>
            <p class="lead">Browse our latest collection </p>
        </div>
    </div>

    <?php
    // Get the selected category from URL parameter (if any)
    $selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
    
    // Fetch all unique categories for the filter menu
    $categorySql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category";
    $categoryResult = $conn->query($categorySql);
    ?>

    <!-- Category Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="btn-group" role="group" aria-label="Category Filter">
                <a href="index.php" class="btn <?= empty($selectedCategory) ? 'btn-primary' : 'btn-outline-primary' ?>">
                    All Products
                </a>
                <?php
                if ($categoryResult && $categoryResult->num_rows > 0) {
                    while($catRow = $categoryResult->fetch_assoc()) {
                        $category = htmlspecialchars($catRow['category']);
                        $isActive = ($selectedCategory === $catRow['category']) ? 'btn-primary' : 'btn-outline-primary';
                        echo '<a href="index.php?category=' . urlencode($catRow['category']) . '" class="btn ' . $isActive . '">' . $category . '</a>';
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <?php
    // Build SQL query based on category filter
    if (!empty($selectedCategory)) {
        $sql = "SELECT id, name, description, price, image_url, category FROM products WHERE category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selectedCategory);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        // SQL query to select all products
        $sql = "SELECT id, name, description, price, image_url, category FROM products";
        $result = $conn->query($sql);
    }

    // Check if any products were found
    if ($result && $result->num_rows > 0) {
        echo '<div class="row">';
        
        // Loop through each product
        while($row = $result->fetch_assoc()) {
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($row["image_url"])): ?>
                        <img class="card-img-top" 
                             src="<?= htmlspecialchars($row["image_url"]) ?>" 
                             alt="<?= htmlspecialchars($row["name"]) ?>"
                             style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <span class="text-white">No Image</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($row["name"]) ?></h5>
                        
                        <?php if (!empty($row["category"])): ?>
                            <span class="badge bg-info mb-2"><?= htmlspecialchars($row["category"]) ?></span>
                        <?php endif; ?>
                        
                        <h6 class="text-primary mb-2">$<?= number_format($row["price"], 2) ?></h6>
                        
                        <p class="card-text text-muted flex-grow-1">
                            <?= substr(htmlspecialchars($row["description"]), 0, 80) ?>...
                        </p>
                    </div>
                    
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="product_detail.php?id=<?= $row["id"] ?>" 
                           class="btn btn-primary btn-sm w-100 mb-2">
                            View Details
                        </a>
                        <button class="btn btn-success btn-sm w-100 add-to-cart" 
                                data-id="<?= $row["id"] ?>">
                            Add to Cart
                        </button>
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
