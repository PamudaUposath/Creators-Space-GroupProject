<?php
// Test database connection and users table
require_once __DIR__ . '/../config/db_connect.php';

header('Content-Type: application/json');

try {
    // Test database connection
    $stmt = $pdo->query("SELECT 1");
    echo json_encode([
        'db_connection' => 'OK',
        'message' => 'Database connection successful'
    ]);
    
    // Check users table structure
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    
    echo "\n\nUsers table structure:\n";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . "\n";
    }
    
    // Count existing users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $count = $stmt->fetch();
    echo "\nTotal users in database: " . $count['count'] . "\n";
    
    // Test insert (but don't commit)
    $pdo->beginTransaction();
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    $testResult = $stmt->execute(['Test', 'User', 'test_' . time() . '@test.com', password_hash('test123', PASSWORD_DEFAULT), 'user']);
    $pdo->rollback(); // Rollback the test insert
    
    echo "\nTest insert result: " . ($testResult ? 'SUCCESS' : 'FAILED') . "\n";
    
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>