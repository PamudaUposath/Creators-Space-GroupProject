<?php
// backend/api/submit_student_report.php
header('Content-Type: application/json');
session_start();

// Check if user is logged in as instructor
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'instructor') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once '../config/db_connect.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);

$required_fields = ['student_id', 'report_type', 'subject', 'description', 'severity'];
$missing_fields = [];

foreach ($required_fields as $field) {
    if (!isset($input[$field]) || empty(trim($input[$field]))) {
        $missing_fields[] = $field;
    }
}

if (!empty($missing_fields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => 'Missing required fields: ' . implode(', ', $missing_fields)
    ]);
    exit;
}

try {
    $instructor_id = $_SESSION['user_id'];
    $student_id = intval($input['student_id']);
    $course_id = isset($input['course_id']) && !empty($input['course_id']) ? intval($input['course_id']) : null;
    $report_type = trim($input['report_type']);
    $subject = trim($input['subject']);
    $description = trim($input['description']);
    $severity = trim($input['severity']);
    
    // Validate report type
    $valid_types = ['academic_concern', 'behavior_issue', 'attendance_problem', 'inappropriate_conduct', 'plagiarism', 'other'];
    if (!in_array($report_type, $valid_types)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid report type']);
        exit;
    }
    
    // Validate severity
    $valid_severities = ['low', 'medium', 'high', 'urgent'];
    if (!in_array($severity, $valid_severities)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid severity level']);
        exit;
    }
    
    // Validate that student exists and is not an admin/instructor
    $stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE id = ? AND role IN ('user', 'student')");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch();
    
    if (!$student) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid student ID']);
        exit;
    }
    
    // If course_id is provided, validate that it belongs to this instructor
    if ($course_id) {
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ? AND instructor_id = ?");
        $stmt->execute([$course_id, $instructor_id]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid course or unauthorized access']);
            exit;
        }
    }
    
    // Check for duplicate reports (same instructor, student, subject within last 24 hours)
    $stmt = $pdo->prepare("
        SELECT id FROM student_reports 
        WHERE instructor_id = ? AND student_id = ? AND subject = ? 
        AND submitted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
    ");
    $stmt->execute([$instructor_id, $student_id, $subject]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Similar report already submitted within the last 24 hours']);
        exit;
    }
    
    // Insert the report
    $stmt = $pdo->prepare("
        INSERT INTO student_reports (instructor_id, student_id, course_id, report_type, subject, description, severity, status, submitted_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    
    $stmt->execute([$instructor_id, $student_id, $course_id, $report_type, $subject, $description, $severity]);
    $report_id = $pdo->lastInsertId();
    
    // Log the report submission
    error_log("Student report submitted - ID: {$report_id}, Instructor: {$instructor_id}, Student: {$student_id}, Type: {$report_type}");
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Report submitted successfully',
        'report_id' => $report_id,
        'student_name' => $student['first_name'] . ' ' . $student['last_name']
    ]);
    
} catch (PDOException $e) {
    error_log("Error submitting student report: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
} catch (Exception $e) {
    error_log("General error submitting student report: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while submitting the report']);
}
?>