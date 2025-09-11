<?php
// backend/add_sample_data.php - Add sample data for testing

require_once __DIR__ . '/config/db_connect.php';

try {
    // Sample users data
    $sampleUsers = [
        [
            'first_name' => 'Alice',
            'last_name' => 'Johnson',
            'username' => 'alice.johnson',
            'email' => 'alice.johnson@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'Bob',
            'last_name' => 'Smith',
            'username' => 'bob.smith',
            'email' => 'bob.smith@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'Carol',
            'last_name' => 'Davis',
            'username' => 'carol.davis',
            'email' => 'carol.davis@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'David',
            'last_name' => 'Wilson',
            'username' => 'david.wilson',
            'email' => 'david.wilson@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'Emma',
            'last_name' => 'Brown',
            'username' => 'emma.brown',
            'email' => 'emma.brown@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'Frank',
            'last_name' => 'Miller',
            'username' => 'frank.miller',
            'email' => 'frank.miller@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'Grace',
            'last_name' => 'Taylor',
            'username' => 'grace.taylor',
            'email' => 'grace.taylor@example.com',
            'password' => 'password123',
            'role' => 'user'
        ],
        [
            'first_name' => 'Henry',
            'last_name' => 'Anderson',
            'username' => 'henry.anderson',
            'email' => 'henry.anderson@example.com',
            'password' => 'password123',
            'role' => 'user'
        ]
    ];

    // Sample courses data
    $sampleCourses = [
        [
            'title' => 'Full Stack Web Development',
            'description' => 'Complete course covering frontend and backend development',
            'instructor_id' => 2, // Instructor user
            'price' => 299.99,
            'duration' => '12 weeks',
            'level' => 'intermediate'
        ],
        [
            'title' => 'Python for Beginners',
            'description' => 'Learn Python programming from scratch',
            'instructor_id' => 2,
            'price' => 199.99,
            'duration' => '8 weeks',
            'level' => 'beginner'
        ],
        [
            'title' => 'React.js Masterclass',
            'description' => 'Advanced React.js concepts and best practices',
            'instructor_id' => 2,
            'price' => 249.99,
            'duration' => '10 weeks',
            'level' => 'advanced'
        ],
        [
            'title' => 'Data Science with Python',
            'description' => 'Learn data analysis and machine learning',
            'instructor_id' => 2,
            'price' => 349.99,
            'duration' => '16 weeks',
            'level' => 'intermediate'
        ],
        [
            'title' => 'UI/UX Design Fundamentals',
            'description' => 'Design principles and user experience',
            'instructor_id' => 2,
            'price' => 179.99,
            'duration' => '6 weeks',
            'level' => 'beginner'
        ]
    ];

    echo "🚀 Adding sample data to the database...\n\n";

    // Add sample users
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, username, email, password_hash, role, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 1, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 30) DAY))
    ");

    $usersAdded = 0;
    foreach ($sampleUsers as $user) {
        // Check if user already exists
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$user['email']]);
        
        if (!$checkStmt->fetch()) {
            $password_hash = password_hash($user['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $user['first_name'],
                $user['last_name'],
                $user['username'],
                $user['email'],
                $password_hash,
                $user['role']
            ]);
            $usersAdded++;
            echo "✅ Added user: {$user['first_name']} {$user['last_name']} ({$user['email']})\n";
        } else {
            echo "⏭️  User already exists: {$user['email']}\n";
        }
    }

    // Add sample courses
    $stmt = $pdo->prepare("
        INSERT INTO courses (title, description, instructor_id, price, duration, level, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 1, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 60) DAY))
    ");

    $coursesAdded = 0;
    foreach ($sampleCourses as $course) {
        // Check if course already exists
        $checkStmt = $pdo->prepare("SELECT id FROM courses WHERE title = ?");
        $checkStmt->execute([$course['title']]);
        
        if (!$checkStmt->fetch()) {
            $stmt->execute([
                $course['title'],
                $course['description'],
                $course['instructor_id'],
                $course['price'],
                $course['duration'],
                $course['level']
            ]);
            $coursesAdded++;
            echo "✅ Added course: {$course['title']}\n";
        } else {
            echo "⏭️  Course already exists: {$course['title']}\n";
        }
    }

    // Get user IDs and course IDs for enrollments
    $userIds = $pdo->query("SELECT id FROM users WHERE role = 'user'")->fetchAll(PDO::FETCH_COLUMN);
    $courseIds = $pdo->query("SELECT id FROM courses")->fetchAll(PDO::FETCH_COLUMN);

    // Add sample enrollments
    if (!empty($userIds) && !empty($courseIds)) {
        $stmt = $pdo->prepare("
            INSERT INTO enrollments (user_id, course_id, enrolled_at, progress) 
            VALUES (?, ?, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 45) DAY), FLOOR(RAND() * 100))
        ");

        $enrollmentsAdded = 0;
        $maxEnrollments = min(20, count($userIds) * 2); // Each user can enroll in multiple courses

        for ($i = 0; $i < $maxEnrollments; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $courseId = $courseIds[array_rand($courseIds)];

            // Check if enrollment already exists
            $checkStmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
            $checkStmt->execute([$userId, $courseId]);

            if (!$checkStmt->fetch()) {
                $stmt->execute([$userId, $courseId]);
                $enrollmentsAdded++;
            }
        }
        echo "✅ Added {$enrollmentsAdded} enrollments\n";
    }

    echo "\n📊 Database Summary:\n";
    echo "==================\n";

    // Show statistics
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    $totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    $totalEnrollments = $pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
    $totalInstructors = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'instructor'")->fetchColumn();

    echo "👥 Total Users: {$totalUsers}\n";
    echo "📚 Total Courses: {$totalCourses}\n";
    echo "📝 Total Enrollments: {$totalEnrollments}\n";
    echo "👨‍🏫 Total Instructors: {$totalInstructors}\n";

    echo "\n🎉 Sample data added successfully!\n";
    echo "You can now view the admin dashboard with populated data.\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
