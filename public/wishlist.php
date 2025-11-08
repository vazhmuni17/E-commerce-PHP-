<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Wishlist.php';
require_once __DIR__ . '/../src/Utils/Helper.php';

use Src\Models\Wishlist;
use Src\Utils\Helper;

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$wishlistModel = new Wishlist();
$wishlistItems = $wishlistModel->getUserWishlist($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - PHP E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .wishlist-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .wishlist-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .out-of-stock {
            opacity: 0.6;
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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Wishlist Content -->
    <div class="container py-5">
        <h1 class="mb-4">My Wishlist</h1>
        
        <?php if (empty($wishlistItems)): ?>
            <div class="text-center py-5">
                <i class="fas fa-heart fa-5x text-muted mb-3"></i>
                <h3>Your wishlist is empty</h3>
                <p class="text-muted">Save products you love for later!</p>
                <a href="index.php" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($wishlistItems as $item): ?>
                                <div class="wishlist-item row align-items-center <?= $item['stock_count'] == 0 ? 'out-of-stock' : '' ?>">
                                    <div class="col-md-2">
                                        <img src="https://via.placeholder.com/80x80?text=<?= urlencode($item['name']) ?>" 
                                             class="product-image" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <h6><?= htmlspecialchars($item['name']) ?></h6>
                                        <p class="text-muted small mb-0">
                                            <?php if ($item['stock_count'] == 0): ?>
                                                <span class="text-danger">Out of Stock</span>
                                            <?php else: ?>
                                                Stock: <?= $item['stock_count'] ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><?= Helper::formatPrice($item['price']) ?></strong>
                                    </div>
                                    <div class="col-md-3">
                                        <?php if ($item['stock_count'] > 0): ?>
                                            <button class="btn btn-sm btn-primary me-1" onclick="addToCartFromWishlist(<?= $item['product_id'] ?>)" title="Add to Cart">
                                                <i class="fas fa-cart-plus"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-danger" onclick="removeFromWishlist(<?= $item['id'] ?>)" title="Remove">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 PHP E-commerce. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addToCartFromWishlist(productId) {
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

        function removeFromWishlist(wishlistId) {
            if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                return;
            }
            
            fetch('api/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    wishlist_id: wishlistId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove item');
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