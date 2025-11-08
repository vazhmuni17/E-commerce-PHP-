<?php
namespace Src\Utils;

class Auth {
    public static function check() {
        return isset($_SESSION['user_id']);
    }
    
    public static function user() {
        if (self::check()) {
            $userModel = new \Src\Models\User();
            return $userModel->find($_SESSION['user_id']);
        }
        return null;
    }
    
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
    }
    
    public static function logout() {
        session_destroy();
    }
    
    public static function redirectIfNotAuthenticated() {
        if (!self::check()) {
            header('Location: /login.php');
            exit();
        }
    }
}

class AdminAuth {
    public static function check() {
        return isset($_SESSION['admin_id']);
    }
    
    public static function admin() {
        if (self::check()) {
            $adminModel = new \Src\Models\Admin();
            return $adminModel->find($_SESSION['admin_id']);
        }
        return null;
    }
    
    public static function login($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
    }
    
    public static function logout() {
        session_destroy();
    }
    
    public static function redirectIfNotAuthenticated() {
        if (!self::check()) {
            header('Location: /admin/login.php');
            exit();
        }
    }
}