<?php
namespace Src\Models;

class User extends BaseModel {
    protected $table = 'users';
    protected $fillable = ['google_id', 'email', 'name', 'profile_image'];
    
    public function findByEmail($email) {
        return $this->where('email', '=', $email)[0] ?? null;
    }
    
    public function findByGoogleId($googleId) {
        return $this->where('google_id', '=', $googleId)[0] ?? null;
    }
    
    public function getAddresses() {
        $addressModel = new Address();
        return $addressModel->where('user_id', '=', $this->id);
    }
    
    public function getCartItems() {
        $cartModel = new CartItem();
        return $cartModel->where('user_id', '=', $this->id);
    }
    
    public function getWishlist() {
        $wishlistModel = new Wishlist();
        return $wishlistModel->where('user_id', '=', $this->id);
    }
    
    public function getOrders() {
        $orderModel = new Order();
        return $orderModel->where('user_id', '=', $this->id);
    }
}