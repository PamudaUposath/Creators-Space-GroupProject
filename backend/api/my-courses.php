<?php
/**
 * My Courses API - Fetch user's enrolled courses
 * Creators-Space Project
 */

session_start();
require_once '../config/db_connect.php';

// Enable CORS for API requests
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
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

if ($method !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

try {
    // First, check what columns exist in the courses table
    $stmt = $pdo->query("DESCRIBE courses");
    $courseColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Build the SELECT clause dynamically based on available columns
    $selectFields = [
        'c.id as course_id',
        'c.title',
        'c.description',
        'c.instructor_id',
        'c.created_at as course_created_at',
        'e.id as enrollment_id',
        'COALESCE(e.overall_progress, 0) as overall_progress',
        'e.last_accessed_lesson_id',
        'e.last_watched_time',
        'e.enrolled_at',
        'e.completed_at',
        'e.last_accessed',
        'e.progress',
        'e.current_lesson_id',
        'e.status',
        'u.first_name',
        'u.last_name'
    ];
    
    // Add optional columns if they exist
    if (in_array('price', $courseColumns)) {
        $selectFields[] = 'c.price';
    }
    if (in_array('category', $courseColumns)) {
        $selectFields[] = 'c.category';
    }
    if (in_array('level', $courseColumns)) {
        $selectFields[] = 'c.level';
    }
    if (in_array('duration', $courseColumns)) {
        $selectFields[] = 'c.duration';
    }
    if (in_array('language', $courseColumns)) {
        $selectFields[] = 'c.language';
    }
    
    // Fetch user's enrolled courses with course details
    $stmt = $pdo->prepare("
        SELECT " . implode(', ', $selectFields) . "
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        LEFT JOIN users u ON c.instructor_id = u.id
        WHERE e.user_id = ?
        ORDER BY e.enrolled_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $courses = [];
    foreach ($enrollments as $enrollment) {
        $courses[] = [
            'id' => $enrollment['course_id'],
            'enrollment_id' => $enrollment['enrollment_id'],
            'title' => $enrollment['title'] ?? 'Untitled Course',
            'description' => $enrollment['description'] ?? 'No description available',
            'instructor_name' => trim(($enrollment['first_name'] ?? '') . ' ' . ($enrollment['last_name'] ?? '')) ?: 'Unknown Instructor',
            'enrolled_at' => $enrollment['enrolled_at'] ?? null,
            'course_created_at' => $enrollment['course_created_at'] ?? null,
            'image' => $enrollment['image_url'] ?? $enrollment['image'] ?? './assets/images/webdev.png',
            'price' => $enrollment['price'] ?? null,
            'category' => $enrollment['category'] ?? 'General',
            'level' => $enrollment['level'] ?? 'beginner',
            'duration' => $enrollment['duration'] ?? null,
            'language' => $enrollment['language'] ?? 'english',
            'overall_progress' => floatval($enrollment['overall_progress'] ?? 0),
            'last_accessed_lesson_id' => $enrollment['last_accessed_lesson_id'],
            'last_watched_time' => floatval($enrollment['last_watched_time'] ?? 0),
            'enrollment' => [
                'enrolled_at' => $enrollment['enrolled_at'],
                'status' => $enrollment['status'] ?? 'active'
            ]
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $courses,
        'count' => count($courses)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>
