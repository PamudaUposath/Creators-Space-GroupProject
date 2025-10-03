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
    // Check enrollment and certificate eligibility
    $enrollmentStmt = $pdo->prepare("
        SELECT 
            e.certificate_eligible,
            e.lessons_completed_properly,
            e.total_lessons_required,
            e.overall_progress,
            c.title as course_title
        FROM enrollments e
        JOIN courses c ON e.course_id = c.id
        WHERE e.user_id = ? AND e.course_id = ? AND e.status = 'active'
    ");
    
    $enrollmentStmt->execute([$userId, $courseId]);
    $enrollment = $enrollmentStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$enrollment) {
        echo json_encode(['success' => false, 'message' => 'Enrollment not found']);
        exit;
    }
    
    // Get detailed lesson progress for certificate validation
    $lessonsStmt = $pdo->prepare("
        SELECT 
            l.id,
            l.title,
            COALESCE(lp.completion_percentage, 0) as completion_percentage,
            COALESCE(lp.actual_watch_time, 0) as actual_watch_time,
            COALESCE(lp.total_duration, 0) as total_duration,
            COALESCE(lp.seek_violations, 0) as seek_violations,
            COALESCE(lp.is_eligible_for_certificate, 0) as is_eligible_for_certificate,
            COALESCE(lp.is_completed, 0) as is_completed
        FROM lessons l
        LEFT JOIN lesson_progress lp ON l.id = lp.lesson_id AND lp.user_id = ?
        WHERE l.course_id = ? AND l.is_published = 1
        ORDER BY l.position ASC
    ");
    
    $lessonsStmt->execute([$userId, $courseId]);
    $lessons = $lessonsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Calculate certificate eligibility requirements
    $totalLessons = count($lessons);
    $eligibleLessons = 0;
    $completedLessons = 0;
    $totalWatchTime = 0;
    $totalVideoDuration = 0;
    $totalViolations = 0;
    
    $lessonDetails = [];
    
    foreach ($lessons as $lesson) {
        $watchPercentage = $lesson['total_duration'] > 0 ? 
            ($lesson['actual_watch_time'] / $lesson['total_duration']) * 100 : 0;
        
        $isEligible = $lesson['is_eligible_for_certificate'] && 
                     $watchPercentage >= 90 && 
                     $lesson['seek_violations'] <= 3;
        
        if ($isEligible) $eligibleLessons++;
        if ($lesson['is_completed']) $completedLessons++;
        
        $totalWatchTime += $lesson['actual_watch_time'];
        $totalVideoDuration += $lesson['total_duration'];
        $totalViolations += $lesson['seek_violations'];
        
        $lessonDetails[] = [
            'id' => $lesson['id'],
            'title' => $lesson['title'],
            'completion_percentage' => floatval($lesson['completion_percentage']),
            'watch_percentage' => round($watchPercentage, 2),
            'seek_violations' => intval($lesson['seek_violations']),
            'is_eligible' => $isEligible,
            'is_completed' => boolval($lesson['is_completed'])
        ];
    }
    
    $overallWatchPercentage = $totalVideoDuration > 0 ? 
        ($totalWatchTime / $totalVideoDuration) * 100 : 0;
    
    // Certificate eligibility criteria:
    // 1. All lessons must be watched properly (90%+ actual watch time)
    // 2. Minimal seek violations across all lessons
    // 3. All lessons marked as eligible for certificate
    $certificateEligible = $totalLessons > 0 && 
                          $eligibleLessons >= $totalLessons && 
                          $totalViolations <= ($totalLessons * 3) && 
                          $overallWatchPercentage >= 85;
    
    // Update enrollment certificate status if changed
    if ($enrollment['certificate_eligible'] != $certificateEligible) {
        $updateStmt = $pdo->prepare("
            UPDATE enrollments 
            SET certificate_eligible = ?, lessons_completed_properly = ?
            WHERE user_id = ? AND course_id = ?
        ");
        $updateStmt->execute([$certificateEligible ? 1 : 0, $eligibleLessons, $userId, $courseId]);
    }
    
    echo json_encode([
        'success' => true,
        'certificate_data' => [
            'course_title' => $enrollment['course_title'],
            'certificate_eligible' => $certificateEligible,
            'overall_progress' => floatval($enrollment['overall_progress']),
            'overall_watch_percentage' => round($overallWatchPercentage, 2),
            'requirements' => [
                'total_lessons' => $totalLessons,
                'eligible_lessons' => $eligibleLessons,
                'completed_lessons' => $completedLessons,
                'total_violations' => $totalViolations,
                'max_allowed_violations' => $totalLessons * 3
            ],
            'validation_criteria' => [
                'min_watch_percentage' => 85,
                'max_violations_per_lesson' => 3,
                'all_lessons_eligible' => $eligibleLessons >= $totalLessons,
                'watch_percentage_met' => $overallWatchPercentage >= 85,
                'violation_limit_met' => $totalViolations <= ($totalLessons * 3)
            ],
            'lesson_details' => $lessonDetails
        ]
    ]);
    
} catch (PDOException $e) {
    error_log("Error checking certificate eligibility: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>