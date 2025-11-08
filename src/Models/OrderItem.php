<?php
namespace Src\Models;

class OrderItem extends BaseModel {
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];
    
    public function getOrder() {
        $orderModel = new Order();
        return $orderModel->find($this->order_id);
    }
    
    public function getProduct() {
        $productModel = new Product();
        return $productModel->find($this->product_id);
    }
    
    public function getTotalPrice() {
        return $this->price * $this->quantity;
    }
}