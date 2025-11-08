<?php
namespace Src\Models;

class Address extends BaseModel {
    protected $table = 'addresses';
    protected $fillable = ['user_id', 'name', 'street', 'city', 'state', 'zip_code', 'country', 'is_default'];
    
    public function getUser() {
        $userModel = new User();
        return $userModel->find($this->user_id);
    }
    
    public function setAsDefault($userId) {
        // Remove default from all other addresses
        $sql = "UPDATE {$this->table} SET is_default = FALSE WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        
        // Set current as default
        $this->update($this->id, ['is_default' => true]);
    }
    
    public function getDefaultAddress($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND is_default = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}