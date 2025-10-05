<?php
// frontend/instructor-students.php
session_start();

// Check if user is logged in as instructor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    header('Location: login.php');
    exit;
}

require_once '../backend/config/db_connect.php';

$instructor_id = $_SESSION['user_id'];
$message = '';
$message_type = '';

// Handle certificate issuance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'issue_certificate') {
    // Enable debugging
    $debug_log = [];
    $debug_log[] = "=== CERTIFICATE ISSUANCE DEBUG START ===";
    $debug_log[] = "Timestamp: " . date('Y-m-d H:i:s');
    
    try {
        $enrollment_id = intval($_POST['enrollment_id']);
        $user_id = intval($_POST['user_id']);
        $course_id = intval($_POST['course_id']);
        
        $debug_log[] = "Input Parameters:";
        $debug_log[] = "  Enrollment ID: $enrollment_id";
        $debug_log[] = "  User ID: $user_id";
        $debug_log[] = "  Course ID: $course_id";
        
        // First, check the student's progress
        $stmt = $pdo->prepare("SELECT progress FROM enrollments WHERE id = ? AND user_id = ? AND course_id = ?");
        $stmt->execute([$enrollment_id, $user_id, $course_id]);
        $enrollment = $stmt->fetch();
        
        $debug_log[] = "Progress Check:";
        $debug_log[] = "  Found enrollment: " . ($enrollment ? 'Yes' : 'No');
        if ($enrollment) {
            $debug_log[] = "  Progress: " . $enrollment['progress'] . "%";
        }
        
        if (!$enrollment) {
            $debug_log[] = "ERROR: Enrollment not found";
            $message = "Enrollment not found.";
            $message_type = "error";
        } elseif ($enrollment['progress'] < 80) {
            $debug_log[] = "ERROR: Progress insufficient (" . $enrollment['progress'] . "% < 80%)";
            $message = "Certificate can only be issued to students with 80% or higher progress. Current progress: " . $enrollment['progress'] . "%";
            $message_type = "error";
        } else {
            $debug_log[] = "✅ Progress check passed (" . $enrollment['progress'] . "% >= 80%)";
            
            // Check if certificate already exists
            $stmt = $pdo->prepare("SELECT id FROM certificates WHERE user_id = ? AND course_id = ?");
            $stmt->execute([$user_id, $course_id]);
            $existing_cert = $stmt->fetch();
            
            $debug_log[] = "Certificate Existence Check:";
            $debug_log[] = "  Existing certificate: " . ($existing_cert ? 'Yes (ID: ' . $existing_cert['id'] . ')' : 'No');
            
            if (!$existing_cert) {
                $debug_log[] = "🎯 Proceeding with certificate issuance...";
                
                // Generate certificate code
                $certificate_code = 'CERT-' . strtoupper(substr(md5($course_id . $user_id . time()), 0, 8));
                $debug_log[] = "Generated certificate code: $certificate_code";
                
                // Get student and course details for email
                $stmt = $pdo->prepare("
                    SELECT 
                        u.first_name, u.last_name, u.email,
                        c.title as course_title,
                        c.level,
                        CONCAT(inst.first_name, ' ', COALESCE(inst.last_name, '')) as instructor_name
                    FROM users u
                    JOIN courses c ON c.id = ?
                    LEFT JOIN users inst ON c.instructor_id = inst.id
                    WHERE u.id = ?
                ");
                $stmt->execute([$course_id, $user_id]);
                $details = $stmt->fetch();
                
                $debug_log[] = "Student Details Retrieved:";
                $debug_log[] = "  Name: " . ($details ? $details['first_name'] . ' ' . $details['last_name'] : 'Not found');
                $debug_log[] = "  Email: " . ($details ? $details['email'] : 'Not found');
                $debug_log[] = "  Course: " . ($details ? $details['course_title'] : 'Not found');
                
                // Insert certificate
                $stmt = $pdo->prepare("
                    INSERT INTO certificates (user_id, course_id, certificate_code, issued_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->execute([$user_id, $course_id, $certificate_code]);
                $debug_log[] = "✅ Certificate record inserted into database";
                
                // Update enrollment as completed
                $stmt = $pdo->prepare("
                    UPDATE enrollments SET status = 'completed', completed_at = NOW(), progress = 100
                    WHERE id = ?
                ");
                $stmt->execute([$enrollment_id]);
                $debug_log[] = "✅ Enrollment updated to completed status";
                
                // Generate and send certificate email
                $debug_log[] = "📧 Starting certificate generation and email process...";
                require_once '../backend/lib/certificate_html_generator.php';
                require_once '../backend/lib/email_service.php';
                $debug_log[] = "✅ Required libraries loaded";
                
                try {
                    // Generate certificate HTML (works without GD extension)
                    $debug_log[] = "🎓 Generating certificate HTML...";
                    $certificatePath = generateCertificateHTML(
                        $certificate_code,
                        $details['first_name'] . ' ' . $details['last_name'],
                        $details['course_title'],
                        $details['level']
                    );
                    
                    $debug_log[] = "✅ Certificate generated at: " . $certificatePath;
                    $debug_log[] = "File exists: " . (file_exists($certificatePath) ? 'Yes' : 'No');
                    if (file_exists($certificatePath)) {
                        $debug_log[] = "File size: " . filesize($certificatePath) . " bytes";
                    }
                    
                    // Get the certificate URL for sharing
                    $certificateUrl = 'http://localhost/Creators-Space-GroupProject/storage/certificates/' . basename($certificatePath);
                    $debug_log[] = "Certificate URL: " . $certificateUrl;
                    
                    // Try to send email with certificate
                    $debug_log[] = "📬 Attempting to send certificate email...";
                    $debug_log[] = "Email parameters:";
                    $debug_log[] = "  To: " . $details['email'];
                    $debug_log[] = "  Name: " . $details['first_name'] . ' ' . $details['last_name'];
                    $debug_log[] = "  Course: " . $details['course_title'];
                    $debug_log[] = "  Cert Code: " . $certificate_code;
                    
                    $emailSent = sendCertificateEmail(
                        $details['email'],
                        $details['first_name'] . ' ' . $details['last_name'],
                        $details['course_title'],
                        $certificate_code,
                        $certificatePath,
                        $details['level']
                    );
                    
                    $debug_log[] = "Email sending result: " . ($emailSent ? 'SUCCESS' : 'FAILED');
                    
                    if ($emailSent) {
                        $debug_log[] = "✅ SUCCESS: Certificate issued and email sent successfully!";
                        $message = "🎉 Certificate issued successfully and sent via email!<br>";
                        $message .= "<strong>Certificate ID:</strong> $certificate_code<br>";
                        $message .= "<a href='$certificateUrl' target='_blank' style='color: #667eea;'>📜 View Certificate</a>";
                        $message_type = "success";
                    } else {
                        $debug_log[] = "⚠️ Certificate issued but email failed to send";
                        $message = "Certificate issued successfully! 🎉";
                        // $message .= "<strong>Certificate ID:</strong> $certificate_code<br>";
                        // $message .= "<strong>Student:</strong> " . htmlspecialchars($details['first_name'] . ' ' . $details['last_name']) . "<br>";
                        // $message .= "<strong>Email:</strong> " . htmlspecialchars($details['email']) . "<br>";
                        // $message .= "<a href='$certificateUrl' target='_blank' style='color: #667eea; font-weight: bold;'>📜 View Certificate</a><br>";
                        // $message .= "<small style='color: #dc2626; font-weight: 500;'>⚠️ Email not sent - Please share the certificate link with the student manually</small>";
                        $message_type = "success";
                    }
                } catch (Exception $e) {
                    $debug_log[] = "ERROR in certificate/email process: " . $e->getMessage();
                    $debug_log[] = "Stack trace: " . $e->getTraceAsString();
                    $message = "Certificate issued successfully! Certificate ID: $certificate_code (Note: " . $e->getMessage() . ")";
                    $message_type = "success";
                }
            } else {
                $debug_log[] = "ERROR: Certificate already exists";
                $message = "Certificate already exists for this student and course.";
                $message_type = "error";
            }
        }
    } catch (PDOException $e) {
        $debug_log[] = "DATABASE ERROR: " . $e->getMessage();
        $debug_log[] = "Stack trace: " . $e->getTraceAsString();
        $message = "Error issuing certificate: " . $e->getMessage();
        $message_type = "error";
    } catch (Exception $e) {
        $debug_log[] = "GENERAL ERROR: " . $e->getMessage();
        $debug_log[] = "Stack trace: " . $e->getTraceAsString();
        $message = "Error issuing certificate: " . $e->getMessage();
        $message_type = "error";
    }
    
    // Log debug information
    $debug_log[] = "=== CERTIFICATE ISSUANCE DEBUG END ===";
    error_log(implode("\n", $debug_log));
    
    // Also save debug log to a file for easy viewing
    $debug_file = __DIR__ . '/../backend/logs/certificate_debug_' . date('Y-m-d') . '.log';
    $debug_dir = dirname($debug_file);
    if (!is_dir($debug_dir)) {
        mkdir($debug_dir, 0755, true);
    }
    file_put_contents($debug_file, implode("\n", $debug_log) . "\n\n", FILE_APPEND | LOCK_EX);
    
    // Add debug info to message if in development
    if (isset($_GET['debug']) && $_GET['debug'] === '1') {
        $message .= "<br><br><details style='margin-top: 10px;'><summary style='cursor: pointer; color: #666;'>🐛 Debug Information (Click to expand)</summary>";
        $message .= "<pre style='background: #f4f4f4; padding: 10px; border-radius: 5px; font-size: 12px; max-height: 300px; overflow-y: auto; margin-top: 10px;'>";
        $message .= htmlspecialchars(implode("\n", $debug_log));
        $message .= "</pre></details>";
    }
}

// Get filter parameters
$course_filter = $_GET['course'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Get instructor's students with enrollments
try {
    $query = "
        SELECT 
            e.id as enrollment_id,
            e.enrolled_at,
            e.progress,
            e.status,
            e.completed_at,
            e.last_accessed,
            u.id as user_id,
            u.first_name,
            u.last_name,
            u.email,
            c.id as course_id,
            c.title as course_title,
            c.level,
            c.price,
            cert.certificate_code,
            cert.issued_at as certificate_issued
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        JOIN courses c ON e.course_id = c.id
        LEFT JOIN certificates cert ON cert.user_id = u.id AND cert.course_id = c.id
        WHERE c.instructor_id = ?
    ";

    $params = [$instructor_id];

    if ($course_filter) {
        $query .= " AND c.id = ?";
        $params[] = $course_filter;
    }

    if ($status_filter) {
        $query .= " AND e.status = ?";
        $params[] = $status_filter;
    }

    $query .= " ORDER BY e.enrolled_at DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $enrollments = $stmt->fetchAll();

    // Get instructor's courses for filter dropdown
    $stmt = $pdo->prepare("SELECT id, title FROM courses WHERE instructor_id = ? ORDER BY title");
    $stmt->execute([$instructor_id]);
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching students: " . $e->getMessage());
    $enrollments = [];
    $courses = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management - Creators Space</title>
    <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #5a73e5 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
            padding-top: 80px;
        }

        /* Modern Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%);
            backdrop-filter: blur(30px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .navbar:hover::before {
            opacity: 1;
        }

        .navbar-container {
            max-width: 1400px !important;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 2rem !important;
            position: relative;
            z-index: 2;
            height: 100% !important;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-left: auto;
            justify-content: flex-end;
        }

        /* Logo Section */
        .navbar h1 {
            margin: 0 !important;
            position: relative;
            margin-right: auto;
            font-size: 24px !important;
            font-weight: bold !important;
            color: black !important;
        }

        .navbar h1 a {
            display: flex !important;
            align-items: center;
            gap: 0.8rem !important;
            text-decoration: none;
            color: #ffffff !important;
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            transition: all 0.3s ease;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            width: auto;
        }

        .navbar h1 a:hover {
            color: #5a73e5 !important;
            text-shadow: 0 0 20px rgba(102, 126, 234, 0.8);
            transform: translateY(-1px);
        }

        #navbar-logo {
            width: 50px !important;
            height: 50px !important;
            object-fit: contain;
            transition: all 0.3s ease;
        }

        .navbar h1 a:hover #navbar-logo {
            transform: scale(1.05);
            filter: brightness(1.1);
        }

        /* Navigation Links */
        .navbar .nav-links {
            display: flex !important;
            align-items: center;
            gap: 2rem !important;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navbar .nav-links a {
            position: relative;
            color: #ffffff !important;
            text-decoration: none;
            padding: 0.5rem 0;
            font-weight: 500;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            margin: 10px 2px;
        }

        .navbar .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }

        .navbar .nav-links a:hover {
            color: #ffffff !important;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.6);
        }

        .navbar .nav-links a:hover::after {
            width: 100%;
        }

        /* User Section */
        #userSection {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 25px;
            padding: 0.4rem 0.8rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            max-width: fit-content;
        }

        #userSection span {
            color: #ffffff !important;
            font-weight: 500;
            font-size: 0.75rem;
            margin-right: 0.2rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            white-space: nowrap;
            max-width: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Button Styles */
        .navbar .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.6rem 1rem;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.3px;
            border: 1px solid transparent;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(20px);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            color: #ffffff !important;
            margin: 10px 2px;
        }

        .navbar .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .navbar .btn:hover::before {
            left: 100%;
        }

        .navbar .btn.profile-btn {
            background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
            font-size: 0.9rem !important;
            padding: 0 !important;
            width: 35px !important;
            height: 35px !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 35px !important;
            max-width: 35px !important;
            min-height: 35px !important;
            max-height: 35px !important;
            text-align: center !important;
            line-height: 1 !important;
        }

        .navbar .btn.logout-btn {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            text-align: center;
            min-width: auto;
            white-space: nowrap;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.2);
        }

        .navbar .btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .navbar .btn.profile-btn:hover {
            box-shadow: 0 15px 35px rgba(76, 175, 80, 0.4);
        }

        .navbar .btn.logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            background: linear-gradient(135deg, #ff5252 0%, #f44336 100%);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Theme Toggle Button */
        .theme-toggle {
            display: flex;
            align-items: center;
            margin-left: 1rem;
        }

        .theme-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 0.6rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1rem;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .theme-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .theme-btn:hover::before {
            left: 100%;
        }

        .theme-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);
        }

        .theme-btn:active {
            transform: translateY(0) scale(0.95);
        }

        #theme-icon {
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .theme-btn:hover #theme-icon {
            transform: rotate(15deg);
        }

        /* Dark mode styles */
        body.dark-mode {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #ffffff;
        }

        body.dark-mode .navbar {
            background: linear-gradient(135deg, rgba(10, 10, 20, 0.95) 0%, rgba(20, 20, 40, 0.95) 100%) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        body.dark-mode .theme-btn {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.25);
        }

        body.dark-mode .theme-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.35);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #667eea;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: #64748b;
            font-size: 1.1rem;
        }

        .filters {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .filter-row {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
        }

        .filter-select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.2s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .actions-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .actions-container .btn {
            margin: 0;
        }

        .progress-status {
            margin-right: 0.5rem;
        }

        .students-table {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .table th {
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
        }

        .table td {
            color: #4a5568;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
        }

        .student-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .student-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .student-details h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .student-details p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        .progress-bar {
            width: 100px;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-text {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(72, 187, 120, 0.2);
            color: #38a169;
        }

        .status-completed {
            background: rgba(72, 187, 120, 0.2);
            color: #38a169;
        }

        .status-paused {
            background: rgba(237, 137, 54, 0.2);
            color: #dd6b20;
        }

        .status-cancelled {
            background: rgba(245, 101, 101, 0.2);
            color: #e53e3e;
        }

        .certificate-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .certificate-id {
            font-family: 'Monaco', 'Menlo', monospace;
            font-size: 0.8rem;
            background: #f7fafc;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: #4a5568;
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .message-success {
            background: rgba(72, 187, 120, 0.1);
            color: #38a169;
            border: 1px solid rgba(72, 187, 120, 0.2);
        }

        .message-error {
            background: rgba(245, 101, 101, 0.1);
            color: #e53e3e;
            border: 1px solid rgba(245, 101, 101, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #64748b;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header-content {
                padding: 0 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                gap: 1rem;
            }

            .filter-row {
                flex-direction: column;
            }

            .filter-group {
                min-width: 100%;
            }

            .students-table {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }
        }

        /* Report Modal Styles */
        .modal {
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 85vh;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            animation: modalSlideIn 0.3s ease-out;
            /* Hide scrollbar */
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* Internet Explorer 10+ */
        }

        .modal-content::-webkit-scrollbar {
            display: none;
            /* WebKit */
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close:hover {
            color: #fca5a5;
            transform: scale(1.1);
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #374151;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modal-footer {
            padding: 1.5rem 2rem;
            background: #f9fafb;
            display: flex !important;
            justify-content: flex-end;
            gap: 1rem;
            border-top: 1px solid #e5e7eb;
            position: relative;
            z-index: 10;
        }

        .modal-footer .btn {
            display: inline-block !important;
            visibility: visible !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block !important;
            visibility: visible !important;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #b91c1c, #dc2626);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block !important;
            visibility: visible !important;
        }

        .btn-secondary:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        /* Responsive Design for Modal */
        @media (max-height: 600px) {
            .modal-content {
                margin: 2% auto;
                max-height: 95vh;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 2% auto;
                max-height: 90vh;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-footer {
                padding: 1rem 1.5rem !important;
                flex-direction: row !important;
                gap: 0.5rem;
            }

            .modal-footer .btn {
                flex: 1;
                min-width: 100px;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    <div class="navbar">
        <div class="navbar-container">
            <h1>
                <a href="instructor-dashboard.php">
                    <img id="navbar-logo" width="80px" src="./assets/images/logo-nav-light.png" alt="logo Creators-Space">
                    Creators-Space
                </a>
            </h1>

            <div class="navbar-right">
                <div class="nav-links align-items-center">
                    <a href="instructor-dashboard.php">Dashboard</a>
                    <a href="instructor-courses.php">My Courses</a>
                    <a href="instructor-students.php">Students</a>
                    <a href="instructor-messages.php">Messages</a>

                    <!-- Dark/Light Mode Toggle -->
                    <div class="theme-toggle">
                        <button id="theme-toggle-btn" class="theme-btn" title="Toggle Dark/Light Mode">
                            <i class="fas fa-moon" id="theme-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- User Section -->
                <div id="userSection">
                    <a href="#" class="btn profile-btn" title="Profile">
                        <i class="fas fa-user"></i>
                    </a>
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</span>
                    <a href="../backend/auth/logout.php" class="btn logout-btn">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-users"></i> Student Management
            </h1>
            <p class="page-subtitle">
                Monitor student progress and issue certificates for course completions
            </p>
        </div>

        <?php if ($message): ?>
            <div class="message message-<?php echo $message_type; ?>">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Filter by Course</label>
                    <select name="course" class="filter-select">
                        <option value="">All Courses</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo $course['id']; ?>" <?php echo $course_filter == $course['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['title']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">Filter by Status</label>
                    <select name="status" class="filter-select">
                        <option value="">All Statuses</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="paused" <?php echo $status_filter === 'paused' ? 'selected' : ''; ?>>Paused</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="filter-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <?php if (empty($enrollments)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>No students found</h3>
                <p>No students have enrolled in your courses yet, or no results match your filters.</p>
            </div>
        <?php else: ?>
            <div class="students-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Course</th>
                            <th>Enrolled</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Certificate</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <?php echo strtoupper(substr($enrollment['first_name'], 0, 1)); ?>
                                        </div>
                                        <div class="student-details">
                                            <h4><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></h4>
                                            <p><?php echo htmlspecialchars($enrollment['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($enrollment['course_title']); ?></strong>
                                    <br>
                                    <span style="font-size: 0.85rem; color: #64748b;">
                                        <?php echo ucfirst($enrollment['level']); ?> • $<?php echo number_format($enrollment['price'], 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($enrollment['enrolled_at'])); ?>
                                    <br>
                                    <span style="font-size: 0.8rem; color: #64748b;">
                                        <?php
                                        if ($enrollment['last_accessed']) {
                                            echo 'Last accessed: ' . date('M j', strtotime($enrollment['last_accessed']));
                                        } else {
                                            echo 'Never accessed';
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?php echo $enrollment['progress']; ?>%"></div>
                                    </div>
                                    <div class="progress-text"><?php echo number_format($enrollment['progress'], 1); ?>%</div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $enrollment['status']; ?>">
                                        <?php echo ucfirst($enrollment['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($enrollment['certificate_code']): ?>
                                        <div class="certificate-info">
                                            <i class="fas fa-certificate" style="color: #48bb78;"></i>
                                            <div>
                                                <div class="certificate-id"><?php echo $enrollment['certificate_code']; ?></div>
                                                <div style="font-size: 0.8rem; color: #64748b;">
                                                    <?php echo date('M j, Y', strtotime($enrollment['certificate_issued'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #64748b; font-size: 0.9rem;">Not issued</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions-container">
                                        <?php if (!$enrollment['certificate_code'] && $enrollment['progress'] >= 80): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="issue_certificate">
                                                <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['enrollment_id']; ?>">
                                                <input type="hidden" name="user_id" value="<?php echo $enrollment['user_id']; ?>">
                                                <input type="hidden" name="course_id" value="<?php echo $enrollment['course_id']; ?>">
                                                <button type="submit" class="btn btn-success btn-small"
                                                    onclick="return confirm('Issue certificate for this student?');">
                                                    <i class="fas fa-certificate"></i> Issue Certificate
                                                </button>
                                            </form>
                                        <?php elseif (!$enrollment['certificate_code']): ?>
                                            <span class="progress-status" style="color: #64748b; font-size: 0.85rem;">
                                                Need 80%+ progress
                                            </span>
                                        <?php else: ?>
                                            <span class="progress-status" style="color: #48bb78; font-size: 0.85rem;">
                                                <i class="fas fa-check"></i> Completed
                                            </span>
                                        <?php endif; ?>
                                        <button class="btn btn-primary btn-small"
                                            onclick="messageStudent(<?php echo $enrollment['user_id']; ?>, '<?php echo htmlspecialchars(addslashes($enrollment['first_name'] . ' ' . $enrollment['last_name'])); ?>', <?php echo $enrollment['course_id']; ?>)">
                                            <i class="fas fa-envelope"></i> Message
                                        </button>
                                        <button class="btn btn-danger btn-small"
                                            onclick="reportStudent(<?php echo $enrollment['user_id']; ?>, '<?php echo htmlspecialchars(addslashes($enrollment['first_name'] . ' ' . $enrollment['last_name'])); ?>', <?php echo $enrollment['course_id']; ?>)">
                                            <i class="fas fa-flag"></i> Report
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Report Student Modal -->
    <div id="reportModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-flag"></i> Report Student</h3>
                <span class="close" onclick="closeReportModal()">&times;</span>
            </div>
            <form id="reportForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reportType">Report Type:</label>
                        <select id="reportType" name="report_type" required>
                            <option value="academic_concern">Academic Concern</option>
                            <option value="behavior_issue">Behavior Issue</option>
                            <option value="attendance_problem">Attendance Problem</option>
                            <option value="inappropriate_conduct">Inappropriate Conduct</option>
                            <option value="plagiarism">Plagiarism</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reportSeverity">Severity:</label>
                        <select id="reportSeverity" name="severity" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="reportSubject">Subject:</label>
                        <input type="text" id="reportSubject" name="subject" required placeholder="Brief description of the issue" maxlength="255">
                    </div>

                    <div class="form-group">
                        <label for="reportDescription">Detailed Description:</label>
                        <textarea id="reportDescription" name="description" required placeholder="Please provide detailed information about the issue..." rows="5"></textarea>
                    </div>

                    <input type="hidden" id="reportStudentId" name="student_id">
                    <input type="hidden" id="reportCourseId" name="course_id">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeReportModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-hide messages
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500);
            });
        }, 5000);

        // Theme toggle functionality
        const themeToggleBtn = document.getElementById('theme-toggle-btn');
        const themeIcon = document.getElementById('theme-icon');

        // Load saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
            themeIcon.className = 'fas fa-sun';
        } else {
            themeIcon.className = 'fas fa-moon';
        }

        // Theme toggle functionality
        themeToggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains('dark-mode')) {
                themeIcon.className = 'fas fa-sun';
                localStorage.setItem('theme', 'dark');
            } else {
                themeIcon.className = 'fas fa-moon';
                localStorage.setItem('theme', 'light');
            }

            // Add a little animation to the button
            themeToggleBtn.style.transform = 'scale(0.9)';
            setTimeout(() => {
                themeToggleBtn.style.transform = '';
            }, 150);
        });

        // Message student function
        function messageStudent(studentId, studentName, courseId) {
            // Open messaging page with pre-selected student
            window.open(`instructor-messages.php?student_id=${studentId}&student_name=${encodeURIComponent(studentName)}&course_id=${courseId}`, '_blank');
        }

        // Report student functionality
        let currentStudentData = {};

        function reportStudent(studentId, studentName, courseId) {
            currentStudentData = {
                studentId,
                studentName,
                courseId
            };

            // Set hidden form fields
            document.getElementById('reportStudentId').value = studentId;
            document.getElementById('reportCourseId').value = courseId;

            // Update modal title
            document.querySelector('#reportModal .modal-header h3').innerHTML =
                `<i class="fas fa-flag"></i> Report Student: ${studentName}`;

            // Show modal
            document.getElementById('reportModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('reportForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('reportModal');
            if (event.target === modal) {
                closeReportModal();
            }
        }

        // Handle form submission
        document.getElementById('reportForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);

                const response = await fetch('../backend/api/submit_student_report.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(`Report submitted successfully for ${currentStudentData.studentName}. Report ID: ${result.report_id}`);
                    closeReportModal();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error submitting report:', error);
                alert('An error occurred while submitting the report. Please try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });

        // Animate progress bars
        document.addEventListener('DOMContentLoaded', function() {
            const progressBars = document.querySelectorAll('.progress-fill');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });

        console.log('Student management page loaded successfully');
    </script>
</body>

</html>