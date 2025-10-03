<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$lessonId = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;
$userId = $_SESSION['user_id'];

if (!$lessonId) {
    echo json_encode(['success' => false, 'message' => 'Lesson ID required']);
    exit;
}

try {
    // Get lesson details with enrollment check
    $stmt = $pdo->prepare("
        SELECT 
            l.*,
            c.title as course_title,
            c.id as course_id,
            e.status as enrollment_status,
            COALESCE(lp.last_watched_time, 0) as last_watched_time,
            COALESCE(lp.total_duration, 0) as saved_duration,
            COALESCE(lp.completion_percentage, 0) as completion_percentage,
            COALESCE(lp.is_completed, 0) as is_completed
        FROM lessons l 
        JOIN courses c ON l.course_id = c.id
        LEFT JOIN enrollments e ON c.id = e.course_id AND e.user_id = ?
        LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = ?
        WHERE l.id = ? AND l.is_published = 1
    ");
    
    $stmt->execute([$userId, $userId, $lessonId]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$lesson) {
        echo json_encode(['success' => false, 'message' => 'Lesson not found']);
        exit;
    }
    
    // Check access permissions
    $hasAccess = false;
    
    if ($lesson['is_free']) {
        $hasAccess = true;
    } elseif ($lesson['enrollment_status'] === 'active') {
        $hasAccess = true;
    }
    
    if (!$hasAccess) {
        echo json_encode([
            'success' => false, 
            'message' => 'Access denied. Please enroll in this course to watch this lesson.'
        ]);
        exit;
    }
    
    // Update last accessed lesson in enrollment
    if ($lesson['enrollment_status'] === 'active') {
        $updateStmt = $pdo->prepare("
            UPDATE enrollments 
            SET last_accessed_lesson_id = ?, last_accessed_at = NOW() 
            WHERE user_id = ? AND course_id = ?
        ");
        $updateStmt->execute([$lessonId, $userId, $lesson['course_id']]);
    }
    
    // Return lesson data with progress
    echo json_encode([
        'success' => true,
        'lesson' => [
            'id' => $lesson['id'],
            'title' => $lesson['title'],
            'content' => $lesson['content'],
            'video_url' => $lesson['video_url'],
            'duration' => $lesson['duration'],
            'course_title' => $lesson['course_title'],
            'course_id' => $lesson['course_id'],
            'last_watched_time' => floatval($lesson['last_watched_time']),
            'saved_duration' => floatval($lesson['saved_duration']),
            'completion_percentage' => floatval($lesson['completion_percentage']),
            'is_completed' => boolval($lesson['is_completed'])
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error fetching lesson: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>