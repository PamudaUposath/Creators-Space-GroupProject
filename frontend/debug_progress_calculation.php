<?php
// Debug script to verify progress calculations are working correctly
session_start();
require_once '../backend/config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$userId = $_SESSION['user_id'];
$courseId = isset($_GET['course_id']) ? intval($_GET['course_id']) : 2; // Default to course 2

try {
    // Test 1: Check lesson_progress table data
    $lessonProgressStmt = $pdo->prepare("
        SELECT 
            lp.lesson_id,
            l.title as lesson_title,
            lp.completion_percentage,
            lp.actual_watch_time,
            lp.total_duration,
            lp.is_completed,
            lp.updated_at
        FROM lesson_progress lp
        JOIN lessons l ON lp.lesson_id = l.id
        WHERE lp.user_id = ? AND lp.course_id = ?
        ORDER BY l.position ASC
    ");
    $lessonProgressStmt->execute([$userId, $courseId]);
    $lessonProgressData = $lessonProgressStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Test 2: Calculate average progress manually
    $avgProgressStmt = $pdo->prepare("
        SELECT 
            AVG(lp.completion_percentage) as avg_progress,
            COUNT(*) as total_lessons_with_progress
        FROM lesson_progress lp
        JOIN lessons l ON lp.lesson_id = l.id
        WHERE lp.user_id = ? AND lp.course_id = ? AND l.is_published = 1
    ");
    $avgProgressStmt->execute([$userId, $courseId]);
    $avgProgressData = $avgProgressStmt->fetch(PDO::FETCH_ASSOC);
    
    // Test 3: Check enrollments table data
    $enrollmentStmt = $pdo->prepare("
        SELECT 
            overall_progress,
            last_accessed_lesson_id,
            last_watched_time,
            updated_at
        FROM enrollments
        WHERE user_id = ? AND course_id = ?
    ");
    $enrollmentStmt->execute([$userId, $courseId]);
    $enrollmentData = $enrollmentStmt->fetch(PDO::FETCH_ASSOC);
    
    // Test 4: Test my-courses API calculation
    $myCoursesStmt = $pdo->prepare("
        SELECT 
            c.id as course_id,
            c.title,
            e.overall_progress as cached_progress,
            COALESCE(
                (SELECT AVG(completion_percentage) 
                 FROM lesson_progress lp2 
                 JOIN lessons l2 ON lp2.lesson_id = l2.id 
                 WHERE lp2.user_id = e.user_id 
                   AND lp2.course_id = c.id 
                   AND l2.is_published = 1), 
                0
            ) as calculated_progress
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.user_id = ? AND c.id = ?
    ");
    $myCoursesStmt->execute([$userId, $courseId]);
    $myCoursesData = $myCoursesStmt->fetch(PDO::FETCH_ASSOC);
    
    // Test 5: Test continue data API calculation
    $continueDataStmt = $pdo->prepare("
        SELECT 
            COALESCE(
                (SELECT AVG(completion_percentage) 
                 FROM lesson_progress lp2 
                 JOIN lessons l2 ON lp2.lesson_id = l2.id 
                 WHERE lp2.user_id = ? 
                   AND lp2.course_id = ? 
                   AND l2.is_published = 1), 
                0
            ) as calculated_progress
    ");
    $continueDataStmt->execute([$userId, $courseId]);
    $continueDataData = $continueDataStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'user_id' => $userId,
        'course_id' => $courseId,
        'debug_data' => [
            'lesson_progress_records' => $lessonProgressData,
            'manual_avg_calculation' => $avgProgressData,
            'enrollment_cached_progress' => $enrollmentData,
            'my_courses_calculation' => $myCoursesData,
            'continue_data_calculation' => $continueDataData
        ],
        'summary' => [
            'lessons_with_progress' => count($lessonProgressData),
            'avg_progress_manual' => round(floatval($avgProgressData['avg_progress'] ?? 0), 2),
            'cached_progress' => round(floatval($enrollmentData['overall_progress'] ?? 0), 2),
            'my_courses_calculated' => round(floatval($myCoursesData['calculated_progress'] ?? 0), 2),
            'continue_data_calculated' => round(floatval($continueDataData['calculated_progress'] ?? 0), 2)
        ]
    ], JSON_PRETTY_PRINT);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
?>