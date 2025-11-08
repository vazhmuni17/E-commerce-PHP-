<?php
namespace Src\Models;

class Product extends BaseModel {
    protected $table = 'products';
    protected $fillable = ['name', 'description', 'price', 'stock_count', 'category_id', 'image'];
    
    public function getCategory() {
        $categoryModel = new Category();
        return $categoryModel->find($this->category_id);
    }
    
    public function isInStock() {
        return $this->stock_count > 0;
    }
    
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE 
                name LIKE :keyword OR 
                description LIKE :keyword";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }
    
    public function sortByPrice($order = 'ASC') {
        $sql = "SELECT * FROM {$this->table} ORDER BY price " . ($order === 'DESC' ? 'DESC' : 'ASC');
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function sortByNewest() {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    public function getByCategory($categoryId) {
        return $this->where('category_id', '=', $categoryId);
    }
    
    public function reduceStock($quantity) {
        if ($this->stock_count >= $quantity) {
            $this->stock_count -= $quantity;
            $this->update($this->id, ['stock_count' => $this->stock_count]);
            return true;
        }
        return false;
    }
}