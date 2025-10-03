<?php
/**
 * Unenroll API - Handle course unenrollment
 * Creators-Space Project
 */

session_start();
require_once '../config/db_connect.php';

// Enable CORS for API requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'User not authenticated'
    ]);
    exit();
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['enrollment_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Enrollment ID is required'
    ]);
    exit();
}

$enrollment_id = $input['enrollment_id'];

try {
    // Verify that the enrollment belongs to the current user
    $stmt = $pdo->prepare("SELECT id, course_id FROM enrollments WHERE id = ? AND user_id = ?");
    $stmt->execute([$enrollment_id, $user_id]);
    $enrollment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$enrollment) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Enrollment not found or access denied'
        ]);
        exit();
    }
    
    // Delete the enrollment record
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ? AND user_id = ?");
    $stmt->execute([$enrollment_id, $user_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Successfully unenrolled from the course'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to unenroll from the course'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>