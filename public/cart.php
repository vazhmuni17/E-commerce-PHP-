<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/CartItem.php';
require_once __DIR__ . '/../src/Utils/Helper.php';

use Src\Models\CartItem;
use Src\Utils\Helper;

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$cartModel = new CartItem();
$cartItems = $cartModel->getUserCart($userId);
$cartTotal = $cartModel->getCartTotal($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - PHP E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 1rem 0;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Cart Content -->
    <div class="container py-5">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-5x text-muted mb-3"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted">Add some products to get started!</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="cart-item row align-items-center">
                                    <div class="col-md-2">
                                        <img src="https://via.placeholder.com/80x80?text=<?= urlencode($item['name']) ?>" 
                                             class="product-image" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h6><?= htmlspecialchars($item['name']) ?></h6>
                                        <p class="text-muted small mb-0">
                                            <?php if ($item['stock_count'] == 0): ?>
                                                <span class="text-danger">Out of Stock</span>
                                            <?php else: ?>
                                                Stock: <?= $item['stock_count'] ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="quantity-control">
                                            <button class="btn btn-sm btn-outline-secondary quantity-btn" 
                                                    onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <span class="quantity"><?= $item['quantity'] ?></span>
                                            <button class="btn btn-sm btn-outline-secondary quantity-btn" 
                                                    onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)"
                                                    <?= $item['quantity'] >= $item['stock_count'] ? 'disabled' : '' ?>>
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><?= Helper::formatPrice($item['price'] * $item['quantity']) ?></strong>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-sm btn-danger" onclick="removeFromCart(<?= $item['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal (<?= count($cartItems) ?> items)</span>
                                <span><?= Helper::formatPrice($cartTotal) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping</span>
                                <span class="text-success">Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong><?= Helper::formatPrice($cartTotal) ?></strong>
                            </div>
                            <a href="checkout.php" class="btn btn-primary w-100">Proceed to Checkout</a>
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
        function updateQuantity(cartItemId, quantity) {
            if (quantity < 1) return;
            
            fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'update',
                    cart_item_id: cartItemId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update quantity');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function removeFromCart(cartItemId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            fetch('api/cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'remove',
                    cart_item_id: cartItemId
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