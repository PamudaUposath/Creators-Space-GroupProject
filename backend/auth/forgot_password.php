<?php
// backend/auth/forgot_password.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

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
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, first_name, email FROM users WHERE email = ? AND is_active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Always return success message to prevent email enumeration
    $successMessage = 'If an account with that email exists, a password reset link has been sent.';

    if ($user) {
        // Generate reset token
        $resetToken = generateSecureToken();
        $resetExpires = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now

        // Store reset token in database
        $stmt = $pdo->prepare("
            UPDATE users 
            SET reset_token = ?, reset_expires = ? 
            WHERE id = ?
        ");
        $stmt->execute([$resetToken, $resetExpires, $user['id']]);

        // Create reset link
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/backend/auth/reset_password.php?token=" . $resetToken;

        // Send email
        $subject = "Password Reset - Creators-Space";
        $message = getPasswordResetEmailContent($resetLink, $user['first_name']);
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: noreply@creatorsspace.local\r\n";

        sendEmail($user['email'], $subject, $message, $headers);

        // Log activity
        logActivity($user['id'], 'password_reset_requested', "Password reset requested for: " . $email);
    }

    successResponse($successMessage);

} catch (PDOException $e) {
    error_log("Forgot password error: " . $e->getMessage());
    errorResponse('Unable to process request. Please try again.', 500);
}
?>
