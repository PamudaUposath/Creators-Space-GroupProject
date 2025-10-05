<?php
// Quick test script to send certificate email to piyal@mailinator.com
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Send Test Email to piyal@mailinator.com</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>📧 Sending Test Certificate Email</h1>
    <p>Testing email delivery to: <strong>piyal@mailinator.com</strong></p>";

try {
    // Load required files
    require_once __DIR__ . '/backend/lib/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/SMTP.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/Exception.php';
    require_once __DIR__ . '/backend/lib/email_service.php';
    require_once __DIR__ . '/backend/lib/certificate_html_generator.php';

    echo "<div class='status success'>✅ All required files loaded successfully</div>";

    // Test email details
    $testEmail = 'piyal@mailinator.com';
    $studentName = 'Piyal Test Student';
    $courseName = 'Advanced Web Development';
    $certificateCode = 'TEST_' . date('YmdHis') . '_PIYAL';

    echo "<div class='status info'>📋 Test Details:
Email: {$testEmail}
Student: {$studentName}
Course: {$courseName}
Certificate Code: {$certificateCode}</div>";

    // Generate test certificate
    echo "<div class='status info'>🎓 Generating test certificate...</div>";
    $certPath = generateCertificateHTML($certificateCode, $studentName, $courseName, 'Advanced');
    
    if (file_exists($certPath)) {
        echo "<div class='status success'>✅ Certificate generated successfully
Path: {$certPath}
Size: " . number_format(filesize($certPath)) . " bytes</div>";
    } else {
        throw new Exception("Certificate generation failed");
    }

    // Insert test certificate into database for verification
    echo "<div class='status info'>💾 Creating database record for verification...</div>";
    
    require_once __DIR__ . '/backend/config/db_connect.php';
    // $pdo is now available from db_connect.php
    
    // Check if test user exists, create if not
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $testUser = $stmt->fetch();
    
    if (!$testUser) {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, role, created_at) VALUES (?, ?, ?, 'student', NOW())");
        $stmt->execute(['Piyal', 'Test Student', $testEmail]);
        $testUserId = $pdo->lastInsertId();
        echo "<div class='status info'>👤 Created test user with ID: {$testUserId}</div>";
    } else {
        $testUserId = $testUser['id'];
        echo "<div class='status info'>👤 Using existing test user ID: {$testUserId}</div>";
    }
    
    // Check if test course exists, create if not
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ?");
    $stmt->execute([$courseName]);
    $testCourse = $stmt->fetch();
    
    if (!$testCourse) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, level, category, duration, instructor_id, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$courseName, 'Test course for certificate verification', 'advanced', 'web development', '40 hours']);
        $testCourseId = $pdo->lastInsertId();
        echo "<div class='status info'>📚 Created test course with ID: {$testCourseId}</div>";
    } else {
        $testCourseId = $testCourse['id'];
        echo "<div class='status info'>📚 Using existing test course ID: {$testCourseId}</div>";
    }
    
    // Create enrollment if not exists
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$testUserId, $testCourseId]);
    $enrollment = $stmt->fetch();
    
    if (!$enrollment) {
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, status, progress, enrolled_at, completed_at) VALUES (?, ?, 'completed', 100, NOW(), NOW())");
        $stmt->execute([$testUserId, $testCourseId]);
        echo "<div class='status info'>🎯 Created test enrollment</div>";
    }
    
    // Insert certificate record
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE certificate_code = ?"); // Remove any existing
    $stmt->execute([$certificateCode]);
    
    $stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$testUserId, $testCourseId, $certificateCode]);
    
    echo "<div class='status success'>✅ Certificate record created in database
Certificate can now be verified!</div>";

    // Send test email with shareable certificate
    echo "<div class='status info'>📬 Sending certificate email with shareable image...</div>";
    
    $emailResult = sendCertificateEmail($testEmail, $studentName, $courseName, $certificateCode, $certPath, 'Advanced');
    
    if ($emailResult) {
        echo "<div class='status success'>🎉 SUCCESS! Test email sent to piyal@mailinator.com
        
✅ Certificate email delivered successfully!
📧 Check your mailinator inbox: https://www.mailinator.com/v4/public/inboxes.jsp?to=piyal

The email contains:
- Professional certificate email template
- Certificate file attachment
- Verification details
- Certificate code: {$certificateCode}</div>";
        
        // Test certificate verification
        echo "<div class='status info'>🔍 Testing certificate verification...</div>";
        
        $verificationUrl = "http://localhost/Creators-Space-GroupProject/backend/api/verify_certificate.php?id=" . urlencode($certificateCode);
        $verificationResponse = @file_get_contents($verificationUrl);
        
        if ($verificationResponse) {
            $verificationData = json_decode($verificationResponse, true);
            if ($verificationData && $verificationData['success'] && $verificationData['verified']) {
                echo "<div class='status success'>✅ Certificate verification working!
Verification Response: Certificate verified successfully for {$studentName}</div>";
            } else {
                echo "<div class='status error'>❌ Certificate verification failed
Response: " . htmlspecialchars($verificationResponse) . "</div>";
            }
        } else {
            echo "<div class='status error'>❌ Could not test certificate verification</div>";
        }
        
        echo "<div class='status info'>🔗 Quick Links:
• Mailinator Inbox: <a href='https://www.mailinator.com/v4/public/inboxes.jsp?to=piyal' target='_blank'>Check piyal@mailinator.com</a>
• Certificate File: <a href='storage/certificates/" . basename($certPath) . "' target='_blank'>View Certificate</a>
• Certificate Verification: <a href='frontend/certificate.php' target='_blank'>Verify Certificate</a>
• Test Verification: <a href='backend/api/verify_certificate.php?id={$certificateCode}' target='_blank'>API Test</a></div>";
        
    } else {
        echo "<div class='status error'>❌ Email sending failed
        
Please check:
- Email configuration in backend/config/email_config.php
- Gmail credentials and App Password
- SMTP connection settings
- Network connectivity</div>";
        
        // Show configuration for debugging
        $config = require __DIR__ . '/backend/config/email_config.php';
        echo "<div class='status info'>📋 Current Email Configuration:
Host: {$config['smtp_host']}
Port: {$config['smtp_port']}
Username: {$config['smtp_username']}
Security: {$config['smtp_secure']}</div>";
    }

} catch (Exception $e) {
    echo "<div class='status error'>💥 Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='code'>Stack Trace:
" . htmlspecialchars($e->getTraceAsString()) . "</div>";
}

echo "
    <hr>
    <h2>📝 What to Check Next</h2>
    <div class='status info'>
        1. <strong>Mailinator Inbox:</strong> Go to <a href='https://www.mailinator.com/v4/public/inboxes.jsp?to=piyal' target='_blank'>mailinator.com</a> and check the 'piyal' inbox<br>
        2. <strong>Email Content:</strong> Verify the email has proper formatting and certificate attachment<br>
        3. <strong>Certificate Verification:</strong> Test the certificate verification link in the email<br>
        4. <strong>Debugging:</strong> If failed, check the configuration and error messages above
    </div>
</body>
</html>";
?>