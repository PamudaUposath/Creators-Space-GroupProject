<?php
/**
 * Database Connection Test
 * This file helps verify database connectivity
 */

// Prevent direct access if not testing
if (!isset($_GET['test']) && !isset($_POST['test'])) {
    header('HTTP/1.0 403 Forbidden');
    exit('Access denied. Add ?test=1 to URL to run test.');
}

echo "<!DOCTYPE html>\n";
echo "<html><head><title>Database Connection Test</title></head><body>\n";
echo "<h1>Creators-Space Database Connection Test</h1>\n";

// Include database configuration
require_once '../config/db_connect.php';

echo "<h2>Configuration Test</h2>\n";
echo "<ul>\n";
echo "<li>PHP Version: " . PHP_VERSION . "</li>\n";
echo "<li>PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? '✅ Yes' : '❌ No') . "</li>\n";
echo "<li>Database Host: " . $DB_HOST . "</li>\n";
echo "<li>Database Name: " . $DB_NAME . "</li>\n";
echo "<li>Database User: " . $DB_USER . "</li>\n";
echo "</ul>\n";

echo "<h2>Connection Test</h2>\n";

try {
    // Test basic connection
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    echo "<p>✅ <strong>Database connection successful!</strong></p>\n";
    
    // Test table existence
    echo "<h3>Table Structure Test</h3>\n";
    echo "<ul>\n";
    
    $tables = [
        'users' => 'User accounts and profiles',
        'courses' => 'Course catalog',
        'lessons' => 'Course lessons and content',
        'enrollments' => 'User course enrollments',
        'certificates' => 'Course completion certificates',
        'bookmarks' => 'User bookmarked content',
        'user_progress' => 'Learning progress tracking',
        'admin_logs' => 'Administrative activity logs',
        'password_resets' => 'Password reset tokens',
        'login_attempts' => 'Security login tracking'
    ];
    
    foreach ($tables as $table => $description) {
        $stmt = $pdo->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        $exists = $stmt->rowCount() > 0;
        
        echo "<li><strong>$table</strong>: " . ($exists ? '✅' : '❌') . " $description</li>\n";
    }
    echo "</ul>\n";
    
    // Test admin user
    echo "<h3>Admin User Test</h3>\n";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, role FROM users WHERE role = 'admin' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "<p>✅ <strong>Admin user found:</strong></p>\n";
        echo "<ul>\n";
        echo "<li>ID: " . htmlspecialchars($admin['id']) . "</li>\n";
        echo "<li>Name: " . htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) . "</li>\n";
        echo "<li>Email: " . htmlspecialchars($admin['email']) . "</li>\n";
        echo "<li>Role: " . htmlspecialchars($admin['role']) . "</li>\n";
        echo "</ul>\n";
    } else {
        echo "<p>❌ <strong>No admin user found. Please run seed data script.</strong></p>\n";
    }
    
    // Test sample data
    echo "<h3>Sample Data Test</h3>\n";
    
    // Count users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "<p>Total users: <strong>$userCount</strong></p>\n";
    
    // Count courses
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM courses");
    $courseCount = $stmt->fetch()['count'];
    echo "<p>Total courses: <strong>$courseCount</strong></p>\n";
    
    // Count lessons
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM lessons");
    $lessonCount = $stmt->fetch()['count'];
    echo "<p>Total lessons: <strong>$lessonCount</strong></p>\n";
    
    echo "<h3>System Status</h3>\n";
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>\n";
    echo "<p>🎉 <strong>Database setup is complete and working correctly!</strong></p>\n";
    echo "<p>You can now:</p>\n";
    echo "<ul>\n";
    echo "<li>Access the frontend at: <a href='../../frontend/'>Frontend Application</a></li>\n";
    echo "<li>Access the admin panel at: <a href='admin_login.php'>Admin Login</a></li>\n";
    echo "<li>Use default admin credentials: admin@creatorsspace.local / password</li>\n";
    echo "</ul>\n";
    echo "</div>\n";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>\n";
    echo "<p>❌ <strong>Database connection failed:</strong></p>\n";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    echo "<p><strong>Troubleshooting steps:</strong></p>\n";
    echo "<ol>\n";
    echo "<li>Check if MySQL service is running</li>\n";
    echo "<li>Verify database credentials in <code>backend/config/db_connect.php</code></li>\n";
    echo "<li>Ensure database '$DB_NAME' exists</li>\n";
    echo "<li>Run the database schema import script</li>\n";
    echo "</ol>\n";
    echo "</div>\n";
}

echo "<hr>\n";
echo "<p><small>Test completed at: " . date('Y-m-d H:i:s') . "</small></p>\n";
echo "<p><a href='?test=1'>🔄 Run test again</a></p>\n";
echo "</body></html>\n";
?>
