<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Product.php';
require_once __DIR__ . '/../src/Utils/Helper.php';

use Src\Models\Product;
use Src\Utils\Helper;

$productId = $_GET['id'] ?? 0;
if (!$productId) {
    header('Location: index.php');
    exit();
}

$productModel = new Product();
$product = $productModel->find($productId);

if (!$product) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - PHP E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .price {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
        .stock-info {
            font-size: 1.1rem;
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
                    <?php if (isset($_SESSION['user_id'])): ?>
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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Product Detail -->
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6">
                <img src="https://via.placeholder.com/500x500?text=<?= urlencode($product['name']) ?>" 
                     class="product-image" alt="<?= htmlspecialchars($product['name']) ?>">
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <div class="price mb-3"><?= Helper::formatPrice($product['price']) ?></div>
                
                <div class="stock-info mb-3">
                    <?php if ($product['stock_count'] > 0): ?>
                        <span class="badge bg-success">In Stock (<?= $product['stock_count'] ?>)</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <div class="description mb-4">
                    <h5>Description</h5>
                    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                </div>

                <div class="actions">
                    <?php if ($product['stock_count'] > 0): ?>
                        <button class="btn btn-primary btn-lg me-2" onclick="addToCart(<?= $product['id'] ?>)">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <button class="btn btn-outline-danger btn-lg" onclick="addToWishlist(<?= $product['id'] ?>)">
                            <i class="fas fa-heart"></i> Add to Wishlist
                        </button>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="fas fa-ban"></i> Out of Stock
                        </button>
                    <?php endif; ?>
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

        function addToWishlist(productId) {
            fetch('api/wishlist.php', {
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
                    alert('Product added to wishlist!');
                } else {
                    alert(data.message || 'Failed to add to wishlist');
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