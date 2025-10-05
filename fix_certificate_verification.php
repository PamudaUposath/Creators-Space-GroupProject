<?php
// Quick test to verify the certificate TEST_20251005163648_PIYAL
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Certificate Verification Test</title>
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
    <h1>🔍 Certificate Verification Debug</h1>";

try {
    // Connect to database
    require_once __DIR__ . '/backend/config/db_connect.php';
    // $pdo is now available from db_connect.php

    echo "<div class='status success'>✅ Database connection established</div>";

    // Test certificate ID
    $certificateId = 'TEST_20251005163648_PIYAL';
    
    echo "<div class='status info'>🎯 Testing certificate ID: {$certificateId}</div>";

    // Check if certificate exists in database
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.certificate_code,
            c.issued_at,
            u.first_name,
            u.last_name,
            u.email,
            co.title as course_title
        FROM certificates c
        LEFT JOIN users u ON c.user_id = u.id
        LEFT JOIN courses co ON c.course_id = co.id
        WHERE c.certificate_code = ?
    ");
    
    $stmt->execute([$certificateId]);
    $certificate = $stmt->fetch();

    if ($certificate) {
        echo "<div class='status success'>✅ Certificate found in database!
        
Certificate Details:
- ID: {$certificate['certificate_code']}
- Student: {$certificate['first_name']} {$certificate['last_name']}
- Email: {$certificate['email']}
- Course: {$certificate['course_title']}
- Issued: {$certificate['issued_at']}</div>";
        
        // Test API verification
        $verificationUrl = "http://localhost/Creators-Space-GroupProject/backend/api/verify_certificate.php?id=" . urlencode($certificateId);
        $verificationResponse = @file_get_contents($verificationUrl);
        
        if ($verificationResponse) {
            $verificationData = json_decode($verificationResponse, true);
            echo "<div class='status info'>🔧 API Response:</div>";
            echo "<div class='code'>" . json_encode($verificationData, JSON_PRETTY_PRINT) . "</div>";
            
            if ($verificationData && $verificationData['success'] && $verificationData['verified']) {
                echo "<div class='status success'>✅ Certificate verification API working correctly!</div>";
            } else {
                echo "<div class='status error'>❌ Certificate verification API failed</div>";
            }
        } else {
            echo "<div class='status error'>❌ Could not reach verification API</div>";
        }
        
    } else {
        echo "<div class='status error'>❌ Certificate NOT found in database</div>";
        
        // Create the certificate record
        echo "<div class='status info'>💾 Creating certificate record...</div>";
        
        // Get or create test user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'piyal@mailinator.com'");
        $stmt->execute();
        $user = $stmt->fetch();
        
        if (!$user) {
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, role, created_at) VALUES ('Piyal', 'Test Student', 'piyal@mailinator.com', 'student', NOW())");
            $stmt->execute();
            $userId = $pdo->lastInsertId();
        } else {
            $userId = $user['id'];
        }
        
        // Get or create test course
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE title = 'Advanced Web Development'");
        $stmt->execute();
        $course = $stmt->fetch();
        
        if (!$course) {
            $stmt = $pdo->prepare("INSERT INTO courses (title, description, level, category, duration, instructor_id, created_at) VALUES ('Advanced Web Development', 'Test course', 'advanced', 'web development', '40 hours', 1, NOW())");
            $stmt->execute();
            $courseId = $pdo->lastInsertId();
        } else {
            $courseId = $course['id'];
        }
        
        // Create certificate record
        $stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$userId, $courseId, $certificateId]);
        
        echo "<div class='status success'>✅ Certificate record created! Try verification again.</div>";
        
        // Test verification again
        $verificationUrl = "http://localhost/Creators-Space-GroupProject/backend/api/verify_certificate.php?id=" . urlencode($certificateId);
        $verificationResponse = @file_get_contents($verificationUrl);
        
        if ($verificationResponse) {
            $verificationData = json_decode($verificationResponse, true);
            echo "<div class='status info'>🔧 New API Response:</div>";
            echo "<div class='code'>" . json_encode($verificationData, JSON_PRETTY_PRINT) . "</div>";
        }
    }

    // Test with other certificate IDs mentioned
    echo "<hr><h2>🧪 Testing Sample Certificate IDs</h2>";
    
    $sampleIds = ['CERT-JS30-2024-001', 'CERT-FSWD-2024-002'];
    
    foreach ($sampleIds as $sampleId) {
        echo "<div class='status info'>Testing: {$sampleId}</div>";
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM certificates WHERE certificate_code = ?");
        $stmt->execute([$sampleId]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo "<div class='status success'>✅ {$sampleId} exists in database</div>";
        } else {
            echo "<div class='status error'>❌ {$sampleId} not found - creating sample record...</div>";
            
            // Create sample certificate
            $stmt = $pdo->prepare("INSERT INTO certificates (user_id, course_id, certificate_code, issued_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$userId, $courseId, $sampleId]);
            echo "<div class='status success'>✅ Created {$sampleId}</div>";
        }
    }

} catch (Exception $e) {
    echo "<div class='status error'>💥 Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "
    <hr>
    <h2>🔗 Quick Actions</h2>
    <div class='status info'>
        <a href='frontend/certificate.php' target='_blank' style='display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Test Certificate Verification Page</a>
        <a href='backend/api/verify_certificate.php?id=TEST_20251005163648_PIYAL' target='_blank' style='display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 5px;'>🔧 Test API Directly</a>
    </div>
</body>
</html>";
?>