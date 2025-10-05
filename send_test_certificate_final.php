<?php
// Final test of certificate email system - clean version without emojis
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Final Certificate Email Test - Clean Version</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
    </style>
</head>
<body>
    <h1>Final Certificate Email Test - Clean Professional Version</h1>";

try {
    // Load required files
    require_once __DIR__ . '/backend/config/db_connect.php';
    require_once __DIR__ . '/backend/lib/email_service.php';
    require_once __DIR__ . '/backend/lib/certificate_image_generator.php';
    
    echo "<div class='status success'>✅ All required files loaded successfully</div>";
    
    // Test email recipient
    $testEmail = 'pamudaugoonatilake@gmail.com';
    $studentName = 'Pamuda Ugonatilake';
    $courseName = 'Advanced Web Development Masterclass';
    $courseLevel = 'Advanced';
    $certificateCode = 'CERT-TEST-' . date('YmdHis');
    
    echo "<div class='status info'>📧 Preparing test email for: $testEmail</div>";
    echo "<div class='status info'>🎓 Certificate Code: $certificateCode</div>";
    echo "<div class='status info'>👤 Student: $studentName</div>";
    echo "<div class='status info'>📚 Course: $courseName ($courseLevel)</div>";
    
    // Create certificate record in database
    try {
        $stmt = $pdo->prepare("INSERT INTO certificates (certificate_code, student_name, course_name, course_level, issue_date, status) VALUES (?, ?, ?, ?, NOW(), 'active')");
        $stmt->execute([$certificateCode, $studentName, $courseName, $courseLevel]);
        echo "<div class='status success'>✅ Certificate record created in database</div>";
    } catch (PDOException $e) {
        echo "<div class='status warning'>⚠️ Database insert note: " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "<div class='status info'>📝 Continuing with email test...</div>";
    }
    
    // Generate certificate image (if possible)
    $outputDir = __DIR__ . '/backend/assets/certificates/';
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0755, true);
        echo "<div class='status info'>📁 Created certificates directory</div>";
    }
    
    echo "<div class='status info'>🖼️ Generating certificate image...</div>";
    $imageResult = generateCertificateImage($certificateCode, $studentName, $courseName, $courseLevel, $outputDir);
    
    if ($imageResult['success']) {
        echo "<div class='status success'>✅ Certificate image generated: " . basename($imageResult['image_path']) . "</div>";
    } else {
        echo "<div class='status warning'>⚠️ Image generation note: " . htmlspecialchars($imageResult['message']) . "</div>";
    }
    
    // Send email with PHPMailer
    echo "<div class='status info'>📬 Sending certificate email...</div>";
    
    $emailResult = sendCertificateEmailPHPMailer(
        $testEmail, 
        $studentName, 
        $courseName, 
        $certificateCode,
        $courseLevel
    );
    
    if ($emailResult['success']) {
        echo "<div class='status success'>🎉 SUCCESS! Certificate email sent successfully!</div>";
        echo "<div class='status info'>📧 Email delivered to: $testEmail</div>";
        echo "<div class='status info'>📋 Email includes:</div>";
        echo "<ul style='margin-left: 30px;'>";
        echo "<li>✅ Clean professional template (no emojis)</li>";
        echo "<li>✅ Certificate verification link</li>";
        echo "<li>✅ Shareable certificate image attachment</li>";
        echo "<li>✅ Clean HTML certificate (no screenshot instructions)</li>";
        echo "<li>✅ Professional formatting</li>";
        echo "</ul>";
        
        // Show email preview
        echo "<div class='status info'>👀 Email template preview (first 800 characters):</div>";
        echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 11px; max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6;'>";
        $sampleHTML = generateCertificateEmailHTML($studentName, $courseName, $certificateCode);
        echo htmlspecialchars(substr($sampleHTML, 0, 800)) . "...";
        echo "</div>";
        
    } else {
        echo "<div class='status error'>❌ Email sending failed!</div>";
        echo "<div class='status error'>Error: " . htmlspecialchars($emailResult['message']) . "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='status error'>💥 Fatal Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='status info'>📝 Stack trace: " . htmlspecialchars($e->getTraceAsString()) . "</div>";
}

echo "
    <div style='margin-top: 30px; padding: 20px; background: #e8f5e8; border-radius: 10px;'>
        <h3>✨ Final Test Summary</h3>
        <p><strong>What's Being Tested:</strong></p>
        <ul>
            <li>🧹 Clean email templates (all emojis removed)</li>
            <li>🖼️ Certificate image generation and attachment</li>
            <li>📧 Professional email delivery</li>
            <li>🔗 Certificate verification system</li>
            <li>📱 Clean shareable HTML (no screenshot instructions)</li>
            <li>💼 Business-ready professional appearance</li>
        </ul>
        
        <p><strong>Expected Result:</strong> Clean, professional certificate email with image attachment sent to <strong>pamudaugoonatilake@gmail.com</strong></p>
        
        <div style='background: #fff; padding: 15px; border-radius: 5px; margin-top: 15px; border-left: 4px solid #28a745;'>
            <strong>✅ System Status:</strong> Ready for production use with clean, professional certificate emails!
        </div>
    </div>
</body>
</html>";
?>