<?php
// backend/lib/helpers.php

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate secure random token
 */
function generateSecureToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email format
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

/**
 * Check if user is instructor
 */
function isInstructor() {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'instructor';
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin($redirectUrl = '/backend/public/admin_login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Redirect to login if not admin
 */
function requireAdmin($redirectUrl = '/backend/public/admin_login.php') {
    if (!isAdmin()) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Send JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function errorResponse($message, $statusCode = 400) {
    jsonResponse(['success' => false, 'message' => $message], $statusCode);
}

/**
 * Send success response
 */
function successResponse($message, $data = null) {
    $response = ['success' => true, 'message' => $message];
    if ($data !== null) {
        $response['data'] = $data;
    }
    jsonResponse($response);
}

/**
 * Rate limiting check (simple implementation)
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 900) { // 15 minutes
    $attempts = $_SESSION["rate_limit_$identifier"] ?? [];
    $now = time();
    
    // Remove attempts outside time window
    $attempts = array_filter($attempts, function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });
    
    if (count($attempts) >= $maxAttempts) {
        return false;
    }
    
    $attempts[] = $now;
    $_SESSION["rate_limit_$identifier"] = $attempts;
    return true;
}

/**
 * Send email (placeholder - implement with your preferred mail service)
 */
function sendEmail($to, $subject, $message, $headers = '') {
    // For development, you can log emails or use a service like Mailtrap
    // For production, integrate with SMTP or a service like SendGrid
    
    // Simple PHP mail (not recommended for production)
    // return mail($to, $subject, $message, $headers);
    
    // For now, just log the email
    error_log("EMAIL TO: $to, SUBJECT: $subject, MESSAGE: $message");
    return true; // Return true for development
}

/**
 * Generate password reset email content
 */
function getPasswordResetEmailContent($resetLink, $firstName) {
    return "
    <html>
    <body>
        <h2>Password Reset Request</h2>
        <p>Hello $firstName,</p>
        <p>You have requested to reset your password. Click the link below to reset it:</p>
        <p><a href='$resetLink'>Reset Password</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this, please ignore this email.</p>
        <p>Best regards,<br>Creators-Space Team</p>
    </body>
    </html>
    ";
}

/**
 * Log user activity (for security monitoring)
 */
function logActivity($userId, $action, $details = '') {
    // This could write to a separate activity log table
    error_log("USER ACTIVITY - User ID: $userId, Action: $action, Details: $details");
}
?>
