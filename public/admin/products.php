<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Models/Product.php';
require_once __DIR__ . '/../../src/Models/Category.php';
require_once __DIR__ . '/../../src/Utils/Helper.php';
require_once __DIR__ . '/../../src/Utils/Auth.php';

use Src\Models\Product;
use Src\Models\Category;
use Src\Utils\Helper;
use Src\Utils\AdminAuth;

// Check admin authentication
AdminAuth::redirectIfNotAuthenticated();

$productModel = new Product();
$categoryModel = new Category();

// Handle form submissions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock_count' => $_POST['stock_count'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'image' => $_POST['image'] ?? ''
            ];
            
            if ($data['name'] && $data['price'] >= 0 && $data['stock_count'] >= 0) {
                $productModel->create($data);
                $message = 'Product added successfully!';
            } else {
                $error = 'Please fill in all required fields correctly';
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? 0,
                'stock_count' => $_POST['stock_count'] ?? 0,
                'category_id' => $_POST['category_id'] ?? null,
                'image' => $_POST['image'] ?? ''
            ];
            
            if ($id && $data['name'] && $data['price'] >= 0 && $data['stock_count'] >= 0) {
                $productModel->update($id, $data);
                $message = 'Product updated successfully!';
            } else {
                $error = 'Please fill in all required fields correctly';
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $productModel->delete($id);
                $message = 'Product deleted successfully!';
            }
            break;
    }
}

// Get all products and categories
$products = $productModel->findAll();
$categories = $categoryModel->findAll();

// Check if we're editing a product
$editProduct = null;
if (isset($_GET['edit'])) {
    $editProduct = $productModel->find($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin - PHP E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['admin_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><?= $editProduct ? 'Edit Product' : 'Add New Product' ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editProduct ? 'edit' : 'add' ?>">
                            <?php if ($editProduct): ?>
                                <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= $editProduct['name'] ?? '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= $editProduct['description'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" 
                                       value="<?= $editProduct['price'] ?? '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="stock_count" class="form-label">Stock Count</label>
                                <input type="number" class="form-control" id="stock_count" name="stock_count" 
                                       value="<?= $editProduct['stock_count'] ?? '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" 
                                                <?= ($editProduct['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Image URL</label>
                                <input type="text" class="form-control" id="image" name="image" 
                                       value="<?= $editProduct['image'] ?? '' ?>">
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editProduct ? 'Update' : 'Add' ?> Product
                                </button>
                                <?php if ($editProduct): ?>
                                    <a href="products.php" class="btn btn-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>All Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Category</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product['id'] ?></td>
                                            <td><?= htmlspecialchars($product['name']) ?></td>
                                            <td><?= Helper::formatPrice($product['price']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= $product['stock_count'] == 0 ? 'danger' : ($product['stock_count'] <= 5 ? 'warning' : 'success') ?>">
                                                    <?= $product['stock_count'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $category = $categoryModel->find($product['category_id']);
                                                echo $category ? htmlspecialchars($category['name']) : 'None';
                                                ?>
                                            </td>
                                            <td>
                                                <a href="products.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>