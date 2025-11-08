<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Models/Product.php';
require_once __DIR__ . '/../../src/Models/Category.php';
require_once __DIR__ . '/../../src/Models/Order.php';
require_once __DIR__ . '/../../src/Models/User.php';
require_once __DIR__ . '/../../src/Utils/Helper.php';
require_once __DIR__ . '/../../src/Utils/Auth.php';

use Src\Models\Product;
use Src\Models\Category;
use Src\Models\Order;
use Src\Models\User;
use Src\Utils\Helper;
use Src\Utils\AdminAuth;

// Check admin authentication
AdminAuth::redirectIfNotAuthenticated();

$productModel = new Product();
$categoryModel = new Category();
$orderModel = new Order();
$userModel = new User();

// Get statistics
$totalProducts = $productModel->count();
$totalCategories = $categoryModel->count();
$totalOrders = $orderModel->count();
$totalUsers = $userModel->count();

// Get recent orders
$recentOrders = $orderModel->getAllOrders();
$recentOrders = array_slice($recentOrders, 0, 5);

// Get low stock products
$allProducts = $productModel->findAll();
$lowStockProducts = array_filter($allProducts, function($product) {
    return $product['stock_count'] <= 5 && $product['stock_count'] > 0;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PHP E-commerce</title>
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
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
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

    <!-- Dashboard Content -->
    <div class="container py-4">
        <h1>Dashboard</h1>
        
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Total Products</h6>
                                <h3><?= $totalProducts ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Total Categories</h6>
                                <h3><?= $totalCategories ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-tags fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Total Orders</h6>
                                <h3><?= $totalOrders ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>Total Users</h6>
                                <h3><?= $totalUsers ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($recentOrders)): ?>
                            <p class="text-muted">No orders yet</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td><?= $order['id'] ?></td>
                                                <td><?= htmlspecialchars($order['user_name']) ?></td>
                                                <td><?= Helper::formatPrice($order['total_amount']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'shipped' ? 'info' : 'warning') ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="orders.php" class="btn btn-primary btn-sm">View All Orders</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Low Stock Products -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Low Stock Alert</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($lowStockProducts)): ?>
                            <p class="text-success">All products have sufficient stock</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lowStockProducts as $product): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($product['name']) ?></td>
                                                <td>
                                                    <span class="badge bg-<?= $product['stock_count'] == 0 ? 'danger' : 'warning' ?>">
                                                        <?= $product['stock_count'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="products.php?edit=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <a href="products.php" class="btn btn-primary btn-sm">Manage Products</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>