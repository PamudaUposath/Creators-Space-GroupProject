<?php
session_start();
require_once '../config/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'POST method required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$lessonId = isset($input['lesson_id']) ? intval($input['lesson_id']) : 0;
$courseId = isset($input['course_id']) ? intval($input['course_id']) : 0;
$currentTime = isset($input['current_time']) ? floatval($input['current_time']) : 0;
$totalDuration = isset($input['total_duration']) ? floatval($input['total_duration']) : 0;
$actualWatchTime = isset($input['actual_watch_time']) ? floatval($input['actual_watch_time']) : $currentTime;
$watchSessions = isset($input['watch_sessions']) ? intval($input['watch_sessions']) : 1;
$skippedDuration = isset($input['skipped_duration']) ? floatval($input['skipped_duration']) : 0;
$seekViolations = isset($input['seek_violations']) ? intval($input['seek_violations']) : 0;
$isProperlyWatched = isset($input['is_properly_watched']) ? boolval($input['is_properly_watched']) : true;
$isCompleted = isset($input['is_completed']) ? boolval($input['is_completed']) : false;
$userId = $_SESSION['user_id'];

if (!$lessonId || !$courseId) {
    echo json_encode(['success' => false, 'message' => 'Lesson ID and Course ID required']);
    exit;
}

try {
    // Calculate completion percentage based on actual watch time for certificate validation
    $completionPercentage = $totalDuration > 0 ? ($actualWatchTime / $totalDuration) * 100 : 0;
    $completionPercentage = min(100, max(0, $completionPercentage)); // Clamp between 0-100
    
    // Lesson is completed only if properly watched (90% actual watch time + minimal violations)
    $watchPercentage = $totalDuration > 0 ? ($actualWatchTime / $totalDuration) * 100 : 0;
    $isLessonCompleted = $isCompleted && $isProperlyWatched && $watchPercentage >= 90;
    
    // Certificate eligibility: must watch 90%+ with minimal skipping
    $isCertificateEligible = $watchPercentage >= 90 && $seekViolations <= 3 && $isProperlyWatched;
    
    // Insert or update lesson progress with watch validation data
    $stmt = $pdo->prepare("
        INSERT INTO lesson_progress 
        (user_id, lesson_id, course_id, last_watched_time, total_duration, completion_percentage, 
         actual_watch_time, watch_sessions, skipped_duration, seek_violations, 
         is_eligible_for_certificate, is_completed, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
        last_watched_time = VALUES(last_watched_time),
        total_duration = VALUES(total_duration),
        completion_percentage = VALUES(completion_percentage),
        actual_watch_time = GREATEST(actual_watch_time, VALUES(actual_watch_time)),
        watch_sessions = VALUES(watch_sessions),
        skipped_duration = VALUES(skipped_duration),
        seek_violations = VALUES(seek_violations),
        is_eligible_for_certificate = VALUES(is_eligible_for_certificate),
        is_completed = CASE 
            WHEN VALUES(is_completed) = 1 AND VALUES(is_eligible_for_certificate) = 1 
            THEN 1 
            ELSE is_completed 
        END,
        updated_at = NOW()
    ");
    
    $stmt->execute([
        $userId, 
        $lessonId, 
        $courseId, 
        $currentTime, 
        $totalDuration, 
        $completionPercentage,
        $actualWatchTime,
        $watchSessions,
        $skippedDuration,
        $seekViolations,
        $isCertificateEligible ? 1 : 0,
        $isLessonCompleted ? 1 : 0
    ]);
    
    // Calculate overall course progress and certificate eligibility
    $courseProgressStmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total_lessons,
            AVG(completion_percentage) as avg_completion,
            COUNT(CASE WHEN is_completed = 1 THEN 1 END) as completed_lessons,
            COUNT(CASE WHEN is_eligible_for_certificate = 1 THEN 1 END) as certificate_eligible_lessons,
            COUNT(CASE WHEN is_completed = 1 AND is_eligible_for_certificate = 1 THEN 1 END) as properly_completed_lessons
        FROM lesson_progress lp
        JOIN lessons l ON lp.lesson_id = l.id
        WHERE lp.user_id = ? AND lp.course_id = ? AND l.is_published = 1
    ");
    
    $courseProgressStmt->execute([$userId, $courseId]);
    $courseProgress = $courseProgressStmt->fetch(PDO::FETCH_ASSOC);
    
    $overallProgress = $courseProgress['avg_completion'] ?: 0;
    $properlyCompletedLessons = intval($courseProgress['properly_completed_lessons']);
    $totalLessons = intval($courseProgress['total_lessons']);
    
    // Course certificate eligibility: all lessons must be properly completed
    $courseCertificateEligible = $totalLessons > 0 && $properlyCompletedLessons >= $totalLessons;
    
    // Update enrollment progress and certificate eligibility
    $updateEnrollmentStmt = $pdo->prepare("
        UPDATE enrollments 
        SET 
            overall_progress = ?,
            last_accessed_lesson_id = ?,
            last_watched_time = ?,
            certificate_eligible = ?,
            lessons_completed_properly = ?,
            total_lessons_required = ?,
            last_accessed_at = NOW()
        WHERE user_id = ? AND course_id = ?
    ");
    
    $updateEnrollmentStmt->execute([
        $overallProgress,
        $lessonId,
        $currentTime,
        $courseCertificateEligible ? 1 : 0,
        $properlyCompletedLessons,
        $totalLessons,
        $userId,
        $courseId
    ]);
    
    echo json_encode([
        'success' => true,
        'progress' => [
            'lesson_completion' => $completionPercentage,
            'actual_watch_percentage' => $watchPercentage,
            'is_lesson_completed' => $isLessonCompleted,
            'is_certificate_eligible' => $isCertificateEligible,
            'course_progress' => $overallProgress,
            'completed_lessons' => intval($courseProgress['completed_lessons']),
            'properly_completed_lessons' => $properlyCompletedLessons,
            'total_lessons' => $totalLessons,
            'certificate_eligible' => $courseCertificateEligible,
            'watch_validation' => [
                'actual_watch_time' => $actualWatchTime,
                'total_duration' => $totalDuration,
                'watch_sessions' => $watchSessions,
                'seek_violations' => $seekViolations,
                'skipped_duration' => $skippedDuration
            ]
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error saving progress: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to save progress']);
}
?>