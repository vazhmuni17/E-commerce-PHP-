<?php
session_start();

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

require_once __DIR__ . '/../config/config.php';
$config = include __DIR__ . '/../config/config.php';
$googleConfig = $config['google_oauth'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PHP E-commerce</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background: white;
        }
        .google-btn {
            background: #4285f4;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .google-btn:hover {
            background: #357ae8;
        }
        body {
            background: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Welcome to PHP E-commerce</h2>
            <p class="text-center text-muted mb-4">Sign in with your Google account to continue</p>
            
            <button class="google-btn" onclick="loginWithGoogle()">
                <i class="fab fa-google"></i>
                Sign in with Google
            </button>
            
            <div class="text-center mt-3">
                <small class="text-muted">By signing in, you agree to our Terms of Service</small>
            </div>
        </div>
    </div>

    <script>
        function loginWithGoogle() {
            // Redirect to Google OAuth
            const clientId = '<?= $googleConfig['client_id'] ?>';
            const redirectUri = '<?= $googleConfig['redirect_uri'] ?>';
            const scope = 'email profile';
            const responseType = 'code';
            
            const authUrl = `https://accounts.google.com/o/oauth2/auth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${encodeURIComponent(scope)}&response_type=${responseType}`;
            
            window.location.href = authUrl;
        }
    </script>
</body>
</html>