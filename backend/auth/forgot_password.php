<?php
// backend/auth/forgot_password.php

// Set CORS headers to allow frontend requests
$allowedOrigins = [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:3000',
    'http://localhost:8080',
    'http://localhost/Creators-Space-GroupProject',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins) || strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false) {
    header("Access-Control-Allow-Origin: $origin");
} else {
    header("Access-Control-Allow-Origin: http://localhost");
}

header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/email_helper.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Rate limiting
if (!checkRateLimit('forgot_password_' . $_SERVER['REMOTE_ADDR'], 3, 600)) { // 3 attempts per 10 minutes
    errorResponse('Too many password reset attempts. Please try again later.', 429);
}

// Get and validate email
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    errorResponse('Valid email is required');
}

try {
    // Verify database connection
    if (!isset($pdo) || !$pdo) {
        throw new Exception('Database connection not available');
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, first_name, email FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Always return success message to prevent email enumeration
    $successMessage = 'If an account with that email exists, a password reset link has been sent.';

    if ($user) {
        // Generate reset token (use shorter, URL-safe token)
        $resetToken = generateSecureToken(32); // 64 character hex string
        $resetExpires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

        // Clear any existing reset tokens first, then store new token
        $stmt = $pdo->prepare("
            UPDATE users 
            SET reset_token = ?, reset_expires = ? 
            WHERE id = ?
        ");
        $result = $stmt->execute([$resetToken, $resetExpires, $user['id']]);
        
        if (!$result) {
            throw new Exception('Failed to update reset token in database');
        }
        
        // Log token creation for debugging (remove in production)
        error_log("Password reset token created for user {$user['id']} ({$user['email']}): Token length " . strlen($resetToken) . ", expires at $resetExpires");

        // Create reset link - build dynamic path
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        
        // Try to determine the project base path
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Extract project root from the current script path
        if (strpos($scriptName, '/backend/auth/') !== false) {
            $projectRoot = substr($scriptName, 0, strpos($scriptName, '/backend/auth/'));
        } else {
            $projectRoot = '/Creators-Space-GroupProject'; // fallback
        }
        
        $resetLink = $protocol . "://" . $host . $projectRoot . "/backend/auth/reset_password.php?token=" . urlencode($resetToken);

        // Send password reset email using PHPMailer
        sendPasswordResetEmail($user['email'], $user['first_name'], $resetLink);

        // Log activity
        logActivity($user['id'], 'password_reset_requested', "Password reset requested for: " . $email);
    }

    successResponse($successMessage);

} catch (PDOException $e) {
    error_log("Forgot password database error: " . $e->getMessage());
    errorResponse('Database error occurred. Please try again later.', 500);
} catch (Exception $e) {
    error_log("Forgot password general error: " . $e->getMessage());
    errorResponse('Unable to process request. Please try again.', 500);
}
?>
