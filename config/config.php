<?php
// Database configuration
return [
    'database' => [
        'host' => 'localhost',
        'dbname' => 'ecommerce',
        'username' => 'root',
        'password' => 'vazhmuni',
        'charset' => 'utf8mb4'
    ],
    
    'google_oauth' => [
        'client_id' => 'YOUR_GOOGLE_CLIENT_ID',
        'client_secret' => 'YOUR_GOOGLE_CLIENT_SECRET',
        'redirect_uri' => 'http://localhost/php-ecommerce/public/google-callback.php'
    ],
    
    'app' => [
        'base_url' => 'http://localhost/php-ecommerce/public',
        'upload_path' => __DIR__ . '/../uploads/',
        'admin_email' => 'admin@example.com'
    ]
];