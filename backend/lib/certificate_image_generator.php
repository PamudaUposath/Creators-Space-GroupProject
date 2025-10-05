<?php
// backend/lib/certificate_image_generator.php
// Generate shareable certificate images

function generateCertificateImage($certificateCode, $studentName, $courseName, $courseLevel) {
    try {
        // Create output directory if it doesn't exist
        $outputDir = __DIR__ . '/../../storage/certificates/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        // Try multiple approaches for image generation
        
        // Approach 1: Use wkhtmltoimage if available
        $imageFile = generateWithWkhtml($certificateCode, $studentName, $courseName, $courseLevel, $outputDir);
        
        if ($imageFile && file_exists($imageFile)) {
            return $imageFile;
        }
        
        // Approach 2: Generate CSS-styled HTML that can be screenshot
        $screenshotFile = generateScreenshotReadyHTML($certificateCode, $studentName, $courseName, $courseLevel, $outputDir);
        
        if ($screenshotFile && file_exists($screenshotFile)) {
            return $screenshotFile;
        }
        
        // Approach 3: Create a simple text-based image using basic PHP
        $textImageFile = generateTextBasedCertificate($certificateCode, $studentName, $courseName, $courseLevel, $outputDir);
        
        return $textImageFile;
        
    } catch (Exception $e) {
        error_log("Certificate image generation error: " . $e->getMessage());
        throw new Exception("Failed to generate certificate image: " . $e->getMessage());
    }
}

// Approach 1: Use wkhtmltoimage (if available)
function generateWithWkhtml($certificateCode, $studentName, $courseName, $courseLevel, $outputDir) {
    try {
        // Check if wkhtmltoimage is available
        $wkhtml = shell_exec('where wkhtmltoimage 2>NUL');
        if (!$wkhtml) {
            return false;
        }
        
        // Generate HTML content
        $htmlContent = generateShareableCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel);
        $htmlFile = $outputDir . 'temp_cert_' . $certificateCode . '.html';
        file_put_contents($htmlFile, $htmlContent);
        
        // Generate image
        $imageFile = $outputDir . 'certificate_' . $certificateCode . '.png';
        $command = "wkhtmltoimage --width 1200 --height 800 --format png \"$htmlFile\" \"$imageFile\"";
        
        exec($command, $output, $returnCode);
        
        // Clean up temp file
        unlink($htmlFile);
        
        if ($returnCode === 0 && file_exists($imageFile)) {
            return $imageFile;
        }
        
        return false;
        
    } catch (Exception $e) {
        return false;
    }
}

// Approach 2: Generate screenshot-ready HTML
function generateScreenshotReadyHTML($certificateCode, $studentName, $courseName, $courseLevel, $outputDir) {
    try {
        $htmlContent = generateShareableCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel);
        
        // No additional content needed - clean certificate ready for sharing
        
        $filename = 'shareable_certificate_' . $certificateCode . '.html';
        $outputPath = $outputDir . $filename;
        
        file_put_contents($outputPath, $htmlContent);
        
        return $outputPath;
        
    } catch (Exception $e) {
        return false;
    }
}

// Approach 3: Simple text-based certificate (fallback)
function generateTextBasedCertificate($certificateCode, $studentName, $courseName, $courseLevel, $outputDir) {
    try {
        // Create a simple HTML certificate that's easy to screenshot
        $htmlContent = generateSimpleTextCertificate($certificateCode, $studentName, $courseName, $courseLevel);
        
        $filename = 'text_certificate_' . $certificateCode . '.html';
        $outputPath = $outputDir . $filename;
        
        file_put_contents($outputPath, $htmlContent);
        
        return $outputPath;
        
    } catch (Exception $e) {
        throw $e;
    }
}

function generateShareableCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel) {
    $issueDate = date('F j, Y');
    
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Certificate - $certificateCode</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@400;500;600&display=swap');
            
            body {
                margin: 0;
                padding: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                font-family: 'Inter', sans-serif;
                min-height: calc(100vh - 40px);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .certificate {
                width: 1000px;
                height: 700px;
                background: white;
                border: 15px solid #2c3e50;
                border-radius: 20px;
                position: relative;
                box-shadow: 0 20px 40px rgba(0,0,0,0.3);
                overflow: hidden;
            }
            
            .certificate::before {
                content: '';
                position: absolute;
                top: 20px;
                left: 20px;
                right: 20px;
                bottom: 20px;
                border: 3px solid #3498db;
                border-radius: 10px;
            }
            
            .header {
                text-align: center;
                padding: 40px 60px 20px 60px;
                background: linear-gradient(135deg, #3498db, #2980b9);
                color: white;
                position: relative;
            }
            
            .logo {
                font-size: 28px;
                font-weight: 900;
                margin-bottom: 10px;
                font-family: 'Playfair Display', serif;
            }
            
            .subtitle {
                font-size: 14px;
                opacity: 0.9;
                letter-spacing: 2px;
                text-transform: uppercase;
            }
            
            .main-content {
                padding: 60px;
                text-align: center;
                background: white;
                position: relative;
                z-index: 2;
            }
            
            .certificate-title {
                font-family: 'Playfair Display', serif;
                font-size: 48px;
                font-weight: 700;
                color: #2c3e50;
                margin-bottom: 20px;
                letter-spacing: 2px;
            }
            
            .awarded-text {
                font-size: 18px;
                color: #7f8c8d;
                margin-bottom: 30px;
                font-weight: 500;
            }
            
            .student-name {
                font-family: 'Playfair Display', serif;
                font-size: 42px;
                font-weight: 700;
                color: #3498db;
                margin-bottom: 30px;
                text-decoration: underline;
                text-decoration-color: #e74c3c;
                text-decoration-thickness: 3px;
                text-underline-offset: 8px;
            }
            
            .course-info {
                margin-bottom: 40px;
            }
            
            .completion-text {
                font-size: 20px;
                color: #2c3e50;
                margin-bottom: 15px;
                font-weight: 600;
            }
            
            .course-name {
                font-family: 'Playfair Display', serif;
                font-size: 32px;
                font-weight: 700;
                color: #27ae60;
                margin-bottom: 10px;
            }
            
            .course-level {
                font-size: 16px;
                color: #f39c12;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 1px;
                background: rgba(243, 156, 18, 0.1);
                padding: 5px 15px;
                border-radius: 20px;
                display: inline-block;
            }
            
            .footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 50px;
                padding-top: 30px;
                border-top: 2px solid #ecf0f1;
            }
            
            .certificate-id {
                font-family: 'Inter', monospace;
                font-size: 14px;
                color: #7f8c8d;
                background: #ecf0f1;
                padding: 8px 12px;
                border-radius: 5px;
            }
            
            .issue-date {
                font-size: 16px;
                color: #2c3e50;
                font-weight: 600;
            }
            
            .signature-area {
                position: absolute;
                bottom: 40px;
                right: 80px;
                text-align: center;
            }
            
            .signature-line {
                width: 200px;
                height: 2px;
                background: #2c3e50;
                margin-bottom: 10px;
            }
            
            .signature-text {
                font-size: 12px;
                color: #7f8c8d;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            
            .decorative-corner {
                position: absolute;
                width: 100px;
                height: 100px;
                background: linear-gradient(45deg, #3498db, transparent);
                opacity: 0.1;
            }
            
            .decorative-corner.top-left {
                top: 0;
                left: 0;
                border-radius: 0 0 100px 0;
            }
            
            .decorative-corner.bottom-right {
                bottom: 0;
                right: 0;
                border-radius: 100px 0 0 0;
            }
            
            .verification-info {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                font-size: 10px;
                color: #95a5a6;
                text-align: center;
                line-height: 1.4;
            }
        </style>
    </head>
    <body>
        <div class='certificate'>
            <div class='decorative-corner top-left'></div>
            <div class='decorative-corner bottom-right'></div>
            
            <div class='header'>
                <div class='logo'>CREATORS SPACE</div>
                <div class='subtitle'>Certificate of Completion</div>
            </div>
            
            <div class='main-content'>
                <h1 class='certificate-title'>CERTIFICATE</h1>
                <p class='awarded-text'>This is to certify that</p>
                
                <div class='student-name'>$studentName</div>
                
                <div class='course-info'>
                    <p class='completion-text'>has successfully completed the course</p>
                    <div class='course-name'>$courseName</div>
                    <div class='course-level'>$courseLevel Level</div>
                </div>
                
                <div class='footer'>
                    <div class='certificate-id'>ID: $certificateCode</div>
                    <div class='issue-date'>Issued: $issueDate</div>
                </div>
            </div>
            
            <div class='signature-area'>
                <div class='signature-line'></div>
                <div class='signature-text'>Authorized Signature</div>
            </div>
            
            <div class='verification-info'>
                Verify this certificate at: creators-space.com/verify<br>
                Certificate ID: $certificateCode
            </div>
        </div>
    </body>
    </html>";
}

function generateSimpleTextCertificate($certificateCode, $studentName, $courseName, $courseLevel) {
    $issueDate = date('F j, Y');
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Certificate - $certificateCode</title>
        <style>
            body {
                margin: 0;
                padding: 30px;
                font-family: Arial, sans-serif;
                background: #f0f0f0;
                min-height: calc(100vh - 60px);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .certificate {
                background: white;
                padding: 60px;
                border: 10px solid #333;
                border-radius: 15px;
                text-align: center;
                max-width: 800px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            }
            .title { font-size: 48px; color: #2c5aa0; margin-bottom: 30px; font-weight: bold; }
            .subtitle { font-size: 24px; margin-bottom: 40px; color: #666; }
            .name { font-size: 36px; color: #e74c3c; margin: 30px 0; font-weight: bold; border-bottom: 3px solid #e74c3c; padding-bottom: 10px; }
            .course { font-size: 28px; color: #27ae60; margin: 30px 0; font-weight: bold; }
            .level { font-size: 18px; color: #f39c12; background: rgba(243,156,18,0.1); padding: 8px 16px; border-radius: 20px; display: inline-block; margin: 20px 0; }
            .footer { margin-top: 50px; font-size: 16px; color: #666; border-top: 2px solid #eee; padding-top: 30px; }
            .id { font-family: monospace; background: #f5f5f5; padding: 5px 10px; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='certificate'>
            <div class='title'>CERTIFICATE</div>
            <div class='subtitle'>OF COMPLETION</div>
            <p style='font-size: 20px; margin: 30px 0;'>This is to certify that</p>
            <div class='name'>$studentName</div>
            <p style='font-size: 20px; margin: 30px 0;'>has successfully completed</p>
            <div class='course'>$courseName</div>
            <div class='level'>$courseLevel Level</div>
            <div class='footer'>
                <p><strong>Issued by:</strong> Creators Space</p>
                <p><strong>Date:</strong> $issueDate</p>
                <p><strong>Certificate ID:</strong> <span class='id'>$certificateCode</span></p>
                <p style='margin-top: 30px; font-size: 14px;'>Verify at: creators-space.com/verify</p>
            </div>
        </div>
    </body>
    </html>";
}
?>