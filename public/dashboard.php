<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Order.php';
require_once __DIR__ . '/../src/Models/Wishlist.php';
require_once __DIR__ . '/../src/Models/Address.php';
require_once __DIR__ . '/../src/Utils/Helper.php';

use Src\Models\User;
use Src\Models\Order;
use Src\Models\Wishlist;
use Src\Models\Address;
use Src\Utils\Helper;

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$userModel = new User();
$orderModel = new Order();
$wishlistModel = new Wishlist();
$addressModel = new Address();

$user = $userModel->find($userId);
$orders = $orderModel->getUserOrders($userId);
$wishlistItems = $wishlistModel->getUserWishlist($userId);
$addresses = $addressModel->where('user_id', '=', $userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PHP E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">PHP E-commerce</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                                <span class="badge bg-danger"><?= $_SESSION['cart_count'] ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="wishlist.php">
                            <i class="fas fa-heart"></i> Wishlist
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
                <p class="text-muted">Manage your account and view your activity</p>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <i class="fas fa-shopping-cart fa-3x text-primary mb-3"></i>
                        <h5>Orders</h5>
                        <p class="text-muted"><?= count($orders) ?> total orders</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                        <h5>Wishlist</h5>
                        <p class="text-muted"><?= count($wishlistItems) ?> saved items</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <i class="fas fa-map-marker-alt fa-3x text-success mb-3"></i>
                        <h5>Addresses</h5>
                        <p class="text-muted"><?= count($addresses) ?> saved addresses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card text-center">
                    <div class="card-body">
                        <i class="fas fa-user fa-3x text-info mb-3"></i>
                        <h5>Profile</h5>
                        <p class="text-muted">Manage your account</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Recent Orders</h5>
                        <a href="orders.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <p class="text-muted">No orders yet</p>
                            <a href="index.php" class="btn btn-primary">Start Shopping</a>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($orders, 0, 5) as $order): ?>
                                            <tr>
                                                <td>#<?= $order['id'] ?></td>
                                                <td><?= Helper::formatDate($order['created_at']) ?></td>
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
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Recent Wishlist Items -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Wishlist Items</h5>
                        <a href="wishlist.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($wishlistItems)): ?>
                            <p class="text-muted">No items in wishlist</p>
                            <a href="index.php" class="btn btn-primary">Browse Products</a>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach (array_slice($wishlistItems, 0, 3) as $item): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card">
                                            <img src="https://via.placeholder.com/150x100?text=<?= urlencode($item['name']) ?>" 
                                                 class="card-img-top" alt="<?= htmlspecialchars($item['name']) ?>">
                                            <div class="card-body">
                                                <h6 class="card-title small"><?= htmlspecialchars($item['name']) ?></h6>
                                                <p class="card-text small"><?= Helper::formatPrice($item['price']) ?></p>
                                                <?php if ($item['stock_count'] > 0): ?>
                                                    <button class="btn btn-sm btn-primary" onclick="addToCart(<?= $item['product_id'] ?>)">
                                                        Add to Cart
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
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

        <!-- Account Settings -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Profile Details</h6>
                                <p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
                                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                <p><strong>Member Since:</strong> <?= Helper::formatDate($user['created_at']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Quick Actions</h6>
                                <div class="d-flex flex-column gap-2">
                                    <a href="addresses.php" class="btn btn-outline-primary">Manage Addresses</a>
                                    <a href="orders.php" class="btn btn-outline-primary">View Order History</a>
                                    <a href="wishlist.php" class="btn btn-outline-primary">View Wishlist</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 PHP E-commerce. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCart(productId) {
            fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to add to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    </script>
</body>
</html>