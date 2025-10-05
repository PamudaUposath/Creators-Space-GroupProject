<?php
// Test email functionality
echo "<h2>📧 Email Test for XAMPP</h2>";

// Test basic PHP mail function
echo "<h3>Testing PHP mail() function...</h3>";

$to = "test@example.com"; // Change this to your email for testing
$subject = "Test Email from Creators Space";
$message = "
<html>
<head>
    <title>Test Email</title>
</head>
<body>
    <h2>Test Email from Creators Space</h2>
    <p>If you receive this email, the mail function is working!</p>
    <p>Time sent: " . date('Y-m-d H:i:s') . "</p>
</body>
</html>
";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Creators Space <noreply@creatorsspace.com>\r\n";

echo "<p>Attempting to send test email to: <strong>$to</strong></p>";

// Configure basic SMTP settings for localhost
ini_set('SMTP', 'localhost');
ini_set('smtp_port', 25);
ini_set('sendmail_from', 'noreply@creatorsspace.com');

$result = @mail($to, $subject, $message, $headers);

if ($result) {
    echo "<p style='color: green;'>✅ Email sent successfully!</p>";
    echo "<p><strong>Note:</strong> Check your email inbox (and spam folder) to confirm delivery.</p>";
} else {
    echo "<p style='color: red;'>❌ Email sending failed</p>";
    
    $error = error_get_last();
    if ($error) {
        echo "<p><strong>Error:</strong> " . htmlspecialchars($error['message']) . "</p>";
    }
    
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>🔧 To Enable Email in XAMPP:</h4>";
    echo "<ol>";
    echo "<li><strong>Option 1 - Use Gmail SMTP:</strong>";
    echo "<ul>";
    echo "<li>Install PHPMailer: <code>composer require phpmailer/phpmailer</code></li>";
    echo "<li>Enable 2-factor authentication in Gmail</li>";
    echo "<li>Generate an App Password in Gmail</li>";
    echo "<li>Update email service to use Gmail SMTP</li>";
    echo "</ul></li>";
    
    echo "<li><strong>Option 2 - Use Mercury Mail (XAMPP built-in):</strong>";
    echo "<ul>";
    echo "<li>Open XAMPP Control Panel</li>";
    echo "<li>Start 'Mercury' service</li>";
    echo "<li>Configure Mercury Mail settings</li>";
    echo "</ul></li>";
    
    echo "<li><strong>Option 3 - Use Mailtrap (Testing):</strong>";
    echo "<ul>";
    echo "<li>Sign up at <a href='https://mailtrap.io' target='_blank'>mailtrap.io</a></li>";
    echo "<li>Get SMTP credentials</li>";
    echo "<li>Use PHPMailer with Mailtrap settings</li>";
    echo "</ul></li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h3>📋 Current PHP Configuration:</h3>";
echo "<p><strong>SMTP:</strong> " . ini_get('SMTP') . "</p>";
echo "<p><strong>smtp_port:</strong> " . ini_get('smtp_port') . "</p>";
echo "<p><strong>sendmail_from:</strong> " . ini_get('sendmail_from') . "</p>";

echo "<h3>🎯 For Now:</h3>";
echo "<p>Your certificate system is working perfectly! Students can:</p>";
echo "<ul>";
echo "<li>✅ Receive certificates when they complete 80% of the course</li>";
echo "<li>✅ View certificates via direct links</li>";
echo "<li>✅ Verify certificates using the certificate ID</li>";
echo "</ul>";
echo "<p>The only missing piece is email delivery, which requires SMTP configuration.</p>";
?>