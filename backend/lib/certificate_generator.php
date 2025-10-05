<?php
// backend/lib/certificate_generator.php
// Certificate generation service using GD library

function generateCertificate($certificateCode, $studentName, $courseName, $courseLevel) {
    // Check if GD extension is available
    if (!extension_loaded('gd')) {
        // Fall back to HTML certificate generation
        require_once __DIR__ . '/certificate_html_generator.php';
        return generateCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel);
    }
    
    try {
        // Path to certificate template
        $templatePath = __DIR__ . '/../../frontend/assets/images/Certificate/Creators-Sapce-Certificate Template.png';
        
        // Check if template exists
        if (!file_exists($templatePath)) {
            // Fall back to HTML certificate generation
            require_once __DIR__ . '/certificate_html_generator.php';
            return generateCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel);
        }
        
        // Load the template image
        $template = imagecreatefrompng($templatePath);
        if (!$template) {
            // Fall back to HTML certificate generation
            require_once __DIR__ . '/certificate_html_generator.php';
            return generateCertificateHTML($certificateCode, $studentName, $courseName, $courseLevel);
        }
        
        // Get image dimensions
        $width = imagesx($template);
        $height = imagesy($template);
        
        // Define colors
        $black = imagecolorallocate($template, 0, 0, 0);
        $darkBlue = imagecolorallocate($template, 26, 35, 126); // #1a237e
        $gold = imagecolorallocate($template, 255, 193, 7); // #ffc107
        
        // Define font paths (you may need to adjust these paths)
        $fontPathBold = __DIR__ . '/fonts/arial-bold.ttf';
        $fontPathRegular = __DIR__ . '/fonts/arial.ttf';
        
        // If TTF fonts are not available, we'll use built-in fonts
        $useBuiltInFonts = !file_exists($fontPathBold) || !file_exists($fontPathRegular);
        
        if ($useBuiltInFonts) {
            // Use built-in fonts (less elegant but works without external font files)
            // Course name (centered, top area)
            $courseText = strtoupper($courseName);
            $courseX = $width / 2 - (strlen($courseText) * 10) / 2;
            $courseY = $height * 0.35; // Approximately 35% from top
            imagestring($template, 5, $courseX, $courseY, $courseText, $darkBlue);
            
            // Student name (centered, middle area)
            $nameText = strtoupper($studentName);
            $nameX = $width / 2 - (strlen($nameText) * 12) / 2;
            $nameY = $height * 0.50; // Approximately 50% from top
            imagestring($template, 5, $nameX, $nameY, $nameText, $black);
            
            // Certificate ID (bottom right area)
            $certText = "Certificate ID: " . $certificateCode;
            $certX = $width - (strlen($certText) * 8) - 20;
            $certY = $height - 40;
            imagestring($template, 3, $certX, $certY, $certText, $darkBlue);
            
            // Course level (if provided)
            if (!empty($courseLevel)) {
                $levelText = "Level: " . ucfirst($courseLevel);
                $levelX = 20;
                $levelY = $height - 40;
                imagestring($template, 3, $levelX, $levelY, $levelText, $darkBlue);
            }
            
            // Date issued
            $dateText = "Issued: " . date('F j, Y');
            $dateX = $width / 2 - (strlen($dateText) * 6) / 2;
            $dateY = $height * 0.75;
            imagestring($template, 3, $dateX, $dateY, $dateText, $darkBlue);
            
        } else {
            // Use TTF fonts for better typography
            // Course name
            imagettftext($template, 24, 0, $width/2 - 200, $height * 0.35, $darkBlue, $fontPathBold, strtoupper($courseName));
            
            // Student name
            imagettftext($template, 28, 0, $width/2 - 250, $height * 0.50, $black, $fontPathBold, strtoupper($studentName));
            
            // Certificate ID
            imagettftext($template, 12, 0, $width - 300, $height - 40, $darkBlue, $fontPathRegular, "Certificate ID: " . $certificateCode);
            
            // Course level
            if (!empty($courseLevel)) {
                imagettftext($template, 12, 0, 20, $height - 40, $darkBlue, $fontPathRegular, "Level: " . ucfirst($courseLevel));
            }
            
            // Date issued
            imagettftext($template, 14, 0, $width/2 - 100, $height * 0.75, $darkBlue, $fontPathRegular, "Issued: " . date('F j, Y'));
        }
        
        // Create output directory if it doesn't exist
        $outputDir = __DIR__ . '/../../storage/certificates/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        // Generate filename
        $filename = 'certificate_' . $certificateCode . '_' . time() . '.png';
        $outputPath = $outputDir . $filename;
        
        // Save the image
        $saved = imagepng($template, $outputPath, 9); // 9 = maximum compression
        
        // Clean up memory
        imagedestroy($template);
        
        if (!$saved) {
            throw new Exception("Failed to save certificate image");
        }
        
        return $outputPath;
        
    } catch (Exception $e) {
        error_log("Certificate generation error: " . $e->getMessage());
        throw new Exception("Failed to generate certificate: " . $e->getMessage());
    }
}

// Alternative function to generate PDF certificate using TCPDF (if available)
function generateCertificatePDF($certificateCode, $studentName, $courseName, $courseLevel) {
    // Check if TCPDF is available
    if (!class_exists('TCPDF')) {
        throw new Exception("TCPDF library not found. Please install TCPDF or use image generation.");
    }
    
    try {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator('Creators Space');
        $pdf->SetAuthor('Creators Space');
        $pdf->SetTitle('Certificate of Completion');
        $pdf->SetSubject('Course Certificate');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add a page
        $pdf->AddPage('L', 'A4'); // Landscape orientation
        
        // Set background color
        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect(0, 0, 297, 210, 'F');
        
        // Add border
        $pdf->SetDrawColor(26, 35, 126);
        $pdf->SetLineWidth(2);
        $pdf->Rect(10, 10, 277, 190);
        
        // Certificate title
        $pdf->SetFont('helvetica', 'B', 36);
        $pdf->SetTextColor(26, 35, 126);
        $pdf->SetXY(0, 40);
        $pdf->Cell(297, 20, 'CERTIFICATE OF COMPLETION', 0, 1, 'C');
        
        // Course name
        $pdf->SetFont('helvetica', 'B', 24);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(0, 80);
        $pdf->Cell(297, 15, strtoupper($courseName), 0, 1, 'C');
        
        // Student name
        $pdf->SetFont('helvetica', 'B', 28);
        $pdf->SetXY(0, 110);
        $pdf->Cell(297, 20, strtoupper($studentName), 0, 1, 'C');
        
        // Date and certificate ID
        $pdf->SetFont('helvetica', '', 12);
        $pdf->SetTextColor(26, 35, 126);
        
        // Date (bottom left)
        $pdf->SetXY(20, 170);
        $pdf->Cell(100, 10, 'Issued: ' . date('F j, Y'), 0, 0, 'L');
        
        // Certificate ID (bottom right)
        $pdf->SetXY(180, 170);
        $pdf->Cell(100, 10, 'Certificate ID: ' . $certificateCode, 0, 0, 'R');
        
        // Level (if provided)
        if (!empty($courseLevel)) {
            $pdf->SetXY(20, 180);
            $pdf->Cell(100, 10, 'Level: ' . ucfirst($courseLevel), 0, 0, 'L');
        }
        
        // Create output directory if it doesn't exist
        $outputDir = __DIR__ . '/../../storage/certificates/';
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }
        
        // Generate filename and save
        $filename = 'certificate_' . $certificateCode . '_' . time() . '.pdf';
        $outputPath = $outputDir . $filename;
        
        $pdf->Output($outputPath, 'F');
        
        return $outputPath;
        
    } catch (Exception $e) {
        error_log("PDF Certificate generation error: " . $e->getMessage());
        throw new Exception("Failed to generate PDF certificate: " . $e->getMessage());
    }
}
?>