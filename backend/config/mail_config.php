<?php
// backend/config/mail_config.php
// Mail configuration for different environments

function configureMailSettings() {
    // Check if we're on localhost/development environment
    $isLocalhost = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1', '::1']) || 
                   strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false;
    
    if ($isLocalhost) {
        // For XAMPP/localhost development - use a fake sendmail
        ini_set('SMTP', 'localhost');
        ini_set('smtp_port', 25);
        ini_set('sendmail_from', 'noreply@localhost');
        
        // Alternative: You can use a service like Mailtrap for testing
        // Uncomment and configure these if you want to use Mailtrap:
        /*
        ini_set('SMTP', 'smtp.mailtrap.io');
        ini_set('smtp_port', 2525);
        ini_set('sendmail_from', 'your-email@example.com');
        // Note: You'll also need to set username/password for authentication
        */
        
        return [
            'method' => 'php_mail',
            'configured' => false,
            'message' => 'Mail server not configured for localhost. Emails will be simulated.'
        ];
    } else {
        // For production - use proper SMTP settings
        return [
            'method' => 'smtp',
            'configured' => true,
            'message' => 'Production mail configuration'
        ];
    }
}

// Function to send certificate notification via alternative methods
function sendCertificateNotification($recipientEmail, $recipientName, $courseName, $certificateCode, $certificateUrl) {
    $config = configureMailSettings();
    
    if (!$config['configured']) {
        // For localhost - create a notification file instead
        $notificationDir = __DIR__ . '/../../storage/notifications/';
        if (!is_dir($notificationDir)) {
            mkdir($notificationDir, 0755, true);
        }
        
        $notificationFile = $notificationDir . 'certificate_notification_' . $certificateCode . '.txt';
        
        $content = "Certificate Notification\n";
        $content .= "======================\n";
        $content .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Student: $recipientName\n";
        $content .= "Email: $recipientEmail\n";
        $content .= "Course: $courseName\n";
        $content .= "Certificate ID: $certificateCode\n";
        $content .= "Certificate URL: $certificateUrl\n";
        $content .= "\nNote: This notification was created because email server is not configured on localhost.\n";
        $content .= "In production, this would be sent as an email to the student.\n";
        
        file_put_contents($notificationFile, $content);
        
        return [
            'success' => true,
            'method' => 'file',
            'message' => 'Notification saved to file (localhost mode)',
            'file' => $notificationFile
        ];
    }
    
    return [
        'success' => false,
        'method' => 'none',
        'message' => 'No notification method available'
    ];
}

// Instructions for setting up mail in XAMPP
function getMailSetupInstructions() {
    return "
    <div style='background: #f0f8ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>
        <h3>📧 Mail Configuration for XAMPP</h3>
        <p>To enable email sending in XAMPP, you have several options:</p>
        
        <h4>Option 1: Use Gmail SMTP (Recommended for testing)</h4>
        <ol>
            <li>Enable 2-factor authentication in your Gmail account</li>
            <li>Generate an App Password for your Gmail account</li>
            <li>Install PHPMailer using Composer: <code>composer require phpmailer/phpmailer</code></li>
            <li>Configure PHPMailer with your Gmail credentials</li>
        </ol>
        
        <h4>Option 2: Use Mailtrap (Free testing service)</h4>
        <ol>
            <li>Sign up at <a href='https://mailtrap.io'>mailtrap.io</a></li>
            <li>Get your SMTP credentials from the dashboard</li>
            <li>Update php.ini with Mailtrap settings</li>
        </ol>
        
        <h4>Option 3: Configure Mercury Mail (XAMPP built-in)</h4>
        <ol>
            <li>Start Mercury Mail from XAMPP Control Panel</li>
            <li>Configure Mercury Mail settings</li>
            <li>Update php.ini SMTP settings</li>
        </ol>
        
        <p><strong>For now:</strong> The system will generate certificates successfully, and you can view them directly via the provided links.</p>
    </div>
    ";
}
?>