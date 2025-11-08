<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Models/Order.php';
require_once __DIR__ . '/../../src/Models/OrderItem.php';
require_once __DIR__ . '/../../src/Utils/Helper.php';
require_once __DIR__ . '/../../src/Utils/Auth.php';

use Src\Models\Order;
use Src\Models\OrderItem;
use Src\Utils\Helper;
use Src\Utils\AdminAuth;

// Check admin authentication
AdminAuth::redirectIfNotAuthenticated();

$orderModel = new Order();
$orderItemModel = new OrderItem();

// Handle order status updates
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'] ?? 0;
    $status = $_POST['status'] ?? '';
    
    if ($orderId && $status) {
        $order = $orderModel->find($orderId);
        if ($order) {
            if ($orderModel->updateStatus($status)) {
                $message = 'Order status updated successfully!';
            } else {
                $error = 'Invalid status';
            }
        } else {
            $error = 'Order not found';
        }
    }
}

// Get all orders
$orders = $orderModel->getAllOrders();

// Get order details if viewing a specific order
$viewOrder = null;
$orderItems = [];
if (isset($_GET['view'])) {
    $orderId = $_GET['view'];
    $viewOrder = $orderModel->find($orderId);
    if ($viewOrder) {
        $orderItems = $viewOrder->getItems();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Admin - PHP E-commerce</title>
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
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php">Orders</a>
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
        <h1>Manage Orders</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Email</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td><?= htmlspecialchars($order['email']) ?></td>
                                    <td><?= Helper::formatPrice($order['total_amount']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $order['status'] == 'delivered' ? 'success' : ($order['status'] == 'shipped' ? 'info' : 'warning') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= Helper::formatDate($order['created_at']) ?></td>
                                    <td>
                                        <a href="orders.php?view=<?= $order['id'] ?>" class="btn btn-sm btn-info">View</a>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <input type="hidden" name="update_status" value="1">
                                            <select name="status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                                <option value="on_process" <?= $order['status'] == 'on_process' ? 'selected' : '' ?>>On Process</option>
                                                <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                                <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Details Modal -->
        <?php if ($viewOrder): ?>
            <div class="modal fade show d-block" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Order #<?= $viewOrder['id'] ?> Details</h5>
                            <a href="orders.php" class="btn-close"></a>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Customer Information</h6>
                                    <p><strong>Name:</strong> <?= htmlspecialchars($viewOrder['user_name']) ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($viewOrder['email']) ?></p>
                                    <p><strong>Address:</strong> <?= htmlspecialchars($viewOrder['address_name']) ?>, <?= htmlspecialchars($viewOrder['street']) ?>, <?= htmlspecialchars($viewOrder['city']) ?>, <?= htmlspecialchars($viewOrder['state']) ?> <?= htmlspecialchars($viewOrder['zip_code']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h6>Order Information</h6>
                                    <p><strong>Order ID:</strong> #<?= $viewOrder['id'] ?></p>
                                    <p><strong>Date:</strong> <?= Helper::formatDate($viewOrder['created_at']) ?></p>
                                    <p><strong>Status:</strong> 
                                        <span class="badge bg-<?= $viewOrder['status'] == 'delivered' ? 'success' : ($viewOrder['status'] == 'shipped' ? 'info' : 'warning') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $viewOrder['status'])) ?>
                                        </span>
                                    </p>
                                    <p><strong>Total:</strong> <?= Helper::formatPrice($viewOrder['total_amount']) ?></p>
                                </div>
                            </div>
                            
                            <h6 class="mt-4">Order Items</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orderItems as $item): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($item['name']) ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td><?= Helper::formatPrice($item['price']) ?></td>
                                                <td><?= Helper::formatPrice($item['price'] * $item['quantity']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="orders.php" class="btn btn-secondary">Close</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>