<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Models/Category.php';
require_once __DIR__ . '/../../src/Utils/Helper.php';
require_once __DIR__ . '/../../src/Utils/Auth.php';

use Src\Models\Category;
use Src\Utils\Helper;
use Src\Utils\AdminAuth;

// Check admin authentication
AdminAuth::redirectIfNotAuthenticated();

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
                'description' => $_POST['description'] ?? ''
            ];
            
            if ($data['name']) {
                $categoryModel->create($data);
                $message = 'Category added successfully!';
            } else {
                $error = 'Please fill in the category name';
            }
            break;
            
        case 'edit':
            $id = $_POST['id'] ?? 0;
            $data = [
                'name' => $_POST['name'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];
            
            if ($id && $data['name']) {
                $categoryModel->update($id, $data);
                $message = 'Category updated successfully!';
            } else {
                $error = 'Please fill in the category name';
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            if ($id) {
                $categoryModel->delete($id);
                $message = 'Category deleted successfully!';
            }
            break;
    }
}

// Get all categories
$categories = $categoryModel->findAll();

// Check if we're editing a category
$editCategory = null;
if (isset($_GET['edit'])) {
    $editCategory = $categoryModel->find($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin - PHP E-commerce</title>
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
                        <a class="nav-link active" href="categories.php">Categories</a>
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

    <!-- Content -->
    <div class="container py-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5><?= $editCategory ? 'Edit Category' : 'Add New Category' ?></h5>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="<?= $editCategory ? 'edit' : 'add' ?>">
                            <?php if ($editCategory): ?>
                                <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Category Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= $editCategory['name'] ?? '' ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?= $editCategory['description'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <?= $editCategory ? 'Update' : 'Add' ?> Category
                                </button>
                                <?php if ($editCategory): ?>
                                    <a href="categories.php" class="btn btn-secondary">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>All Categories</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Product Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td><?= htmlspecialchars($category['name']) ?></td>
                                            <td><?= htmlspecialchars($category['description']) ?></td>
                                            <td>
                                                <?php
                                                $productCount = $categoryModel->getProductCount($category['id']);
                                                echo $productCount;
                                                ?>
                                            </td>
                                            <td>
                                                <a href="categories.php?edit=<?= $category['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $category['id'] ?>">
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