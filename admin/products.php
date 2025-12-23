<?php
define("SECURE_ACCESS", true);
session_start();
include "../includes/db_connect.php";

// Check if admin is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch products with category and brand names, and primary image
$query = "SELECT p.id, p.name, p.sku, p.price, p.compare_at_price, p.stock_quantity, p.status,
                 c.name as category_name, b.name as brand_name, p.short_description,
                 pi.image_url
          FROM products p
          LEFT JOIN categories c ON p.category_id = c.id
          LEFT JOIN brands b ON p.brand_id = b.id
          LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
          ORDER BY p.id DESC";
$result = mysqli_query($conn, $query);

$pageTitle = "Products Management";
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
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .badge-status {
            font-size: 0.75rem;
            padding: 4px 8px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
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
                    <a class="nav-link active" href="products.php">
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
                        <h2 class="mb-1">Products Management</h2>
                        <p class="text-muted mb-0">Manage your product inventory</p>
                    </div>
                    <a href="add.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Add New Product
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-muted mb-1">Total Products</p>
                                        <h3 class="mb-0"><?= mysqli_num_rows($result) ?></h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-box-seam fs-4 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Brand</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary">#<?= $row['id'] ?></span></td>
                                            <td>
                                                <?php if(!empty($row['image_url'])): ?>
                                                    <img src="<?= htmlspecialchars($row['image_url']) ?>" 
                                                         alt="<?= htmlspecialchars($row['name']) ?>" 
                                                         class="product-img">
                                                <?php else: ?>
                                                    <div class="product-img bg-light d-flex align-items-center justify-content-center">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= htmlspecialchars($row['name']) ?></div>
                                                <small class="text-muted">SKU: <?= htmlspecialchars($row['sku']) ?></small>
                                            </td>
                                            <td>
                                                <?php if(!empty($row['category_name'])): ?>
                                                    <span class="badge bg-info"><?= htmlspecialchars($row['category_name']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= !empty($row['brand_name']) ? htmlspecialchars($row['brand_name']) : '-' ?></td>
                                            <td>
                                                <div class="fw-bold">EGP <?= number_format($row['price'], 2) ?></div>
                                                <?php if(!empty($row['compare_at_price']) && $row['compare_at_price'] > $row['price']): ?>
                                                    <small class="text-muted text-decoration-line-through">EGP <?= number_format($row['compare_at_price'], 2) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['stock_quantity'] > 10): ?>
                                                    <span class="badge bg-success"><?= $row['stock_quantity'] ?> in stock</span>
                                                <?php elseif($row['stock_quantity'] > 0): ?>
                                                    <span class="badge bg-warning"><?= $row['stock_quantity'] ?> left</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Out of stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($row['status'] === 'active'): ?>
                                                    <span class="badge bg-success badge-status">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary badge-status">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger" title="Delete" 
                                                       onclick="return confirm('Are you sure you want to delete this product?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                                <p class="text-muted">No products found</p>
                                                <a href="add.php" class="btn btn-primary">Add Your First Product</a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
