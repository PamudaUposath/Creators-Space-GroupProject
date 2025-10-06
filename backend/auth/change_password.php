<?php
// backend/auth/change_password.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

$token = $_POST['token'] ?? '';
$tempPassword = $_POST['temp_password'] ?? '';
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate new password
if (empty($newPassword) || $newPassword !== $confirmPassword) {
    errorResponse('New password and confirmation do not match');
}

// Validate password strength
if (!isStrongPassword($newPassword)) {
    errorResponse('Password does not meet security requirements');
}

try {
    if (!empty($token)) {
        // Handle token-based password change (from email)
        $stmt = $pdo->prepare("
            SELECT id, first_name, password_hash 
            FROM users 
            WHERE username = ? AND is_active = 1
        ");
        // Extract username from token (you might want to implement proper token validation)
        $username = base64_decode($token);
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            errorResponse('Invalid token or user not found');
        }
        
        // Verify temporary password
        if (!password_verify($tempPassword, $user['password_hash'])) {
            errorResponse('Invalid temporary password');
        }
        
        $userId = $user['id'];
        
    } else {
        // Handle regular password change (logged in user)
        if (!isset($_SESSION['user_id'])) {
            errorResponse('You must be logged in to change your password');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Verify current password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            errorResponse('Current password is incorrect');
        }
    }
    
    // Update password
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password_hash = ?, reset_token = NULL, reset_expires = NULL, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$newPasswordHash, $userId]);
    
    // Log activity
    logActivity($userId, 'password_changed', 'Password successfully changed');
    
    // Clear session if it was a token-based change
    if (!empty($token)) {
        session_destroy();
    }
    
    successResponse('Password changed successfully');
    
} catch (PDOException $e) {
    error_log("Password change error: " . $e->getMessage());
    errorResponse('Unable to change password. Please try again.', 500);
}

/**
 * Check if password meets strength requirements
 */
function isStrongPassword($password) {
    return strlen($password) >= 8 &&
           preg_match('/[A-Z]/', $password) &&
           preg_match('/[a-z]/', $password) &&
           preg_match('/\d/', $password) &&
           preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password);
}
?>