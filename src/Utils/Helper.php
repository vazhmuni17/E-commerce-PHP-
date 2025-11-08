<?php
namespace Src\Utils;

class Helper {
    public static function formatPrice($price) {
        return '$' . number_format($price, 2);
    }
    
    public static function formatDate($date) {
        return date('M d, Y H:i:s', strtotime($date));
    }
    
    public static function truncateText($text, $length = 100) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length) . '...';
        }
        return $text;
    }
    
    public static function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
    
    public static function redirect($url) {
        header("Location: {$url}");
        exit();
    }
    
    public static function jsonResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
    
    public static function uploadFile($file, $uploadDir, $allowedTypes = ['image/jpeg', 'image/png', 'image/gif']) {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        $fileType = mime_content_type($file['tmp_name']);
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
        
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $uploadPath = $uploadDir . $fileName;
        
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        }
        
        return false;
    }
    
    public static function generateInvoiceNumber($orderId) {
        return 'INV-' . date('Y') . '-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);
    }
}