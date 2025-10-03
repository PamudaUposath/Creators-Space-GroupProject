<?php
// backend/test_login.php - Simple login test script

require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/lib/helpers.php';

// Test credentials (from sample data)
$testEmail = 'alice.johnson@example.com';
$testPassword = 'password123';

echo "<h1>Login Test Script</h1>\n";

// Check if user exists
try {
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, username, password_hash, role, is_active 
        FROM users 
        WHERE email = ? AND (remove IS NULL OR remove = 0)
        LIMIT 1
    ");
    $stmt->execute([$testEmail]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "<p style='color: red;'>❌ User not found with email: $testEmail</p>\n";
        echo "<p>Please run the sample data script first: <code>php backend/add_sample_data.php</code></p>\n";
        exit;
    }

    echo "<p style='color: green;'>✅ User found: {$user['first_name']} {$user['last_name']} ({$user['email']})</p>\n";
    
    // Test password verification
    if (password_verify($testPassword, $user['password_hash'])) {
        echo "<p style='color: green;'>✅ Password verification successful</p>\n";
    } else {
        echo "<p style='color: red;'>❌ Password verification failed</p>\n";
    }
    
    // Check if user is active
    if ($user['is_active']) {
        echo "<p style='color: green;'>✅ User account is active</p>\n";
    } else {
        echo "<p style='color: red;'>❌ User account is inactive</p>\n";
    }
    
    echo "<h2>Test Results</h2>\n";
    echo "<p>All checks passed! Login should work with:</p>\n";
    echo "<ul>\n";
    echo "<li><strong>Email:</strong> $testEmail</li>\n";
    echo "<li><strong>Password:</strong> $testPassword</li>\n";
    echo "</ul>\n";
    
    echo "<h2>Available Test Users</h2>\n";
    $stmt = $pdo->prepare("SELECT email, first_name, last_name, role FROM users WHERE is_active = 1 ORDER BY role, first_name");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
    echo "<tr><th>Email</th><th>Name</th><th>Role</th></tr>\n";
    foreach ($users as $u) {
        echo "<tr><td>{$u['email']}</td><td>{$u['first_name']} {$u['last_name']}</td><td>{$u['role']}</td></tr>\n";
    }
    echo "</table>\n";
    echo "<p><em>Password for all test users: password123</em></p>\n";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>