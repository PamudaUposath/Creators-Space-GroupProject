<?php
// backend/add_test_user.php
// Script to add a test user: test@gmail.com with password: 12345678

require_once 'config/db_connect.php';

try {
    // Check if user already exists
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute(['test@gmail.com']);
    
    if ($checkStmt->rowCount() > 0) {
        echo "User test@gmail.com already exists!\n";
        exit;
    }
    
    // Hash the password
    $password = '12345678';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert the test user
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, username, password_hash, role, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([
        'Test',           // first_name
        'User',           // last_name
        'test@gmail.com', // email
        'testuser',       // username
        $hashedPassword,  // password_hash
        'user',           // role
        1                 // is_active
    ]);
    
    if ($result) {
        echo "✅ Test user created successfully!\n";
        echo "Email: test@gmail.com\n";
        echo "Password: 12345678\n";
        echo "Username: testuser\n";
        echo "Role: user\n";
    } else {
        echo "❌ Failed to create test user\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>