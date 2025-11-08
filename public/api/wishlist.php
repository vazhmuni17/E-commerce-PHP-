<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Models/Wishlist.php';
require_once __DIR__ . '/../../src/Models/Product.php';

use Src\Models\Wishlist;
use Src\Models\Product;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to manage wishlist']);
    exit();
}

$userId = $_SESSION['user_id'];
$wishlistModel = new Wishlist();
$productModel = new Product();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $productId = $_POST['product_id'] ?? 0;
        
        if (!$productId) {
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            exit();
        }
        
        $product = $productModel->find($productId);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit();
        }
        
        // Check if already in wishlist
        if ($wishlistModel->isInWishlist($userId, $productId)) {
            echo json_encode(['success' => false, 'message' => 'Product already in wishlist']);
            exit();
        }
        
        $wishlistModel->create([
            'user_id' => $userId,
            'product_id' => $productId
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Product added to wishlist']);
        break;
        
    case 'remove':
        $wishlistId = $_POST['wishlist_id'] ?? 0;
        
        if (!$wishlistId) {
            echo json_encode(['success' => false, 'message' => 'Wishlist item ID required']);
            exit();
        }
        
        $wishlistItem = $wishlistModel->find($wishlistId);
        if (!$wishlistItem || $wishlistItem['user_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Wishlist item not found']);
            exit();
        }
        
        $wishlistModel->delete($wishlistId);
        echo json_encode(['success' => true, 'message' => 'Item removed from wishlist']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>