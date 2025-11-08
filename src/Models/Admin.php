<?php
namespace Src\Models;

class Admin extends BaseModel {
    protected $table = 'admins';
    protected $fillable = ['email', 'password', 'name'];
    
    public function findByEmail($email) {
        return $this->where('email', '=', $email)[0] ?? null;
    }
    
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }
    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}