<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/Product.php';
require_once __DIR__ . '/../src/Models/Category.php';
require_once __DIR__ . '/../src/Utils/Helper.php';

use Src\Models\Product;
use Src\Models\Category;
use Src\Utils\Helper;

$productModel = new Product();
$categoryModel = new Category();

// Handle search and sort
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'newest';
$categoryId = $_GET['category'] ?? '';

// Get products based on filters
if ($search) {
    $products = $productModel->search($search);
} elseif ($categoryId) {
    $products = $productModel->getByCategory($categoryId);
} else {
    $products = $productModel->findAll();
}

// Apply sorting
if ($sort === 'price_low') {
    usort($products, function($a, $b) {
        return $a['price'] - $b['price'];
    });
} elseif ($sort === 'price_high') {
    usort($products, function($a, $b) {
        return $b['price'] - $a['price'];
    });
} elseif ($sort === 'newest') {
    usort($products, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

$categories = $categoryModel->findAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP E-commerce Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .product-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #28a745;
        }
        .out-of-stock {
            opacity: 0.6;
        }
        .out-of-stock .btn {
            pointer-events: none;
            opacity: 0.5;
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="index.php">All Categories</a></li>
                            <?php foreach ($categories as $category): ?>
                                <li><a class="dropdown-item" href="index.php?category=<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
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

    <!-- Hero Section -->
    <div class="bg-light py-5 mb-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4">Welcome to PHP E-commerce</h1>
                    <p class="lead">Discover amazing products at great prices!</p>
                </div>
                <div class="col-md-6">
                    <img src="https://via.placeholder.com/500x300?text=Welcome+to+Our+Store" class="img-fluid rounded" alt="Welcome">
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="container mb-4">
        <div class="row">
            <div class="col-md-8">
                <form method="GET" class="d-flex">
                    <input type="text" name="search" class="form-control me-2" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                    <button class="btn btn-outline-primary" type="submit">Search</button>
                </form>
            </div>
            <div class="col-md-4">
                <select name="sort" class="form-select" onchange="window.location.href='?search=<?= urlencode($search) ?>&category=<?= urlencode($categoryId) ?>&sort=' + this.value">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                    <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="container">
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <h3>No products found</h3>
                    <p>Try adjusting your search or filters.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-lg-3 mb-4">
                        <div class="card product-card <?= $product['stock_count'] == 0 ? 'out-of-stock' : '' ?>">
                            <img src="https://via.placeholder.com/300x200?text=<?= urlencode($product['name']) ?>" 
                                 class="card-img-top product-image" alt="<?= htmlspecialchars($product['name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted small">
                                    <?= htmlspecialchars(Helper::truncateText($product['description'], 50)) ?>
                                </p>
                                <p class="price"><?= Helper::formatPrice($product['price']) ?></p>
                                <p class="small text-muted">Stock: <?= $product['stock_count'] ?></p>
                                <div class="mt-auto">
                                    <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">View Details</a>
                                    <?php if ($product['stock_count'] > 0): ?>
                                        <button class="btn btn-outline-success btn-sm" onclick="addToCart(<?= $product['id'] ?>)">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="addToWishlist(<?= $product['id'] ?>)">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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