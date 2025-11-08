<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/Models/CartItem.php';
require_once __DIR__ . '/../../src/Models/Product.php';

use Src\Models\CartItem;
use Src\Models\Product;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to manage cart']);
    exit();
}

$userId = $_SESSION['user_id'];
$cartModel = new CartItem();
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
        if (!$product || $product['stock_count'] <= 0) {
            echo json_encode(['success' => false, 'message' => 'Product not available']);
            exit();
        }
        
        // Check if item already in cart
        $existingItems = $cartModel->where('user_id', '=', $userId);
        $existingItem = null;
        foreach ($existingItems as $item) {
            if ($item['product_id'] == $productId) {
                $existingItem = $item;
                break;
            }
        }
        
        if ($existingItem) {
            // Update quantity
            $cartModel->update($existingItem['id'], ['quantity' => $existingItem['quantity'] + 1]);
        } else {
            // Add new item
            $cartModel->create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
        
        // Update cart count in session
        $cartItems = $cartModel->getUserCart($userId);
        $_SESSION['cart_count'] = count($cartItems);
        
        echo json_encode(['success' => true, 'message' => 'Product added to cart']);
        break;
        
    case 'update':
        $cartItemId = $_POST['cart_item_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!$cartItemId || $quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
            exit();
        }
        
        $cartItem = $cartModel->find($cartItemId);
        if (!$cartItem || $cartItem['user_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Cart item not found']);
            exit();
        }
        
        $product = $productModel->find($cartItem['product_id']);
        if ($quantity > $product['stock_count']) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            exit();
        }
        
        $cartModel->update($cartItemId, ['quantity' => $quantity]);
        echo json_encode(['success' => true, 'message' => 'Cart updated']);
        break;
        
    case 'remove':
        $cartItemId = $_POST['cart_item_id'] ?? 0;
        
        if (!$cartItemId) {
            echo json_encode(['success' => false, 'message' => 'Cart item ID required']);
            exit();
        }
        
        $cartItem = $cartModel->find($cartItemId);
        if (!$cartItem || $cartItem['user_id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Cart item not found']);
            exit();
        }
        
        $cartModel->delete($cartItemId);
        
        // Update cart count in session
        $cartItems = $cartModel->getUserCart($userId);
        $_SESSION['cart_count'] = count($cartItems);
        
        echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>