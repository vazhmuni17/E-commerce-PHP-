<?php
namespace Src\Models;

class Wishlist extends BaseModel {
    protected $table = 'wishlist';
    protected $fillable = ['user_id', 'product_id'];
    
    public function getUser() {
        $userModel = new User();
        return $userModel->find($this->user_id);
    }
    
    public function getProduct() {
        $productModel = new Product();
        return $productModel->find($this->product_id);
    }
    
    public function getUserWishlist($userId) {
        $sql = "SELECT w.*, p.name, p.price, p.image, p.stock_count 
                FROM {$this->table} w
                JOIN products p ON w.product_id = p.id
                WHERE w.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function isInWishlist($userId, $productId) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }
}