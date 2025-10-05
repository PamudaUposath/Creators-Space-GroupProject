<?php
// backend/lib/email_service.php
// Email service for sending certificates

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendCertificateEmail($recipientEmail, $recipientName, $courseName, $certificateCode, $certificatePath, $courseLevel = 'Advanced') {
    // Always use PHPMailer for better reliability
    return sendCertificateEmailPHPMailer($recipientEmail, $recipientName, $courseName, $certificateCode, $certificatePath, $courseLevel);
}

// PHP mail() function version (basic email sending)
function sendCertificateEmailPHP($recipientEmail, $recipientName, $courseName, $certificateCode, $certificatePath) {
    try {
        // Configure basic email settings
        $senderEmail = 'noreply@creatorsspace.com';
        $senderName = 'Creators Space';
        $subject = 'Congratulations! Your Certificate of Completion - ' . $certificateCode;
        
        // Check if certificate file exists
        if (!file_exists($certificatePath)) {
            throw new Exception("Certificate file not found: $certificatePath");
        }
        
        // Simple HTML email (without attachment for better compatibility)
        $headers = "From: $senderName <$senderEmail>\r\n";
        $headers .= "Reply-To: $senderEmail\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        // Generate simple HTML content
        $certificateUrl = 'http://localhost/Creators-Space-GroupProject/storage/certificates/' . basename($certificatePath);
        
        $htmlContent = "
        <html>
        <body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 30px; text-align: center; border-radius: 10px;'>
                <h1>Congratulations!</h1>
                <p style='font-size: 18px; margin: 0;'>You have earned your certificate!</p>
            </div>
            
            <div style='padding: 30px; background: #f9f9f9; margin: 20px 0; border-radius: 10px;'>
                <h2>Certificate Details</h2>
                <p><strong>Student:</strong> " . htmlspecialchars($recipientName) . "</p>
                <p><strong>Course:</strong> " . htmlspecialchars($courseName) . "</p>
                <p><strong>Certificate ID:</strong> " . htmlspecialchars($certificateCode) . "</p>
                <p><strong>Issue Date:</strong> " . date('F j, Y') . "</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='" . htmlspecialchars($certificateUrl) . "' 
                       style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                        View Your Certificate
                    </a>
                </div>
                
                <div style='background: #e6f3ff; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                    <h3>Certificate Verification</h3>
                    <p>Your certificate can be verified using this ID: <strong>" . htmlspecialchars($certificateCode) . "</strong></p>
                    <p>Visit: <a href='http://localhost/Creators-Space-GroupProject/frontend/certificate.php'>Certificate Verification Portal</a></p>
                </div>
            </div>
            
            <div style='text-align: center; color: #666; font-size: 12px; margin-top: 30px;'>
                <p>This certificate was issued by Creators Space</p>
                <p>Thank you for choosing us for your learning journey!</p>
            </div>
        </body>
        </html>";
        
        // Try to send email
        ini_set('SMTP', 'localhost');
        ini_set('smtp_port', 25);
        ini_set('sendmail_from', $senderEmail);
        
        $mailSent = @mail($recipientEmail, $subject, $htmlContent, $headers);
        
        if ($mailSent) {
            error_log("Certificate email sent successfully to: $recipientEmail (Certificate: $certificateCode)");
            return true;
        } else {
            error_log("PHP mail() failed - likely no mail server configured");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Email sending error: " . $e->getMessage());
        return false;
    }
}

function generateEmailHTML($recipientName, $courseName, $certificateCode, $certificatePath = '') {
    $verificationUrl = "http://localhost/Creators-Space-GroupProject/frontend/certificate.php"; // Update with your actual domain
    
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Certificate of Completion</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 20px;
                color: #333;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            }
            .header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 40px 30px;
                text-align: center;
                color: white;
            }
            .header h1 {
                margin: 0;
                font-size: 2.2rem;
                font-weight: 700;
                text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            }
            .header p {
                margin: 10px 0 0;
                font-size: 1.1rem;
                opacity: 0.9;
            }
            .content {
                padding: 40px 30px;
            }
            .congratulations {
                text-align: center;
                margin-bottom: 30px;
            }
            .congratulations h2 {
                color: #667eea;
                font-size: 1.8rem;
                margin: 0 0 15px;
                font-weight: 600;
            }
            .congratulations p {
                font-size: 1.1rem;
                color: #555;
                line-height: 1.6;
            }
            .certificate-info {
                background: linear-gradient(135deg, rgba(102,126,234,0.1) 0%, rgba(118,75,162,0.1) 100%);
                border-radius: 15px;
                padding: 30px;
                margin: 30px 0;
                border-left: 5px solid #667eea;
            }
            .certificate-info h3 {
                color: #667eea;
                margin: 0 0 20px;
                font-size: 1.4rem;
                font-weight: 600;
            }
            .info-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid rgba(102,126,234,0.2);
            }
            .info-row:last-child {
                border-bottom: none;
            }
            .info-label {
                font-weight: 600;
                color: #333;
            }
            .info-value {
                color: #667eea;
                font-weight: 600;
            }
            .verification-section {
                background: #f8f9fa;
                border-radius: 15px;
                padding: 25px;
                margin: 30px 0;
                text-align: center;
            }
            .verification-section h3 {
                color: #333;
                margin: 0 0 15px;
                font-size: 1.3rem;
            }
            .verification-code {
                background: #667eea;
                color: white;
                padding: 12px 20px;
                border-radius: 8px;
                font-family: monospace;
                font-size: 1.2rem;
                font-weight: bold;
                letter-spacing: 2px;
                margin: 15px 0;
                display: inline-block;
            }
            .btn {
                display: inline-block;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                margin: 10px;
                box-shadow: 0 4px 15px rgba(102,126,234,0.3);
                transition: all 0.3s ease;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102,126,234,0.4);
            }
            .footer {
                background: #f8f9fa;
                padding: 30px;
                text-align: center;
                color: #666;
            }
            .footer p {
                margin: 5px 0;
                font-size: 0.9rem;
            }
            .social-links {
                margin: 20px 0;
            }
            .social-links a {
                color: #667eea;
                text-decoration: none;
                margin: 0 10px;
                font-weight: 500;
            }
            @media (max-width: 600px) {
                .container {
                    margin: 10px;
                    border-radius: 15px;
                }
                .header, .content, .footer {
                    padding: 25px 20px;
                }
                .header h1 {
                    font-size: 1.8rem;
                }
                .info-row {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 5px;
                }
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎓 Creators Space</h1>
                <p>Empowering Future Creators</p>
            </div>
            
            <div class='content'>
                <div class='congratulations'>
                    <h2>🎉 Congratulations, " . htmlspecialchars($recipientName) . "!</h2>
                    <p>You have successfully completed your course and earned your official certificate!</p>
                </div>
                
                <div class='certificate-info'>
                    <h3>📜 Certificate Details</h3>
                    <div class='info-row'>
                        <span class='info-label'>Course:</span>
                        <span class='info-value'>" . htmlspecialchars($courseName) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Student:</span>
                        <span class='info-value'>" . htmlspecialchars($recipientName) . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Completion Date:</span>
                        <span class='info-value'>" . date('F j, Y') . "</span>
                    </div>
                    <div class='info-row'>
                        <span class='info-label'>Certificate ID:</span>
                        <span class='info-value'>" . htmlspecialchars($certificateCode) . "</span>
                    </div>
                </div>
                
                <div class='verification-section'>
                    <h3>🔐 Certificate Verification</h3>
                    <p>Your certificate can be verified using this unique ID:</p>
                    <div class='verification-code'>" . htmlspecialchars($certificateCode) . "</div>
                    <p>Visit our verification portal to confirm authenticity:</p>
                    <a href='" . $verificationUrl . "' class='btn'>Verify Certificate</a>
                </div>
                
                <div style='text-align: center; margin-top: 30px;'>
                    <p><strong>📎 Your certificate is attached to this email.</strong></p>
                    <p>You can download, print, or share it as needed for your professional portfolio.</p>" 
                    . (!empty($certificatePath) ? "<a href='http://localhost/Creators-Space-GroupProject/storage/certificates/" . basename($certificatePath) . "' class='btn'>View Certificate Online</a>" : "") . "
                </div>
            </div>
            
            <div class='footer'>
                <p><strong>Thank you for choosing Creators Space!</strong></p>
                <p>Continue your learning journey with our other courses.</p>
                <div class='social-links'>
                    <a href='#'>Visit Our Website</a> |
                    <a href='#'>Browse More Courses</a> |
                    <a href='#'>Contact Support</a>
                </div>
                <p style='margin-top: 20px; font-size: 0.8rem; color: #999;'>
                    This certificate was issued by Creators Space. For support, please contact us at certificates@creatorsspace.com
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
}

// PHPMailer function for sending certificate emails
function sendCertificateEmailPHPMailer($recipientEmail, $recipientName, $courseName, $certificateCode, $certificatePath, $courseLevel = 'Advanced') {
    try {
        // Load email configuration
        $config = require __DIR__ . '/../config/email_config.php';
        
        // Generate shareable certificate image
        require_once __DIR__ . '/certificate_image_generator.php';
        
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = $config['smtp_auth'];
        $mail->Username = $config['smtp_username'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = $config['smtp_secure'];
        $mail->Port = $config['smtp_port'];
        
        // Enable debugging if needed (set to 2 for verbose debugging)
        $mail->SMTPDebug = 0;
        
        // Set timeout
        $mail->Timeout = 30;
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($recipientEmail, $recipientName);
        $mail->addReplyTo($config['from_email'], $config['from_name']);
        
        // Generate and attach shareable certificate image
        try {
            // Use the provided course level
            $shareableCertPath = generateCertificateImage($certificateCode, $recipientName, $courseName, $courseLevel);
            
            if ($shareableCertPath && file_exists($shareableCertPath)) {
                $imageExtension = pathinfo($shareableCertPath, PATHINFO_EXTENSION);
                $shareableAttachmentName = "Certificate_" . $certificateCode . "_Shareable." . $imageExtension;
                $mail->addAttachment($shareableCertPath, $shareableAttachmentName);
                error_log("Added shareable certificate attachment: " . $shareableCertPath);
            } else {
                error_log("Failed to generate shareable certificate image, using original file");
            }
        } catch (Exception $e) {
            error_log("Shareable certificate generation failed: " . $e->getMessage());
            // Continue with original certificate if image generation fails
        }
        
        // Also attach original certificate file
        if (file_exists($certificatePath)) {
            $fileExtension = pathinfo($certificatePath, PATHINFO_EXTENSION);
            $attachmentName = "Certificate_" . $certificateCode . "_Original." . $fileExtension;
            $mail->addAttachment($certificatePath, $attachmentName);
        }
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Congratulations! Your Certificate of Completion - ' . $courseName;
        $mail->Body = generateCertificateEmailHTML($recipientName, $courseName, $certificateCode);
        
        // Alternative plain text version
        $mail->AltBody = "Congratulations {$recipientName}! You have successfully completed the course '{$courseName}'. Your certificate ID is: {$certificateCode}. Please visit our website to download your certificate.";
        
        // Send the email
        $result = $mail->send();
        
        if ($result) {
            error_log("Certificate email sent successfully to: $recipientEmail (Certificate: $certificateCode)");
            return true;
        } else {
            error_log("PHPMailer failed to send email to: $recipientEmail");
            return false;
        }
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $e->getMessage());
        
        // Additional debugging information
        if (isset($mail)) {
            error_log("PHPMailer ErrorInfo: " . $mail->ErrorInfo);
        }
        
        return false;
    }
}

// Generate professional HTML email content
function generateCertificateEmailHTML($recipientName, $courseName, $certificateCode) {
    $verificationUrl = "http://localhost/Creators-Space-GroupProject/frontend/certificate.php";
    $currentDate = date('F j, Y');
    
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Your Certificate - Creators Space</title>
    </head>
    <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
        <div style='max-width: 600px; margin: 0 auto; background-color: white;'>
            <!-- Header -->
            <div style='background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 40px 30px; text-align: center;'>
                <h1 style='margin: 0; font-size: 28px; font-weight: bold;'>Congratulations!</h1>
                <p style='margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;'>You've earned your certificate!</p>
            </div>
            
            <!-- Main Content -->
            <div style='padding: 40px 30px;'>
                <h2 style='color: #333; margin-bottom: 20px;'>Certificate Details</h2>
                
                <div style='background: #f8f9fa; padding: 25px; border-radius: 10px; border-left: 4px solid #667eea; margin-bottom: 30px;'>
                    <p style='margin: 5px 0; font-size: 16px;'><strong>Student:</strong> " . htmlspecialchars($recipientName) . "</p>
                    <p style='margin: 5px 0; font-size: 16px;'><strong>Course:</strong> " . htmlspecialchars($courseName) . "</p>
                    <p style='margin: 5px 0; font-size: 16px;'><strong>Certificate ID:</strong> " . htmlspecialchars($certificateCode) . "</p>
                    <p style='margin: 5px 0; font-size: 16px;'><strong>Issue Date:</strong> {$currentDate}</p>
                </div>
                
                <!-- Verification Section -->
                <div style='background: #e3f2fd; padding: 25px; border-radius: 10px; margin-bottom: 30px;'>
                    <h3 style='color: #1976d2; margin-top: 0; margin-bottom: 15px;'>Certificate Verification</h3>
                    <p style='margin: 10px 0; color: #555;'>Your certificate can be verified using this unique ID:</p>
                    <p style='margin: 10px 0; font-size: 18px; font-weight: bold; color: #1976d2;'>{$certificateCode}</p>
                    <p style='margin: 10px 0; color: #555;'>
                        Visit our <a href='{$verificationUrl}' style='color: #1976d2; text-decoration: none; font-weight: bold;'>Certificate Verification Portal</a> to verify this certificate.
                    </p>
                </div>
                
                <!-- Call to Action -->
                <div style='text-align: center; margin: 30px 0;'>
                    <div style='background: #e8f4fd; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #3498db;'>
                        <h3 style='color: #2980b9; margin-top: 0; margin-bottom: 15px;'>Certificate Attachments</h3>
                        <p style='color: #555; margin: 10px 0;'><strong>Shareable Certificate Image</strong> - Perfect for social media and printing</p>
                        <p style='color: #555; margin: 10px 0;'><strong>Original Certificate File</strong> - Full detailed version</p>
                        <p style='color: #666; font-size: 14px; margin-top: 15px;'>Download the attachments to save and share your achievement!</p>
                    </div>
                    <p style='color: #666; margin-bottom: 20px;'>You can also view your certificate online:</p>
                    <a href='{$verificationUrl}' 
                       style='background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold; font-size: 16px;'>
                        View Certificate Online
                    </a>
                </div>
                
                <!-- Success Message -->
                <div style='background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #4caf50; margin: 30px 0;'>
                    <h3 style='color: #2e7d32; margin-top: 0;'>Achievement Unlocked!</h3>
                    <p style='color: #2e7d32; margin-bottom: 0;'>You have successfully completed <strong>{$courseName}</strong>. This certificate represents your dedication to learning and skill development. Well done!</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div style='background: #f8f9fa; padding: 25px 30px; text-align: center; border-top: 1px solid #dee2e6;'>
                <p style='margin: 0; color: #6c757d; font-size: 14px;'>
                    This certificate was issued by <strong>Creators Space</strong><br>
                    Thank you for choosing us for your learning journey!
                </p>
                <div style='margin-top: 15px;'>
                    <a href='#' style='color: #667eea; text-decoration: none; margin: 0 10px; font-size: 14px;'>Browse Courses</a> |
                    <a href='#' style='color: #667eea; text-decoration: none; margin: 0 10px; font-size: 14px;'>Contact Support</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>