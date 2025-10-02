<?php
// Test email functionality
require_once __DIR__ . '/../lib/email_helper.php';

// Test email sending
echo "Testing email functionality...\n";

$test_result = sendEmailWithPHPMailer(
    'test@example.com', 
    'Test User', 
    'Test Email from Creators-Space', 
    'This is a test email to verify the email system is working.'
);

if ($test_result) {
    echo "✅ Email system is working correctly!\n";
} else {
    echo "❌ Email system has issues. Check configuration.\n";
}

echo "\nCheck backend/logs/emails.log for logged emails if using placeholder credentials.\n";
?>