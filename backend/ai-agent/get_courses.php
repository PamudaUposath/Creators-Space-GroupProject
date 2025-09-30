<?php
/**
 * Get courses for AI agent recommendations
 */

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

header('Content-Type: application/json');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    // Get filter parameters
    $category = $_GET['category'] ?? null;
    $level = $_GET['level'] ?? null;
    $limit = min(intval($_GET['limit'] ?? 10), 50); // Max 50 courses
    $featured = $_GET['featured'] ?? null;
    $search = $_GET['search'] ?? null;
    
    // Build query
    $sql = "SELECT 
                c.id, 
                c.title, 
                c.slug,
                c.description, 
                c.level, 
                c.category,
                c.duration, 
                c.price,
                c.image_url,
                c.featured,
                c.total_lessons,
                c.total_duration_minutes,
                u.first_name as instructor_name,
                u.last_name as instructor_last_name,
                COUNT(e.id) as enrollment_count
            FROM courses c
            LEFT JOIN users u ON c.instructor_id = u.id
            LEFT JOIN enrollments e ON c.id = e.course_id
            WHERE c.is_active = 1";
    
    $params = [];
    
    // Apply filters
    if ($category) {
        $sql .= " AND c.category = ?";
        $params[] = $category;
    }
    
    if ($level) {
        $sql .= " AND c.level = ?";
        $params[] = $level;
    }
    
    if ($featured === 'true') {
        $sql .= " AND c.featured = 1";
    }
    
    if ($search) {
        $sql .= " AND (c.title LIKE ? OR c.description LIKE ? OR c.category LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $sql .= " GROUP BY c.id ORDER BY c.featured DESC, enrollment_count DESC, c.created_at DESC LIMIT ?";
    $params[] = $limit;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format courses data
    $formattedCourses = [];
    foreach ($courses as $course) {
        $formattedCourse = [
            'id' => $course['id'],
            'title' => $course['title'],
            'slug' => $course['slug'],
            'description' => $course['description'] ? substr($course['description'], 0, 150) . '...' : 'No description available',
            'level' => $course['level'],
            'category' => $course['category'],
            'duration' => $course['duration'] ?? 'Self-paced',
            'price' => floatval($course['price']),
            'is_free' => floatval($course['price']) == 0,
            'image_url' => $course['image_url'],
            'featured' => (bool)$course['featured'],
            'total_lessons' => intval($course['total_lessons']),
            'total_duration_minutes' => intval($course['total_duration_minutes']),
            'instructor' => [
                'name' => trim(($course['instructor_name'] ?? '') . ' ' . ($course['instructor_last_name'] ?? '')),
            ],
            'enrollment_count' => intval($course['enrollment_count']),
            'rating' => rand(40, 50) / 10, // Placeholder rating system
            'category_display' => str_replace('-', ' ', ucwords($course['category'])),
            'level_display' => ucfirst($course['level'])
        ];
        
        $formattedCourses[] = $formattedCourse;
    }
    
    // Get total count for pagination info
    $countSql = "SELECT COUNT(*) as total FROM courses WHERE is_active = 1";
    $countParams = [];
    
    if ($category) {
        $countSql .= " AND category = ?";
        $countParams[] = $category;
    }
    
    if ($level) {
        $countSql .= " AND level = ?";
        $countParams[] = $level;
    }
    
    if ($featured === 'true') {
        $countSql .= " AND featured = 1";
    }
    
    if ($search) {
        $countSql .= " AND (title LIKE ? OR description LIKE ? OR category LIKE ?)";
        $searchTerm = "%$search%";
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
        $countParams[] = $searchTerm;
    }
    
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($countParams);
    $totalCourses = $countStmt->fetch()['total'];
    
    // Get available categories and levels
    $categoriesStmt = $pdo->query("
        SELECT category, COUNT(*) as count 
        FROM courses 
        WHERE is_active = 1 
        GROUP BY category 
        ORDER BY count DESC
    ");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $levelsStmt = $pdo->query("
        SELECT level, COUNT(*) as count 
        FROM courses 
        WHERE is_active = 1 
        GROUP BY level 
        ORDER BY 
            CASE level 
                WHEN 'beginner' THEN 1 
                WHEN 'intermediate' THEN 2 
                WHEN 'advanced' THEN 3 
                ELSE 4 
            END
    ");
    $levels = $levelsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'courses' => $formattedCourses,
        'meta' => [
            'total_courses' => intval($totalCourses),
            'returned_courses' => count($formattedCourses),
            'limit' => $limit,
            'filters' => [
                'category' => $category,
                'level' => $level,
                'featured' => $featured === 'true',
                'search' => $search
            ]
        ],
        'filters_available' => [
            'categories' => $categories,
            'levels' => $levels
        ]
    ]);

} catch (Exception $e) {
    error_log("Error fetching courses: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'error' => 'Could not fetch courses',
        'courses' => [],
        'meta' => [
            'total_courses' => 0,
            'returned_courses' => 0
        ]
    ]);
}
?>