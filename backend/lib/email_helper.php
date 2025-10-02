<?php
// Email helper functions using PHPMailer

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send email using PHPMailer
 * 
 * @param string $to_email Recipient email
 * @param string $to_name Recipient name
 * @param string $subject Email subject
 * @param string $message Email body
 * @return bool Success status
 */
function sendEmailWithPHPMailer($to_email, $to_name, $subject, $message) {
    // Load email configuration
    $config = require __DIR__ . '/../config/email_config.php';
    
    // Check if using placeholder credentials (for testing)
    if (strpos($config['smtp_username'], 'your-') !== false) {
        // Log email for testing instead of sending
        $log_message = "EMAIL LOG:\nTO: $to_email ($to_name)\nSUBJECT: $subject\nMESSAGE:\n$message\n" . str_repeat("-", 50) . "\n";
        error_log($log_message);
        
        // Also save to a local file for easy viewing
        file_put_contents(__DIR__ . '/../logs/emails.log', date('Y-m-d H:i:s') . " - " . $log_message, FILE_APPEND | LOCK_EX);
        return true;
    }
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = $config['smtp_auth'];
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['smtp_port'];
        $mail->CharSet = $config['charset'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $mail->isHTML(false); // Send as plain text for better compatibility
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log('PHPMailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send instructor welcome email
 * 
 * @param string $email Instructor email
 * @param string $first_name Instructor first name
 * @param string $username Login username
 * @param string $temp_password Temporary password
 * @return bool Success status
 */
function sendInstructorWelcomeEmail($email, $first_name, $username, $temp_password) {
    $subject = "Welcome to Creators-Space as Instructor";
    $reset_link = "http://localhost/Creators-Space-GroupProject/frontend/login.php";
    
    $message = "Hello $first_name,\n\n";
    $message .= "You have been added as an instructor to Creators-Space.\n\n";
    $message .= "Your login credentials:\n";
    $message .= "Username: $username\n";
    $message .= "Temporary Password: $temp_password\n\n";
    $message .= "Please login at $reset_link and change your password immediately.\n\n";
    $message .= "Welcome to the Creators-Space team!\n\n";
    $message .= "Best regards,\n";
    $message .= "Creators-Space Admin Team";
    
    return sendEmailWithPHPMailer($email, $first_name, $subject, $message);
}

/**
 * Send password reset email
 * 
 * @param string $email User email
 * @param string $first_name User first name
 * @param string $reset_link Password reset link
 * @return bool Success status
 */
function sendPasswordResetEmail($email, $first_name, $reset_link) {
    $subject = "Password Reset - Creators-Space";
    
    $message = "Hello $first_name,\n\n";
    $message .= "You have requested a password reset for your Creators-Space account.\n\n";
    $message .= "Click the link below to reset your password:\n";
    $message .= "$reset_link\n\n";
    $message .= "This link will expire in 1 hour for security reasons.\n\n";
    $message .= "If you didn't request this password reset, please ignore this email.\n\n";
    $message .= "Best regards,\n";
    $message .= "Creators-Space Team";
    
    return sendEmailWithPHPMailer($email, $first_name, $subject, $message);
}
?>