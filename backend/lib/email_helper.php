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
 * @param string $last_name Instructor last name
 * @param string $username Login username
 * @param string $temp_password Temporary password
 * @return bool Success status
 */
function sendInstructorWelcomeEmail($email, $first_name, $last_name, $username, $temp_password) {
    $subject = "Welcome to Creators-Space as Instructor";
    
    // Create a secure token for direct password change
    $token = base64_encode($username);
    $change_password_link = "http://localhost/Creators-Space-GroupProject/frontend/change-password.php?token=" . urlencode($token);
    $login_link = "http://localhost/Creators-Space-GroupProject/frontend/login.php";
    
    $message = "Dear $first_name $last_name,\n\n";
    $message .= "You have been added as an instructor to Creators-Space.\n\n";
    $message .= "Your login credentials:\n";
    $message .= "Email: $email\n";
    $message .= "Temporary Password: $temp_password\n\n";
    $message .= "Username: $username\n";
    $message .= "IMPORTANT: For security, please set up your new password immediately:\n";
    $message .= "👉 Click here to change your password: $change_password_link\n\n";
    //$message .= "Alternatively, you can login at $login_link and change your password from your profile.\n\n";
    $message .= "This temporary password will remain active until you change it.\n\n";
    $message .= "Welcome to the Creators-Space team! We're excited to have you on board.\n\n";
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

/**
 * Send instructor promotion email
 * 
 * @param string $email User email
 * @param string $first_name User first name
 * @param string $last_name User last name
 * @return bool Success status
 */
function sendInstructorPromotionEmail($email, $first_name, $last_name) {
    $subject = "Congratulations! You've been promoted to Instructor - Creators-Space";
    
    // Build links
    $instructor_dashboard_link = "http://localhost/Creators-Space-GroupProject/frontend/instructor-dashboard.php";
    $login_link = "http://localhost/Creators-Space-GroupProject/frontend/login.php";
    
    $message = "Dear $first_name $last_name,\n\n";
    $message .= "🎉 Congratulations! We are excited to inform you that you have been promoted to an Instructor on Creators-Space!\n\n";
    $message .= "This promotion recognizes your dedication and expertise. As an instructor, you now have access to:\n\n";
    $message .= "✅ Create and manage your own courses\n";
    $message .= "✅ Upload course materials and videos\n";
    $message .= "✅ Track student enrollments and progress\n";
    $message .= "✅ Communicate with your students\n";
    $message .= "✅ Access instructor dashboard and analytics\n\n";
    $message .= "Get started with your new role:\n";
    $message .= "👉 Login to your account: $login_link\n";
    $message .= "📊 Access your instructor dashboard: $instructor_dashboard_link\n\n";
    $message .= "We believe you'll make a great impact in the learning community. Welcome to the instructor team!\n\n";
    $message .= "If you have any questions about your new role or need assistance getting started, please don't hesitate to contact our support team.\n\n";
    $message .= "Best regards,\n";
    $message .= "Creators-Space Admin Team";
    
    return sendEmailWithPHPMailer($email, "$first_name $last_name", $subject, $message);
}
?>