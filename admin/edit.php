<?php
define("SECURE_ACCESS", true);
session_start();
include "../includes/db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: products.php");
    exit();
}

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: products.php");
    exit();
}

// Fetch categories for dropdown
$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");

// Fetch brands for dropdown
$brands = mysqli_query($conn, "SELECT id, name FROM brands ORDER BY name");

// Fetch existing product images
$images_query = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = $id ORDER BY is_primary DESC, display_order ASC");
$product_images = [];
while ($img = mysqli_fetch_assoc($images_query)) {
    $product_images[] = $img;
}

$success = '';
$errors = [];

// Handle image deletion
if (isset($_POST['delete_image'])) {
    $image_id = (int)$_POST['delete_image'];
    mysqli_query($conn, "DELETE FROM product_images WHERE id = $image_id AND product_id = $id");
    $success = "Image deleted successfully!";
    header("Location: edit.php?id=$id");
    exit();
}

// Handle set primary image
if (isset($_POST['set_primary'])) {
    $image_id = (int)$_POST['set_primary'];
    mysqli_query($conn, "UPDATE product_images SET is_primary = 0 WHERE product_id = $id");
    mysqli_query($conn, "UPDATE product_images SET is_primary = 1 WHERE id = $image_id AND product_id = $id");
    $success = "Primary image updated!";
    header("Location: edit.php?id=$id");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete_image']) && !isset($_POST['set_primary'])) {
    $name = trim($_POST["name"]);
    $sku = trim($_POST["sku"]);
    $price = (float)$_POST["price"];
    $compare_at_price = !empty($_POST["compare_at_price"]) ? (float)$_POST["compare_at_price"] : null;
    $short_desc = trim($_POST["short_description"]);
    $description = trim($_POST["description"]);
    $category_id = !empty($_POST["category_id"]) ? (int)$_POST["category_id"] : null;
    $brand_id = !empty($_POST["brand_id"]) ? (int)$_POST["brand_id"] : null;
    $stock_quantity = (int)$_POST["stock_quantity"];
    $status = $_POST["status"];

    if (empty($name)) {
        $errors[] = "Product name is required";
    }
    if (empty($sku)) {
        $errors[] = "SKU is required";
    }
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }

    // Handle image uploads
    if (!empty($_FILES['product_images']['name'][0])) {
        $upload_dir = "../uploads/products/";
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB

        foreach ($_FILES['product_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['product_images']['error'][$key] == 0) {
                $file_size = $_FILES['product_images']['size'][$key];
                $file_type = $_FILES['product_images']['type'][$key];
                $file_name = $_FILES['product_images']['name'][$key];

                if (!in_array($file_type, $allowed_types)) {
                    $errors[] = "Invalid file type for $file_name. Only JPG, PNG, GIF, and WebP allowed.";
                    continue;
                }

                if ($file_size > $max_size) {
                    $errors[] = "File $file_name is too large. Maximum size is 5MB.";
                    continue;
                }

                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $new_filename = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $image_url = "/Online_Phones_Store/uploads/products/" . $new_filename;
                    $alt_text = $name . " - Image";
                    $is_primary = empty($product_images) ? 1 : 0;
                    $display_order = count($product_images) + $key;

                    $stmt_img = $conn->prepare("INSERT INTO product_images (product_id, image_url, alt_text, display_order, is_primary) VALUES (?, ?, ?, ?, ?)");
                    $stmt_img->bind_param("issii", $id, $image_url, $alt_text, $display_order, $is_primary);
                    $stmt_img->execute();
                    $stmt_img->close();
                } else {
                    $errors[] = "Failed to upload $file_name";
                }
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare(
            "UPDATE products SET name=?, sku=?, price=?, compare_at_price=?, short_description=?, 
             description=?, category_id=?, brand_id=?, stock_quantity=?, status=? WHERE id=?"
        );
        $stmt->bind_param("ssddssiissi", $name, $sku, $price, $compare_at_price, $short_desc, 
                          $description, $category_id, $brand_id, $stock_quantity, $status, $id);
        
        if ($stmt->execute()) {
            $success = "Product updated successfully!";
            // Refresh product data
            $stmt2 = $conn->prepare("SELECT * FROM products WHERE id = ?");
            $stmt2->bind_param("i", $id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $product = $result2->fetch_assoc();
            $stmt2->close();
            
            // Refresh images
            $images_query = mysqli_query($conn, "SELECT * FROM product_images WHERE product_id = $id ORDER BY is_primary DESC, display_order ASC");
            $product_images = [];
            while ($img = mysqli_fetch_assoc($images_query)) {
                $product_images[] = $img;
            }
        } else {
            $errors[] = "Failed to update product: " . $conn->error;
        }
        $stmt->close();
    }
}

$pageTitle = "Edit Product";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="mb-4"><i class="bi bi-shop"></i> Admin Panel</h4>
                    <div class="mb-4">
                        <small class="text-white-50">Logged in as</small>
                        <div class="fw-bold"><?= htmlspecialchars($_SESSION['admin_username']) ?></div>
                    </div>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link" href="products.php">
                        <i class="bi bi-box-seam me-2"></i> Products
                    </a>
                    <a class="nav-link" href="add.php">
                        <i class="bi bi-plus-circle me-2"></i> Add Product
                    </a>
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="bi bi-globe me-2"></i> View Store
                    </a>
                    <hr class="text-white-50">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Edit Product</h2>
                        <p class="text-muted mb-0">Update product information</p>
                    </div>
                    <a href="products.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Products
                    </a>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Product Images Section -->
                <?php if (!empty($product_images)): ?>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-images me-2"></i>Product Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($product_images as $img): ?>
                            <div class="col-md-3">
                                <div class="card">
                                    <img src="<?= htmlspecialchars($img['image_url']) ?>" class="card-img-top" alt="Product Image" style="height: 200px; object-fit: cover;">
                                    <div class="card-body p-2">
                                        <?php if ($img['is_primary']): ?>
                                            <span class="badge bg-success w-100 mb-2">Primary Image</span>
                                        <?php else: ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="set_primary" value="<?= $img['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-primary w-100 mb-2">Set as Primary</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this image?')">
                                            <input type="hidden" name="delete_image" value="<?= $img['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Edit Form -->
                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <!-- Product Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($product['name']) ?>" required>
                                </div>

                                <!-- SKU -->
                                <div class="col-md-6 mb-3">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <input type="text" class="form-control" id="sku" name="sku" 
                                           value="<?= htmlspecialchars($product['sku']) ?>" required>
                                </div>

                                <!-- Price -->
                                <div class="col-md-4 mb-3">
                                    <label for="price" class="form-label">Price (EGP) *</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                           value="<?= $product['price'] ?>" required>
                                </div>

                                <!-- Compare at Price -->
                                <div class="col-md-4 mb-3">
                                    <label for="compare_at_price" class="form-label">Compare at Price (EGP)</label>
                                    <input type="number" step="0.01" class="form-control" id="compare_at_price" name="compare_at_price" 
                                           value="<?= $product['compare_at_price'] ?>">
                                    <small class="text-muted">Original price for showing discounts</small>
                                </div>

                                <!-- Stock -->
                                <div class="col-md-4 mb-3">
                                    <label for="stock_quantity" class="form-label">Stock Quantity *</label>
                                    <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" 
                                           value="<?= $product['stock_quantity'] ?>" required>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select" id="category_id" name="category_id">
                                        <option value="">-- Select Category --</option>
                                        <?php 
                                        mysqli_data_seek($categories, 0);
                                        while ($cat = mysqli_fetch_assoc($categories)): 
                                        ?>
                                            <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Brand -->
                                <div class="col-md-6 mb-3">
                                    <label for="brand_id" class="form-label">Brand</label>
                                    <select class="form-select" id="brand_id" name="brand_id">
                                        <option value="">-- Select Brand --</option>
                                        <?php 
                                        mysqli_data_seek($brands, 0);
                                        while ($brand = mysqli_fetch_assoc($brands)): 
                                        ?>
                                            <option value="<?= $brand['id'] ?>" <?= $product['brand_id'] == $brand['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($brand['name']) ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-12 mb-3">
                                    <label for="status" class="form-label">Status *</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?= $product['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="inactive" <?= $product['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                        <option value="draft" <?= $product['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                    </select>
                                </div>

                                <!-- Short Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="short_description" class="form-label">Short Description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="2"><?= htmlspecialchars($product['short_description']) ?></textarea>
                                    <small class="text-muted">Brief description for product listings</small>
                                </div>

                                <!-- Full Description -->
                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">Full Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"><?= htmlspecialchars($product['description']) ?></textarea>
                                </div>

                                <!-- Product Images Upload -->
                                <div class="col-md-12 mb-3">
                                    <label for="product_images" class="form-label">
                                        <i class="bi bi-cloud-upload me-2"></i>Upload Product Images
                                    </label>
                                    <input type="file" class="form-control" id="product_images" name="product_images[]" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" multiple>
                                    <small class="text-muted">
                                        You can select multiple images. Allowed formats: JPG, PNG, GIF, WebP. Max size: 5MB per image.
                                        <?php if (empty($product_images)): ?>
                                            <br><strong>The first image uploaded will be set as primary.</strong>
                                        <?php endif; ?>
                                    </small>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="col-md-12">
                                    <hr class="my-4">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg me-2"></i>Update Product
                                        </button>
                                        <a href="products.php" class="btn btn-outline-secondary">
                                            <i class="bi bi-x-lg me-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
