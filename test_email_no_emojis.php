<?php
// Quick test of email template without emojis
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Test Email Template - No Emojis</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .status { padding: 15px; border-radius: 5px; margin: 10px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
    </style>
</head>
<body>
    <h1>Testing Email Template Without Emojis</h1>";

try {
    // Load email service
    require_once __DIR__ . '/backend/lib/email_service.php';
    
    echo "<div class='status success'>✅ Email service loaded</div>";
    
    // Generate sample email HTML to check if emojis are removed
    $sampleHTML = generateCertificateEmailHTML('Test Student', 'Sample Course', 'TEST123');
    
    echo "<div class='status info'>📋 Generated email template preview:</div>";
    
    // Show a preview of the email HTML
    echo "<div style='border: 2px solid #ddd; padding: 20px; margin: 20px 0; border-radius: 10px; background: #f9f9f9;'>";
    echo "<h3>Email Template Preview (First 500 characters):</h3>";
    echo "<div style='background: white; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; max-height: 200px; overflow-y: auto;'>";
    echo htmlspecialchars(substr($sampleHTML, 0, 500)) . "...";
    echo "</div></div>";
    
    // Check for emoji presence
    $hasEmojis = preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $sampleHTML);
    
    if ($hasEmojis) {
        echo "<div class='status error'>❌ Emojis still found in email template!</div>";
    } else {
        echo "<div class='status success'>✅ No emojis found in email template - Clean!</div>";
    }
    
    // Show the full email in an iframe for preview
    echo "<div class='status info'>🔍 Full email preview:</div>";
    echo "<iframe srcdoc='" . htmlspecialchars($sampleHTML) . "' style='width: 100%; height: 400px; border: 2px solid #ddd; border-radius: 10px;'></iframe>";
    
} catch (Exception $e) {
    echo "<div class='status error'>💥 Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "
    <div style='margin-top: 30px; padding: 20px; background: #e3f2fd; border-radius: 10px;'>
        <h3>Changes Made:</h3>
        <ul>
            <li>❌ Removed 🎉 from \"Congratulations!\" headings</li>
            <li>❌ Removed 🔐 from \"Certificate Verification\" sections</li>
            <li>❌ Removed 📎 from \"Certificate Attachments\" section</li>
            <li>❌ Removed 📜 from \"View Certificate Online\" buttons</li>
            <li>❌ Removed 🎓 from \"Achievement Unlocked\" section</li>
            <li>❌ Removed 🎓 from certificate image templates</li>
            <li>❌ Removed 📸 from screenshot instructions</li>
            <li>✅ Email subject line cleaned</li>
            <li>✅ All email content now emoji-free</li>
        </ul>
        <p><strong>Result:</strong> Professional, clean email template without any emojis while maintaining all functionality.</p>
    </div>
</body>
</html>";
?>