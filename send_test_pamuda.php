<?php
// Send test certificate email to pamudaugoonatilake@gmail.com
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Send Certificate to pamudaugoonatilake@gmail.com</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>📧 Sending Certificate Email to Pamuda</h1>
    <p>Testing complete certificate system with shareable image to: <strong>pamudaugoonatilake@gmail.com</strong></p>";

try {
    // Load required files
    require_once __DIR__ . '/backend/lib/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/SMTP.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/Exception.php';
    require_once __DIR__ . '/backend/lib/email_service.php';
    require_once __DIR__ . '/backend/lib/certificate_html_generator.php';
    require_once __DIR__ . '/backend/lib/certificate_image_generator.php';

    echo "<div class='status success'>✅ All required libraries loaded successfully</div>";

    // Test email details for Pamuda
    $testEmail = 'pamudaugoonatilake@gmail.com';
    $studentName = 'Pamuda Ugoonatilake';
    $courseName = 'Full Stack Web Development';
    $courseLevel = 'Advanced';
    $certificateCode = 'CERT_PAMUDA_' . date('YmdHis');

    echo "<div class='status info'>📋 Certificate Details:
Email: {$testEmail}
Student: {$studentName}
Course: {$courseName}
Level: {$courseLevel}
Certificate Code: {$certificateCode}</div>";

    // Insert certificate into database for verification
    echo "<div class='status info'>💾 Creating database record...</div>";
    
    require_once __DIR__ . '/backend/config/db_connect.php';
    
    // Get or create test user for Pamuda
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$testEmail]);
    $testUser = $stmt->fetch();
    
    if (!$testUser) {
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, role, created_at) VALUES (?, ?, ?, 'student', NOW())");
        $stmt->execute(['Pamuda', 'Ugoonatilake', $testEmail]);
        $testUserId = $pdo->lastInsertId();
        echo "<div class='status info'>👤 Created new user record for Pamuda (ID: {$testUserId})</div>";
    } else {
        $testUserId = $testUser['id'];
        echo "<div class='status info'>👤 Using existing user record (ID: {$testUserId})</div>";
    }
    
    // Get or create test course
    $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = ?");
    $stmt->execute([$courseName]);
    $testCourse = $stmt->fetch();
    
    if (!$testCourse) {
        $stmt = $pdo->prepare("INSERT INTO courses (title, description, level, category, duration, instructor_id, created_at) VALUES (?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$courseName, 'Complete full-stack web development course', strtolower($courseLevel), 'web development', '60 hours']);
        $testCourseId = $pdo->lastInsertId();
        echo "<div class='status info'>📚 Created new course record (ID: {$testCourseId})</div>";
    } else {
        $testCourseId = $testCourse['id'];
        echo "<div class='status info'>📚 Using existing course record (ID: {$testCourseId})</div>";
    }
    
    // Create enrollment if not exists
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$testUserId, $testCourseId]);
    $enrollment = $stmt->fetch();
    
    if (!$enrollment) {
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, status, progress, enrolled_at, completed_at) VALUES (?, ?, 'completed', 100, NOW(), NOW())");
        $stmt->execute([$testUserId, $testCourseId]);
        echo "<div class='status info'>🎯 Created enrollment record</div>";
    }
    
    // Insert certificate record
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE certificate_code = ?"); // Remove any existing
    $stmt->execute([$certificateCode]);
    
    $stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$testUserId, $testCourseId, $certificateCode]);
    
    echo "<div class='status success'>✅ Certificate record created in database</div>";

    // Generate original certificate HTML
    echo "<div class='status info'>🎓 Generating certificate files...</div>";
    $certPath = generateCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel);
    
    if (file_exists($certPath)) {
        echo "<div class='status success'>✅ Original certificate generated
Path: " . htmlspecialchars(basename($certPath)) . "
Size: " . number_format(filesize($certPath)) . " bytes</div>";
    } else {
        throw new Exception("Original certificate generation failed");
    }

    // Generate shareable certificate image
    echo "<div class='status info'>🖼️ Generating shareable certificate image...</div>";
    $shareablePath = generateCertificateImage($certificateCode, $studentName, $courseName, $courseLevel);
    
    if ($shareablePath && file_exists($shareablePath)) {
        echo "<div class='status success'>✅ Shareable certificate image generated
Path: " . htmlspecialchars(basename($shareablePath)) . "
Size: " . number_format(filesize($shareablePath)) . " bytes</div>";
    } else {
        echo "<div class='status warning'>⚠️ Shareable certificate generation used fallback method</div>";
    }

    // Send comprehensive certificate email
    echo "<div class='status info'>📬 Sending certificate email with attachments...</div>";
    
    $emailResult = sendCertificateEmail($testEmail, $studentName, $courseName, $certificateCode, $certPath, $courseLevel);
    
    if ($emailResult) {
        echo "<div class='status success'>🎉 SUCCESS! Certificate email sent to pamudaugoonatilake@gmail.com
        
✅ Email delivered successfully!
📧 Check your Gmail inbox: pamudaugoonatilake@gmail.com

Email Contents:
• Professional HTML email template
• Shareable certificate image attachment (perfect for LinkedIn/social media)
• Original certificate file attachment
• Certificate verification details
• Certificate ID: {$certificateCode}

The email includes:
🖼️ High-quality shareable certificate image
📄 Complete certificate file for records
🔗 Online verification link
🆔 Unique certificate code for verification</div>";
        
        // Test certificate verification
        echo "<div class='status info'>🔍 Testing certificate verification...</div>";
        
        $verificationUrl = "http://localhost/Creators-Space-GroupProject/backend/api/verify_certificate.php?id=" . urlencode($certificateCode);
        $verificationResponse = @file_get_contents($verificationUrl);
        
        if ($verificationResponse) {
            $verificationData = json_decode($verificationResponse, true);
            if ($verificationData && $verificationData['success'] && $verificationData['verified']) {
                echo "<div class='status success'>✅ Certificate verification working perfectly!
Student: {$verificationData['data']['student_name']}
Course: {$verificationData['data']['course_name']}
Level: {$verificationData['data']['level']}</div>";
            } else {
                echo "<div class='status error'>❌ Certificate verification failed</div>";
            }
        }
        
        echo "<div class='status info'>🔗 Quick Links:
• Gmail Inbox: <a href='https://mail.google.com/' target='_blank'>Check Gmail</a>
• Certificate Files: <a href='storage/certificates/' target='_blank'>View Generated Files</a>
• Certificate Verification: <a href='frontend/certificate.php' target='_blank'>Verify Certificate</a>
• Direct API Test: <a href='backend/api/verify_certificate.php?id={$certificateCode}' target='_blank'>API Response</a></div>";
        
    } else {
        echo "<div class='status error'>❌ Email sending failed
        
Please check:
- Gmail SMTP credentials in backend/config/email_config.php
- Internet connection
- Gmail App Password validity
- SMTP server accessibility

Certificate files were generated successfully, only email delivery failed.</div>";
        
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
    <h2>📝 What to Check in Your Email</h2>
    <div class='status info'>
        <h3>📧 Email Should Contain:</h3>
        <ul>
            <li><strong>Professional Subject:</strong> 🎉 Congratulations! Your Certificate of Completion</li>
            <li><strong>Beautiful HTML Template:</strong> Branded Creators Space email design</li>
            <li><strong>Two Attachments:</strong>
                <ul>
                    <li>Certificate_[ID]_Shareable.html - Perfect for sharing</li>
                    <li>Certificate_[ID]_Original.html - Complete detailed version</li>
                </ul>
            </li>
            <li><strong>Certificate Details:</strong> Your name, course, certificate ID</li>
            <li><strong>Verification Link:</strong> Link to verify the certificate online</li>
        </ul>
        
        <h3>🎯 Actions to Take:</h3>
        <ol>
            <li>Check your Gmail inbox (including spam folder)</li>
            <li>Download both certificate attachments</li>
            <li>Open the shareable certificate - it's perfect for LinkedIn!</li>
            <li>Test the verification link</li>
            <li>Share your achievement! 🎓</li>
        </ol>
    </div>
</body>
</html>";
?>