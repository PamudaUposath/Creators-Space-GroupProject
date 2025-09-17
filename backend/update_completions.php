<?php
// Add some completed enrollments to make the success rate more realistic
require_once __DIR__ . '/config/db_connect.php';

try {
    // Mark some enrollments as completed (progress = 100%)
    $updateStmt = $pdo->prepare("
        UPDATE enrollments 
        SET progress = 100.00, completed_at = NOW() 
        WHERE user_id IN (1, 2, 3, 4, 5) 
        AND course_id IN (1, 2, 3)
        LIMIT 10
    ");
    
    $updateStmt->execute();
    $updatedRows = $updateStmt->rowCount();
    
    echo "✅ Updated $updatedRows enrollments to completed status\n";
    
    // Test the statistics again
    require_once __DIR__ . '/lib/helpers.php';
    $stats = getPlatformStatistics($pdo);
    
    echo "\n📊 Updated Statistics:\n";
    echo "==================\n";
    echo "Students Enrolled: {$stats['students_display']}\n";
    echo "Expert Instructors: {$stats['instructors_display']}\n";
    echo "Courses Available: {$stats['courses_display']}\n";
    echo "Success Rate: {$stats['success_rate_display']}\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>