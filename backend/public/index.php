<?php
// backend/public/index.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Simple API status endpoint
header('Content-Type: application/json');

// If this is accessed directly, show API info
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    jsonResponse([
        'success' => true,
        'message' => 'Creators-Space Backend API',
        'version' => '1.0.0',
        'endpoints' => [
            'authentication' => [
                'POST /auth/signup_process.php' => 'User registration',
                'POST /auth/login_process.php' => 'User login',
                'GET /auth/logout.php' => 'User logout',
                'POST /auth/forgot_password.php' => 'Password reset request',
                'GET /auth/reset_password.php' => 'Password reset form'
            ],
            'admin' => [
                'GET /public/admin_login.php' => 'Admin login page',
                'GET /admin/dashboard.php' => 'Admin dashboard',
                'GET /admin/users.php' => 'User management'
            ]
        ],
        'database_status' => 'connected',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
