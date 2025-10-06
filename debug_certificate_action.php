<?php
// Debug Certificate Email Action
header('Content-Type: text/html; charset=UTF-8');
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug Certificate Email Action - Creators Space</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .debug-section { background: white; margin: 15px 0; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .debug-section h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; border-left: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; border-left: 4px solid #dc3545; }
        .warning { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; border-left: 4px solid #ffc107; }
        .info { background: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 5px; border-left: 4px solid #17a2b8; }
        .code { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #6c757d; font-family: monospace; white-space: pre-wrap; }
        .test-form { background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-warning { background: #ffc107; color: #212529; }
        .btn-danger { background: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
    </style>
</head>
<body>
    <h1>🐛 Certificate Email Debug Console</h1>";

try {
    // Step 1: Check required files exist
    echo "<div class='debug-section'>
        <h2>📁 File System Check</h2>";
    
    $requiredFiles = [
        'PHPMailer' => __DIR__ . '/backend/lib/PHPMailer/PHPMailer.php',
        'SMTP' => __DIR__ . '/backend/lib/PHPMailer/SMTP.php',
        'Exception' => __DIR__ . '/backend/lib/PHPMailer/Exception.php',
        'Email Service' => __DIR__ . '/backend/lib/email_service.php',
        'Email Config' => __DIR__ . '/backend/config/email_config.php',
        'Certificate Generator' => __DIR__ . '/backend/lib/certificate_html_generator.php',
        'Database Config' => __DIR__ . '/backend/config/database.php'
    ];
    
    $allFilesExist = true;
    foreach ($requiredFiles as $name => $file) {
        if (file_exists($file)) {
            echo "<div class='success'>✅ {$name}: " . htmlspecialchars($file) . "</div>";
        } else {
            echo "<div class='error'>❌ {$name}: Missing - " . htmlspecialchars($file) . "</div>";
            $allFilesExist = false;
        }
    }
    echo "</div>";
    
    if (!$allFilesExist) {
        throw new Exception("Missing required files - please check file paths");
    }
    
    // Step 2: Load and test configuration
    echo "<div class='debug-section'>
        <h2>⚙️ Configuration Check</h2>";
    
    // Load email config
    $emailConfig = require __DIR__ . '/backend/config/email_config.php';
    echo "<div class='success'>✅ Email configuration loaded</div>";
    
    echo "<div class='code'>Email Configuration:
Host: " . htmlspecialchars($emailConfig['smtp_host']) . "
Port: " . htmlspecialchars($emailConfig['smtp_port']) . "
Security: " . htmlspecialchars($emailConfig['smtp_secure']) . "
Username: " . htmlspecialchars($emailConfig['smtp_username']) . "
From: " . htmlspecialchars($emailConfig['from_email']) . " (" . htmlspecialchars($emailConfig['from_name']) . ")
Auth: " . ($emailConfig['smtp_auth'] ? 'Enabled' : 'Disabled') . "</div>";
    
    // Load database config
    $dbConfig = require __DIR__ . '/backend/config/database.php';
    echo "<div class='success'>✅ Database configuration loaded</div>";
    
    echo "</div>";
    
    // Step 3: Test database connection
    echo "<div class='debug-section'>
        <h2>🗄️ Database Connection Test</h2>";
    
    try {
        $pdo = new PDO(
            "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4",
            $dbConfig['username'],
            $dbConfig['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
        echo "<div class='success'>✅ Database connection successful</div>";
        
        // Check required tables
        $tables = ['users', 'courses', 'enrollments', 'certificates'];
        foreach ($tables as $table) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table}");
            $stmt->execute();
            $count = $stmt->fetchColumn();
            echo "<div class='info'>📊 Table '{$table}': {$count} records</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        throw $e;
    }
    echo "</div>";
    
    // Step 4: Test PHPMailer
    echo "<div class='debug-section'>
        <h2>📧 PHPMailer Test</h2>";
    
    require_once __DIR__ . '/backend/lib/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/SMTP.php';
    require_once __DIR__ . '/backend/lib/PHPMailer/Exception.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    echo "<div class='success'>✅ PHPMailer classes loaded successfully</div>";
    
    // Test SMTP connection
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $emailConfig['smtp_host'];
    $mail->SMTPAuth = $emailConfig['smtp_auth'];
    $mail->Username = $emailConfig['smtp_username'];
    $mail->Password = $emailConfig['smtp_password'];
    $mail->SMTPSecure = $emailConfig['smtp_secure'];
    $mail->Port = $emailConfig['smtp_port'];
    $mail->Timeout = 10;
    
    if ($mail->smtpConnect()) {
        echo "<div class='success'>✅ SMTP connection established successfully</div>";
        $mail->smtpClose();
    } else {
        echo "<div class='error'>❌ SMTP connection failed</div>";
    }
    echo "</div>";
    
    // Step 5: Test Certificate Generation
    echo "<div class='debug-section'>
        <h2>🎓 Certificate Generation Test</h2>";
    
    require_once __DIR__ . '/backend/lib/certificate_html_generator.php';
    echo "<div class='success'>✅ Certificate generator loaded</div>";
    
    // Test certificate generation
    $testCertId = 'DEBUG_' . date('YmdHis');
    try {
        $certPath = generateCertificateHTML(
            $testCertId,
            'Test Student',
            'Test Course',
            'Beginner'
        );
        
        if (file_exists($certPath)) {
            echo "<div class='success'>✅ Test certificate generated: " . htmlspecialchars(basename($certPath)) . "</div>";
            echo "<div class='info'>📁 Certificate path: " . htmlspecialchars($certPath) . "</div>";
            echo "<div class='info'>📏 File size: " . number_format(filesize($certPath)) . " bytes</div>";
        } else {
            echo "<div class='error'>❌ Certificate file not created</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Certificate generation failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    echo "</div>";
    
    // Step 6: Full Email Service Test
    echo "<div class='debug-section'>
        <h2>📬 Email Service Test</h2>";
    
    require_once __DIR__ . '/backend/lib/email_service.php';
    echo "<div class='success'>✅ Email service loaded</div>";
    
    // Show available functions
    $functions = get_defined_functions()['user'];
    $emailFunctions = array_filter($functions, function($func) {
        return strpos($func, 'email') !== false || strpos($func, 'certificate') !== false;
    });
    
    echo "<div class='info'>🔧 Available email functions:</div>";
    echo "<div class='code'>" . implode("\n", $emailFunctions) . "</div>";
    
    echo "</div>";
    
    // Step 7: Sample Data for Testing
    echo "<div class='debug-section'>
        <h2>👥 Sample Data Check</h2>";
    
    // Get sample enrollment data
    $stmt = $pdo->prepare("
        SELECT 
            e.id as enrollment_id,
            e.progress,
            u.id as user_id,
            u.first_name,
            u.last_name,
            u.email,
            c.id as course_id,
            c.title as course_title,
            c.level,
            c.instructor_id,
            cert.id as existing_cert_id
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        JOIN courses c ON e.course_id = c.id
        LEFT JOIN certificates cert ON cert.user_id = u.id AND cert.course_id = c.id
        WHERE e.progress >= 80 
        ORDER BY e.id DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $sampleEnrollments = $stmt->fetchAll();
    
    if (empty($sampleEnrollments)) {
        echo "<div class='warning'>⚠️ No enrollments with 80%+ progress found</div>";
        
        // Show all enrollments
        $stmt = $pdo->prepare("
            SELECT 
                e.id, e.progress, u.first_name, u.last_name, c.title
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            JOIN courses c ON e.course_id = c.id
            ORDER BY e.progress DESC
            LIMIT 10
        ");
        $stmt->execute();
        $allEnrollments = $stmt->fetchAll();
        
        echo "<div class='info'>📊 Available enrollments:</div>";
        echo "<table>
            <tr><th>ID</th><th>Student</th><th>Course</th><th>Progress</th></tr>";
        foreach ($allEnrollments as $enroll) {
            $progressClass = $enroll['progress'] >= 80 ? 'success' : ($enroll['progress'] >= 50 ? 'warning' : 'error');
            echo "<tr>
                <td>{$enroll['id']}</td>
                <td>{$enroll['first_name']} {$enroll['last_name']}</td>
                <td>{$enroll['title']}</td>
                <td><span class='{$progressClass}'>{$enroll['progress']}%</span></td>
            </tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='success'>✅ Found " . count($sampleEnrollments) . " enrollments eligible for certificates</div>";
        
        echo "<table>
            <tr><th>Enrollment ID</th><th>Student</th><th>Email</th><th>Course</th><th>Progress</th><th>Has Certificate</th></tr>";
        foreach ($sampleEnrollments as $enroll) {
            $hasCert = $enroll['existing_cert_id'] ? '✅ Yes' : '❌ No';
            echo "<tr>
                <td>{$enroll['enrollment_id']}</td>
                <td>{$enroll['first_name']} {$enroll['last_name']}</td>
                <td>{$enroll['email']}</td>
                <td>{$enroll['course_title']}</td>
                <td>{$enroll['progress']}%</td>
                <td>{$hasCert}</td>
            </tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>💥 Debug Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    echo "<div class='code'>Stack Trace:\n" . htmlspecialchars($e->getTraceAsString()) . "</div>";
}

// Interactive Testing Form
echo "
<div class='debug-section'>
    <h2>🧪 Interactive Testing</h2>
    <div class='test-form'>
        <h3>Test Certificate Email Sending</h3>
        <form method='POST' action='debug_certificate_action.php'>
            <input type='hidden' name='action' value='test_email'>
            
            <p><label>Test Email Address:</label><br>
            <input type='email' name='test_email' placeholder='your-email@example.com' required style='width: 300px; padding: 8px;'></p>
            
            <p><label>Student Name:</label><br>
            <input type='text' name='student_name' value='Test Student' style='width: 300px; padding: 8px;'></p>
            
            <p><label>Course Name:</label><br>
            <input type='text' name='course_name' value='Test Course' style='width: 300px; padding: 8px;'></p>
            
            <button type='submit' class='btn btn-success'>🧪 Send Test Certificate Email</button>
        </form>
        
        <hr>
        
        <h3>Debug Real Certificate Issuance</h3>
        <form method='POST' action='debug_certificate_action.php'>
            <input type='hidden' name='action' value='debug_real'>
            
            <p><label>Enrollment ID (from table above):</label><br>
            <input type='number' name='enrollment_id' placeholder='Enter enrollment ID' style='width: 300px; padding: 8px;'></p>
            
            <button type='submit' class='btn btn-warning'>🔍 Debug Real Certificate Process</button>
        </form>
    </div>
</div>";

// Handle form submissions
if ($_POST) {
    echo "<div class='debug-section'>
        <h2>🎯 Test Results</h2>";
    
    if ($_POST['action'] === 'test_email') {
        // Test email sending
        try {
            require_once __DIR__ . '/backend/lib/email_service.php';
            require_once __DIR__ . '/backend/lib/certificate_html_generator.php';
            
            $testEmail = $_POST['test_email'];
            $studentName = $_POST['student_name'];
            $courseName = $_POST['course_name'];
            $certificateCode = 'TEST_' . date('YmdHis');
            
            echo "<div class='info'>📧 Testing email to: " . htmlspecialchars($testEmail) . "</div>";
            
            // Generate test certificate
            $certPath = generateCertificateHTML($certificateCode, $studentName, $courseName, 'Beginner');
            echo "<div class='success'>✅ Test certificate generated</div>";
            
            // Send email
            $result = sendCertificateEmail($testEmail, $studentName, $courseName, $certificateCode, $certPath);
            
            if ($result) {
                echo "<div class='success'>✅ TEST EMAIL SENT SUCCESSFULLY!</div>";
                echo "<div class='info'>Check your inbox: " . htmlspecialchars($testEmail) . "</div>";
            } else {
                echo "<div class='error'>❌ Test email failed to send</div>";
                echo "<div class='warning'>Check the error logs for more details</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>💥 Test failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    if ($_POST['action'] === 'debug_real') {
        // Debug real certificate process
        try {
            $enrollmentId = (int)$_POST['enrollment_id'];
            
            echo "<div class='info'>🔍 Debugging enrollment ID: {$enrollmentId}</div>";
            
            // Get enrollment details
            $stmt = $pdo->prepare("
                SELECT 
                    e.id as enrollment_id,
                    e.progress,
                    u.id as user_id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    c.id as course_id,
                    c.title as course_title,
                    c.level,
                    cert.id as existing_cert_id
                FROM enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                LEFT JOIN certificates cert ON cert.user_id = u.id AND cert.course_id = c.id
                WHERE e.id = ?
            ");
            $stmt->execute([$enrollmentId]);
            $details = $stmt->fetch();
            
            if (!$details) {
                throw new Exception("Enrollment not found");
            }
            
            echo "<div class='code'>Enrollment Details:
Student: {$details['first_name']} {$details['last_name']} ({$details['email']})
Course: {$details['course_title']} (Level: {$details['level']})
Progress: {$details['progress']}%
Existing Certificate: " . ($details['existing_cert_id'] ? 'Yes (ID: ' . $details['existing_cert_id'] . ')' : 'No') . "</div>";
            
            // Check if eligible
            if ($details['progress'] < 80) {
                throw new Exception("Student progress is {$details['progress']}% - below 80% requirement");
            }
            
            if ($details['existing_cert_id']) {
                echo "<div class='warning'>⚠️ Certificate already exists for this student</div>";
            } else {
                echo "<div class='success'>✅ Student eligible for certificate</div>";
                echo "<div class='info'>🎯 Ready to issue certificate - process would proceed normally</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>💥 Debug failed: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "</div>";
}

echo "
    <div class='debug-section'>
        <h2>📝 Quick Actions</h2>
        <a href='test_email_config.php' class='btn'>🔧 Email Config Test</a>
        <a href='frontend/instructor-students.php' class='btn'>👥 Instructor Students Page</a>
        <a href='backend/logs/' class='btn btn-warning'>📋 View Logs</a>
        <button onclick='location.reload()' class='btn btn-success'>🔄 Refresh Debug</button>
    </div>
</body>
</html>";
?>