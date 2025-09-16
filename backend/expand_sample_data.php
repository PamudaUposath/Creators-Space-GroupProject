<?php
// Add more realistic sample data to make the stats more impressive
require_once __DIR__ . '/config/db_connect.php';

try {
    echo "🚀 Adding more sample data for better statistics...\n\n";
    
    // Add more sample users (students)
    $additionalUsers = [
        ['John', 'Doe', 'john.doe@example.com', 'john.doe'],
        ['Jane', 'Smith', 'jane.smith@example.com', 'jane.smith'],
        ['Mike', 'Johnson', 'mike.johnson@example.com', 'mike.johnson'],
        ['Sarah', 'Williams', 'sarah.williams@example.com', 'sarah.williams'],
        ['Alex', 'Brown', 'alex.brown@example.com', 'alex.brown'],
        ['Lisa', 'Davis', 'lisa.davis@example.com', 'lisa.davis'],
        ['Tom', 'Wilson', 'tom.wilson@example.com', 'tom.wilson'],
        ['Emma', 'Garcia', 'emma.garcia@example.com', 'emma.garcia'],
        ['Ryan', 'Miller', 'ryan.miller@example.com', 'ryan.miller'],
        ['Amy', 'Anderson', 'amy.anderson@example.com', 'amy.anderson'],
        ['Chris', 'Taylor', 'chris.taylor@example.com', 'chris.taylor'],
        ['Ashley', 'Thomas', 'ashley.thomas@example.com', 'ashley.thomas'],
        ['David', 'Jackson', 'david.jackson@example.com', 'david.jackson'],
        ['Jessica', 'White', 'jessica.white@example.com', 'jessica.white'],
        ['Mark', 'Harris', 'mark.harris@example.com', 'mark.harris'],
        ['Nicole', 'Clark', 'nicole.clark@example.com', 'nicole.clark'],
        ['Kevin', 'Lewis', 'kevin.lewis@example.com', 'kevin.lewis'],
        ['Rachel', 'Lee', 'rachel.lee@example.com', 'rachel.lee'],
        ['Steve', 'Walker', 'steve.walker@example.com', 'steve.walker'],
        ['Michelle', 'Hall', 'michelle.hall@example.com', 'michelle.hall']
    ];
    
    $userStmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, username, password_hash, role) 
        VALUES (?, ?, ?, ?, ?, 'user')
    ");
    
    $addedUsers = 0;
    foreach ($additionalUsers as $user) {
        try {
            $userStmt->execute([
                $user[0], 
                $user[1], 
                $user[2], 
                $user[3], 
                password_hash('password123', PASSWORD_DEFAULT)
            ]);
            $addedUsers++;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo "⏭️  User already exists: {$user[2]}\n";
            } else {
                throw $e;
            }
        }
    }
    echo "✅ Added $addedUsers new student users\n";
    
    // Add more instructors
    $instructors = [
        ['Dr. Sarah', 'Johnson', 'sarah.instructor@example.com', 'sarah.instructor'],
        ['Prof. Michael', 'Chen', 'michael.instructor@example.com', 'michael.instructor'],
        ['Dr. Emily', 'Rodriguez', 'emily.instructor@example.com', 'emily.instructor'],
        ['Prof. James', 'Kumar', 'james.instructor@example.com', 'james.instructor']
    ];
    
    $addedInstructors = 0;
    foreach ($instructors as $instructor) {
        try {
            $userStmt->execute([
                $instructor[0], 
                $instructor[1], 
                $instructor[2], 
                $instructor[3], 
                password_hash('instructor123', PASSWORD_DEFAULT)
            ]);
            
            // Update the role to instructor
            $updateStmt = $pdo->prepare("UPDATE users SET role = 'instructor' WHERE email = ?");
            $updateStmt->execute([$instructor[2]]);
            $addedInstructors++;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo "⏭️  Instructor already exists: {$instructor[2]}\n";
            } else {
                throw $e;
            }
        }
    }
    echo "✅ Added $addedInstructors new instructors\n";
    
    // Add more courses
    $additionalCourses = [
        ['Machine Learning Fundamentals', 'Learn the basics of ML and AI', 'machine-learning-fundamentals'],
        ['JavaScript Advanced Concepts', 'Master advanced JavaScript programming', 'javascript-advanced-concepts'],
        ['Digital Marketing Mastery', 'Complete guide to digital marketing', 'digital-marketing-mastery'],
        ['Mobile App Development', 'Build mobile apps with React Native', 'mobile-app-development'],
        ['Cybersecurity Essentials', 'Learn to protect digital assets', 'cybersecurity-essentials'],
        ['Cloud Computing with AWS', 'Master Amazon Web Services', 'cloud-computing-aws'],
        ['Blockchain Development', 'Build decentralized applications', 'blockchain-development'],
        ['Game Development with Unity', 'Create amazing games', 'game-development-unity']
    ];
    
    $courseStmt = $pdo->prepare("
        INSERT INTO courses (title, description, slug, instructor_id, price, level) 
        VALUES (?, ?, ?, (SELECT id FROM users WHERE role = 'instructor' ORDER BY RAND() LIMIT 1), 99.99, 'intermediate')
    ");
    
    $addedCourses = 0;
    foreach ($additionalCourses as $course) {
        try {
            $courseStmt->execute([$course[0], $course[1], $course[2]]);
            $addedCourses++;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                echo "⏭️  Course already exists: {$course[0]}\n";
            } else {
                throw $e;
            }
        }
    }
    echo "✅ Added $addedCourses new courses\n";
    
    // Add enrollments for new users
    $enrollmentStmt = $pdo->prepare("
        INSERT IGNORE INTO enrollments (user_id, course_id, progress) 
        VALUES (?, ?, ?)
    ");
    
    // Get all users and courses
    $users = $pdo->query("SELECT id FROM users WHERE role = 'user'")->fetchAll(PDO::FETCH_COLUMN);
    $courses = $pdo->query("SELECT id FROM courses WHERE is_active = 1")->fetchAll(PDO::FETCH_COLUMN);
    
    $addedEnrollments = 0;
    foreach ($users as $userId) {
        // Randomly enroll users in 1-3 courses
        $numCourses = rand(1, 3);
        $selectedCourses = array_rand($courses, min($numCourses, count($courses)));
        if (!is_array($selectedCourses)) $selectedCourses = [$selectedCourses];
        
        foreach ($selectedCourses as $courseIndex) {
            $courseId = $courses[$courseIndex];
            $progress = rand(0, 100);
            
            try {
                $enrollmentStmt->execute([$userId, $courseId, $progress]);
                $addedEnrollments++;
            } catch (PDOException $e) {
                // Skip duplicates
            }
        }
    }
    echo "✅ Added $addedEnrollments new enrollments\n";
    
    // Mark some enrollments as completed
    $completionStmt = $pdo->prepare("
        UPDATE enrollments 
        SET progress = 100, completed_at = NOW() 
        WHERE progress >= 80 AND completed_at IS NULL
        LIMIT 15
    ");
    $completionStmt->execute();
    $completedCourses = $completionStmt->rowCount();
    echo "✅ Marked $completedCourses enrollments as completed\n";
    
    // Test the new statistics
    require_once __DIR__ . '/lib/helpers.php';
    $stats = getPlatformStatistics($pdo);
    
    echo "\n📊 Updated Statistics:\n";
    echo "==================\n";
    echo "Students Enrolled: {$stats['students_display']}\n";
    echo "Expert Instructors: {$stats['instructors_display']}\n";
    echo "Courses Available: {$stats['courses_display']}\n";
    echo "Success Rate: {$stats['success_rate_display']}\n";
    echo "\n🎉 Sample data expansion completed!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>