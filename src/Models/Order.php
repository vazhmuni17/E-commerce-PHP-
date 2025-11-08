<?php
namespace Src\Models;

class Order extends BaseModel {
    protected $table = 'orders';
    protected $fillable = ['user_id', 'address_id', 'total_amount', 'status'];
    
    public function getUser() {
        $userModel = new User();
        return $userModel->find($this->user_id);
    }
    
    public function getAddress() {
        $addressModel = new Address();
        return $addressModel->find($this->address_id);
    }
    
    public function getItems() {
        $orderItemModel = new OrderItem();
        return $orderItemModel->where('order_id', '=', $this->id);
    }
    
    public function getStatusText() {
        $statusMap = [
            'on_process' => 'On Process',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered'
        ];
        return $statusMap[$this->status] ?? 'Unknown';
    }
    
    public function updateStatus($status) {
        $validStatuses = ['on_process', 'shipped', 'delivered'];
        if (in_array($status, $validStatuses)) {
            $this->update($this->id, ['status' => $status]);
            return true;
        }
        return false;
    }
    
    public function getUserOrders($userId) {
        $sql = "SELECT o.*, a.name as address_name, a.street, a.city, a.state, a.zip_code
                FROM {$this->table} o
                JOIN addresses a ON o.address_id = a.id
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAllOrders() {
        $sql = "SELECT o.*, u.name as user_name, u.email, a.name as address_name
                FROM {$this->table} o
                JOIN users u ON o.user_id = u.id
                JOIN addresses a ON o.address_id = a.id
                ORDER BY o.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}