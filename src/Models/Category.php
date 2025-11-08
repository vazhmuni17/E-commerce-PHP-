<?php
namespace Src\Models;

class Category extends BaseModel {
    protected $table = 'categories';
    protected $fillable = ['name', 'description'];
    
    public function getProducts() {
        $productModel = new Product();
        return $productModel->where('category_id', '=', $this->id);
    }
    
    public function getProductCount() {
        $productModel = new Product();
        $products = $productModel->where('category_id', '=', $this->id);
        return count($products);
    }
}