<?php
namespace Src\Models;

class CartItem extends BaseModel {
    protected $table = 'cart_items';
    protected $fillable = ['user_id', 'product_id', 'quantity'];
    
    public function getUser() {
        $userModel = new User();
        return $userModel->find($this->user_id);
    }
    
    public function getProduct() {
        $productModel = new Product();
        return $productModel->find($this->product_id);
    }
    
    public function getTotalPrice() {
        $product = $this->getProduct();
        return $product ? $product->price * $this->quantity : 0;
    }
    
    public function incrementQuantity() {
        $this->quantity += 1;
        $this->update($this->id, ['quantity' => $this->quantity]);
        return $this->quantity;
    }
    
    public function decrementQuantity() {
        if ($this->quantity > 1) {
            $this->quantity -= 1;
            $this->update($this->id, ['quantity' => $this->quantity]);
        }
        return $this->quantity;
    }
    
    public function getUserCart($userId) {
        $sql = "SELECT ci.*, p.name, p.price, p.image, p.stock_count 
                FROM {$this->table} ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getCartTotal($userId) {
        $sql = "SELECT SUM(ci.quantity * p.price) as total
                FROM {$this->table} ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
}