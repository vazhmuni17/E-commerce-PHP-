<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../config/config.php';

use Src\Models\User;

$config = include __DIR__ . '/../config/config.php';
$googleConfig = $config['google_oauth'];

if (!isset($_GET['code'])) {
    header('Location: login.php');
    exit();
}

$code = $_GET['code'];

// Exchange authorization code for access token
$tokenUrl = 'https://oauth2.googleapis.com/token';
$tokenData = [
    'client_id' => $googleConfig['client_id'],
    'client_secret' => $googleConfig['client_secret'],
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $googleConfig['redirect_uri']
];

$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);

if (!isset($tokenData['access_token'])) {
    header('Location: login.php?error=oauth_failed');
    exit();
}

// Get user info from Google
$userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
$accessToken = $tokenData['access_token'];

$ch = curl_init($userInfoUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$userInfo = curl_exec($ch);
curl_close($ch);

$userData = json_decode($userInfo, true);

if (!isset($userData['id'])) {
    header('Location: login.php?error=user_info_failed');
    exit();
}

// Create or update user in database
$userModel = new User();
$existingUser = $userModel->findByGoogleId($userData['id']);

if ($existingUser) {
    // Update existing user
    $userModel->update($existingUser['id'], [
        'email' => $userData['email'],
        'name' => $userData['name'],
        'profile_image' => $userData['picture'] ?? null
    ]);
    $user = $existingUser;
} else {
    // Check if user exists with same email
    $emailUser = $userModel->findByEmail($userData['email']);
    
    if ($emailUser) {
        // Update existing user with Google ID
        $userModel->update($emailUser['id'], [
            'google_id' => $userData['id'],
            'name' => $userData['name'],
            'profile_image' => $userData['picture'] ?? null
        ]);
        $user = $emailUser;
    } else {
        // Create new user
        $userId = $userModel->create([
            'google_id' => $userData['id'],
            'email' => $userData['email'],
            'name' => $userData['name'],
            'profile_image' => $userData['picture'] ?? null
        ]);
        $user = $userModel->find($userId);
    }
}

// Login user
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];

// Update cart count
$cartModel = new \Src\Models\CartItem();
$cartItems = $cartModel->getUserCart($user['id']);
$_SESSION['cart_count'] = count($cartItems);

header('Location: index.php');
exit();