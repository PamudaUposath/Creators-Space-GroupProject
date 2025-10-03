<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$userId = $_SESSION['user_id'];

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Course ID required']);
    exit;
}

try {
    // Get the last watched lesson for the user in this course
    $stmt = $pdo->prepare("
        SELECT 
            e.last_accessed_lesson_id,
            e.last_watched_time,
            e.overall_progress,
            l.title as lesson_title,
            l.video_url,
            lp.last_watched_time as lesson_progress_time,
            lp.completion_percentage
        FROM enrollments e
        LEFT JOIN lessons l ON e.last_accessed_lesson_id = l.id
        LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = e.user_id
        WHERE e.user_id = ? AND e.course_id = ? AND e.status = 'active'
    ");
    
    $stmt->execute([$userId, $courseId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Enrollment not found']);
        exit;
    }
    
    // If no last accessed lesson, get the first lesson
    if (!$result['last_accessed_lesson_id']) {
        $firstLessonStmt = $pdo->prepare("
            SELECT id, title, video_url 
            FROM lessons 
            WHERE course_id = ? AND is_published = 1 
            ORDER BY position ASC 
            LIMIT 1
        ");
        $firstLessonStmt->execute([$courseId]);
        $firstLesson = $firstLessonStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($firstLesson) {
            $result['last_accessed_lesson_id'] = $firstLesson['id'];
            $result['lesson_title'] = $firstLesson['title'];
            $result['video_url'] = $firstLesson['video_url'];
            $result['lesson_progress_time'] = 0;
            $result['completion_percentage'] = 0;
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'No lessons found for this course',
                'debug' => [
                    'course_id' => $courseId,
                    'user_id' => $userId,
                    'enrollment_found' => true,
                    'lessons_found' => false
                ]
            ]);
            exit;
        }
    }
    
    // Check if video URL exists
    if (empty($result['video_url'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Video content not available for this lesson',
            'debug' => [
                'course_id' => $courseId,
                'user_id' => $userId,
                'lesson_id' => $result['last_accessed_lesson_id'],
                'video_url_empty' => true
            ]
        ]);
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'continue_data' => [
            'lesson_id' => $result['last_accessed_lesson_id'],
            'lesson_title' => $result['lesson_title'],
            'video_url' => $result['video_url'],
            'last_watched_time' => floatval($result['lesson_progress_time'] ?: 0),
            'overall_progress' => floatval($result['overall_progress'] ?: 0),
            'lesson_completion' => floatval($result['completion_percentage'] ?: 0)
        ],
        'debug' => [
            'course_id' => $courseId,
            'user_id' => $userId,
            'enrollment_found' => true,
            'has_last_accessed' => !empty($result['last_accessed_lesson_id']),
            'video_url_exists' => !empty($result['video_url'])
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error getting continue data: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>