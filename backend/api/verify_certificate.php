<?php
// Certificate verification API endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db_connect.php';

try {
    // Get the certificate ID from request
    $certificateId = '';
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        $certificateId = trim($_GET['id']);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $certificateId = isset($input['certificate_id']) ? trim($input['certificate_id']) : '';
    }

    if (empty($certificateId)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Certificate ID is required'
        ]);
        exit;
    }

    // Query the database for the certificate
    $stmt = $pdo->prepare("
        SELECT 
            c.id,
            c.certificate_code,
            c.issued_at,
            u.first_name,
            u.last_name,
            u.email,
            co.title as course_title,
            co.description as course_description,
            co.level,
            co.category,
            co.duration,
            CONCAT(inst.first_name, ' ', COALESCE(inst.last_name, '')) as instructor_name,
            e.progress,
            e.completed_at
        FROM certificates c
        INNER JOIN users u ON c.user_id = u.id
        INNER JOIN courses co ON c.course_id = co.id
        LEFT JOIN users inst ON co.instructor_id = inst.id
        LEFT JOIN enrollments e ON e.user_id = c.user_id AND e.course_id = c.course_id
        WHERE c.certificate_code = ?
    ");
    
    $stmt->execute([$certificateId]);
    $certificate = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($certificate) {
        // Certificate found - return verification data
        echo json_encode([
            'success' => true,
            'verified' => true,
            'data' => [
                'certificate_id' => $certificate['certificate_code'],
                'student_name' => $certificate['first_name'] . ' ' . $certificate['last_name'],
                'course_name' => $certificate['course_title'],
                'course_description' => $certificate['course_description'],
                'level' => ucfirst($certificate['level']),
                'category' => ucfirst($certificate['category']),
                'duration' => $certificate['duration'],
                'instructor' => $certificate['instructor_name'] ?: 'Creators Space Team',
                'issue_date' => $certificate['issued_at'],
                'completion_date' => $certificate['completed_at'],
                'progress' => $certificate['progress'] ?: 100,
                'verified_at' => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        // Certificate not found
        echo json_encode([
            'success' => true,
            'verified' => false,
            'message' => 'Certificate not found or invalid'
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>