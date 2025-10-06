<?php
// Test email functionality
header('Content-Type: text/html; charset=UTF-8');

// Start HTML output
echo "<!DOCTYPE html>
<html>
<head>
    <title>Email Configuration Test - Creators Space</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        code { background: #f4f4f4; padding: 2px 5px; border-radius: 3px; }
        .config-box { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <h1>📧 Email Configuration Test</h1>
    <p>Testing email functionality for certificate delivery...</p>";

try {
    // Load required files
    require_once __DIR__ . '/backend/lib/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/SMTP.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/Exception.php';
    require_once __DIR__ . '/backend/lib/email_service.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    echo "<div class='status success'>✅ PHPMailer libraries loaded successfully</div>";

    // Load configuration
    $config = require __DIR__ . '/backend/config/email_config.php';
    
    echo "<div class='status success'>✅ Email configuration loaded</div>";
    
    echo "<div class='config-box'>
        <h3>📋 Current Email Configuration</h3>
        <p><strong>SMTP Host:</strong> " . htmlspecialchars($config['smtp_host']) . "</p>
        <p><strong>SMTP Port:</strong> " . htmlspecialchars($config['smtp_port']) . "</p>
        <p><strong>SMTP Security:</strong> " . htmlspecialchars($config['smtp_secure']) . "</p>
        <p><strong>Username:</strong> " . htmlspecialchars($config['smtp_username']) . "</p>
        <p><strong>From Email:</strong> " . htmlspecialchars($config['from_email']) . "</p>
        <p><strong>From Name:</strong> " . htmlspecialchars($config['from_name']) . "</p>
    </div>";

    // Test SMTP connection
    echo "<h2>🔧 Testing SMTP Connection...</h2>";
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = $config['smtp_auth'];
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = $config['smtp_secure'];
    $mail->Port = $config['smtp_port'];
    $mail->Timeout = 10;
    
    // Test connection without sending
    if ($mail->smtpConnect()) {
        echo "<div class='status success'>✅ SMTP connection successful!</div>";
        $mail->smtpClose();
        
        // If we have a test parameter, send a test email
        if (isset($_GET['test_email']) && !empty($_GET['test_email'])) {
            $testEmail = filter_var($_GET['test_email'], FILTER_VALIDATE_EMAIL);
            
            if ($testEmail) {
                echo "<h2>📬 Sending Test Email...</h2>";
                
                // Configure test email
                $mail->clearAddresses();
                $mail->setFrom($config['from_email'], $config['from_name']);
                $mail->addAddress($testEmail);
                
                $mail->isHTML(true);
                $mail->Subject = 'Test Email - Creators Space Certificate System';
                $mail->Body = "
                    <h2>🎉 Email Configuration Test Successful!</h2>
                    <p>If you're reading this, your email configuration is working correctly.</p>
                    <p><strong>Test Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                    <p><strong>Configuration:</strong> " . htmlspecialchars($config['smtp_host']) . ":" . htmlspecialchars($config['smtp_port']) . "</p>
                    <hr>
                    <p><small>This is a test email from the Creators Space certificate system.</small></p>
                ";
                
                if ($mail->send()) {
                    echo "<div class='status success'>✅ Test email sent successfully to: " . htmlspecialchars($testEmail) . "</div>";
                } else {
                    echo "<div class='status error'>❌ Failed to send test email: " . $mail->ErrorInfo . "</div>";
                }
            } else {
                echo "<div class='status error'>❌ Invalid email address provided</div>";
            }
        } else {
            echo "<div class='status info'>
                📧 <strong>Ready to send test email</strong><br>
                Add <code>?test_email=your-email@example.com</code> to this URL to send a test email.
            </div>";
        }
        
    } else {
        echo "<div class='status error'>❌ SMTP connection failed</div>";
        echo "<div class='status warning'>
            <strong>Possible issues:</strong><br>
            • Check your email credentials in <code>backend/config/email_config.php</code><br>
            • Make sure you're using an App Password (not regular password) for Gmail<br>
            • Verify SMTP settings for your email provider<br>
            • Check if your firewall allows outgoing connections on port " . $config['smtp_port'] . "
        </div>";
    }

} catch (Exception $e) {
    echo "<div class='status error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    
    echo "<div class='status warning'>
        <strong>Setup Instructions:</strong><br>
        1. Update credentials in <code>backend/config/email_config.php</code><br>
        2. For Gmail: Enable 2-factor auth and generate an App Password<br>
        3. Use the App Password (16 characters) instead of your regular password<br>
        4. Make sure PHPMailer files exist in <code>backend/lib/PHPMailer/</code>
    </div>";
}

echo "
    <h2>🔧 Quick Setup Guide</h2>
    <div class='config-box'>
        <h3>For Gmail Setup:</h3>
        <ol>
            <li>Go to <a href='https://myaccount.google.com/' target='_blank'>Google Account Settings</a></li>
            <li>Enable <strong>2-Step Verification</strong></li>
            <li>Go to <strong>App passwords</strong> and generate a new password</li>
            <li>Update <code>backend/config/email_config.php</code> with:
                <ul>
                    <li><code>smtp_username</code>: Your Gmail address</li>
                    <li><code>smtp_password</code>: The 16-character App Password</li>
                </ul>
            </li>
        </ol>
        
        <h3>Test Your Setup:</h3>
        <p>Visit: <code>http://localhost/Creators-Space-GroupProject/test_email_config.php?test_email=your-email@example.com</code></p>
    </div>
</body>
</html>";
?>