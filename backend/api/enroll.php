<?php
/**
 * Enrollment API - Handle course enrollment
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

if (!isset($input['course_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Course ID is required'
    ]);
    exit();
}

$course_id = $input['course_id'];

try {
    // Check if course exists
    $stmt = $pdo->prepare("SELECT id, title, price FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$course) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Course not found'
        ]);
        exit();
    }
    
    // Check if user is already enrolled
    $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$user_id, $course_id]);
    if ($stmt->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already enrolled in this course'
        ]);
        exit();
    }
    
    // For free courses, enroll directly
    if ($course['price'] == 0) {
        $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, enrolled_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $course_id]);
        
        // Remove from cart if it exists
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND course_id = ?");
        $stmt->execute([$user_id, $course_id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Successfully enrolled in the course!'
        ]);
    } else {
        // For paid courses, redirect to payment or add to cart
        echo json_encode([
            'success' => false,
            'message' => 'This is a paid course. Please add to cart and proceed to checkout.',
            'redirect' => 'cart'
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