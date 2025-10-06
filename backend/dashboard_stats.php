<?php
// backend/dashboard_stats.php - Get current dashboard statistics

require_once __DIR__ . '/config/db_connect.php';

try {
    echo "📊 Current Dashboard Statistics:\n";
    echo "================================\n\n";

    // Get detailed user statistics
    $stmt = $pdo->query("
        SELECT 
            role,
            COUNT(*) as count,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count
        FROM users 
        GROUP BY role
    ");
    $userStats = $stmt->fetchAll();

    echo "👥 Users by Role:\n";
    foreach ($userStats as $stat) {
        echo "   {$stat['role']}: {$stat['count']} total ({$stat['active_count']} active)\n";
    }

    // Recent users
    echo "\n👤 Recent Users (Last 5):\n";
    $stmt = $pdo->query("
        SELECT first_name, last_name, email, role, created_at 
        FROM users 
        WHERE role = 'user' 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recentUsers = $stmt->fetchAll();

    foreach ($recentUsers as $user) {
        echo "   • {$user['first_name']} {$user['last_name']} ({$user['email']}) - " . 
             date('M j, Y', strtotime($user['created_at'])) . "\n";
    }

    // Course statistics
    echo "\n📚 Course Statistics:\n";
    $stmt = $pdo->query("
        SELECT 
            c.title,
            c.level,
            c.price,
            COUNT(e.id) as enrollment_count,
            u.first_name as instructor_first,
            u.last_name as instructor_last
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        LEFT JOIN users u ON c.instructor_id = u.id
        GROUP BY c.id, c.title, c.level, c.price, u.first_name, u.last_name
        ORDER BY enrollment_count DESC
    ");
    $courseStats = $stmt->fetchAll();

    foreach ($courseStats as $course) {
        echo "   • {$course['title']} ({$course['level']}) - $" . number_format($course['price'], 2) . 
             " - {$course['enrollment_count']} enrollments\n";
        echo "     Instructor: {$course['instructor_first']} {$course['instructor_last']}\n";
    }

    // Enrollment statistics
    echo "\n📝 Enrollment Statistics:\n";
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(enrolled_at, '%Y-%m') as month,
            COUNT(*) as enrollments
        FROM enrollments 
        GROUP BY DATE_FORMAT(enrolled_at, '%Y-%m') 
        ORDER BY month DESC 
        LIMIT 6
    ");
    $enrollmentStats = $stmt->fetchAll();

    foreach ($enrollmentStats as $stat) {
        echo "   • {$stat['month']}: {$stat['enrollments']} enrollments\n";
    }

    // Overall totals
    echo "\n🎯 Overall Totals:\n";
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user' AND (remove IS NULL OR remove = 0)")->fetchColumn();
    $totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $totalEnrollments = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $totalInstructors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'instructor' AND (remove IS NULL OR remove = 0)")->fetchColumn();
    $totalRevenue = $pdo->query("
        SELECT SUM(c.price) 
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id
    ")->fetchColumn() ?: 0;

    echo "   • Total Users: {$totalUsers}\n";
    echo "   • Total Courses: {$totalCourses}\n";
    echo "   • Total Enrollments: {$totalEnrollments}\n";
    echo "   • Total Instructors: {$totalInstructors}\n";
    echo "   • Total Revenue: $" . number_format($totalRevenue, 2) . "\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
