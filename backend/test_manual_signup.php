<?php
require_once 'config/db_connect.php';

// Test manual user insertion using PDO (same as signup_process.php)
echo "<h2>Manual Signup Test (PDO)</h2>";

// Test data
$testUser = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'username' => 'testuser_' . time(), // Unique username
    'email' => 'test_' . time() . '@example.com', // Unique email
    'password' => 'Test123!'
];

echo "<p>Testing with data:</p>";
echo "<pre>" . print_r($testUser, true) . "</pre>";

try {
    // Database connection (using same PDO connection as signup_process.php)
    if (!isset($pdo)) {
        throw new Exception("PDO connection not available");
    }
    echo "<p>✓ PDO Database connection available</p>";

    // Check if email exists (same query as signup_process.php)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1 AND (remove IS NULL OR remove = 0)");
    $stmt->execute([$testUser['email']]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "<p>❌ Email already exists</p>";
    } else {
        echo "<p>✓ Email is available</p>";
    }

    // Check if username exists (same query as signup_process.php)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND is_active = 1 AND (remove IS NULL OR remove = 0)");
    $stmt->execute([$testUser['username']]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "<p>❌ Username already exists</p>";
    } else {
        echo "<p>✓ Username is available</p>";
    }

    // Hash password (same as signup_process.php)
    $hashedPassword = password_hash($testUser['password'], PASSWORD_DEFAULT);
    echo "<p>✓ Password hashed: " . substr($hashedPassword, 0, 20) . "...</p>";

    // Insert user (exact same query as signup_process.php)
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, username, password_hash, role, is_active, remove) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    echo "<p>Executing INSERT statement (same as signup_process.php)...</p>";
    
    $result = $stmt->execute([
        $testUser['first_name'],
        $testUser['last_name'],
        $testUser['email'],
        $testUser['username'],
        $hashedPassword,
        'user',
        1,  // is_active = 1
        0   // remove = 0
    ]);
    
    if ($result) {
        $newUserId = $pdo->lastInsertId();
        echo "<p>✅ <strong>SUCCESS!</strong> User inserted with ID: $newUserId</p>";
        
        // Verify the insertion
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$newUserId]);
        $insertedUser = $stmt->fetch();
        
        echo "<p>Verification - User data:</p>";
        echo "<pre>" . print_r($insertedUser, true) . "</pre>";
        
        // Clean up - delete the test user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$newUserId])) {
            echo "<p>✓ Test user cleaned up successfully</p>";
        }
        
    } else {
        echo "<p>❌ <strong>INSERT FAILED!</strong></p>";
        $errorInfo = $stmt->errorInfo();
        echo "<p>SQL Error Code: " . $errorInfo[1] . "</p>";
        echo "<p>SQL Error Message: " . $errorInfo[2] . "</p>";
    }

} catch (Exception $e) {
    echo "<p>❌ <strong>EXCEPTION:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Stack Trace:</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='../frontend/test_signup_debug.html'>← Back to Debug Page</a></p>";
echo "<p><a href='auth/test_db.php'>Test Database Connection</a></p>";
?>