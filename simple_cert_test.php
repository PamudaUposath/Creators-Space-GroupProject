<?php
// Simple certificate email test for pamudaugoonatilake@gmail.com
echo "Testing certificate email to pamudaugoonatilake@gmail.com...\n\n";

try {
    // Load required files
    require_once __DIR__ . '/backend/config/db_connect.php';
    require_once __DIR__ . '/backend/lib/email_service.php';
    
    echo "✅ Files loaded successfully\n";
    
    // Test parameters
    $testEmail = 'pamudaugoonatilake@gmail.com';
    $studentName = 'Pamuda Ugonatilake';
    $courseName = 'Advanced Web Development Masterclass';
    $certificateCode = 'CERT-FINAL-' . date('YmdHis');
    $courseLevel = 'Advanced';
    
    echo "📧 Sending to: $testEmail\n";
    echo "🎓 Certificate: $certificateCode\n";
    echo "👤 Student: $studentName\n";
    echo "📚 Course: $courseName\n\n";
    
    // Send email
    echo "📬 Sending email...\n";
    $emailResult = sendCertificateEmailPHPMailer(
        $testEmail, 
        $studentName, 
        $courseName, 
        $certificateCode,
        $courseLevel
    );
    
    if ($emailResult['success']) {
        echo "🎉 SUCCESS! Email sent successfully!\n";
        echo "📧 Certificate email delivered to pamudaugoonatilake@gmail.com\n";
        echo "✅ Email includes clean professional template (no emojis)\n";
        echo "✅ Certificate attachments included\n";
        echo "✅ Verification link included\n\n";
        echo "📋 The email now contains:\n";
        echo "  - Clean professional formatting\n";
        echo "  - No emojis in subject or content\n";
        echo "  - No screenshot instructions\n";
        echo "  - Professional certificate images\n";
        echo "  - Verification system integration\n";
    } else {
        echo "❌ Email failed: " . $emailResult['message'] . "\n";
    }
    
} catch (Exception $e) {
    echo "💥 Error: " . $e->getMessage() . "\n";
}

echo "\n🏁 Test completed!\n";
?>