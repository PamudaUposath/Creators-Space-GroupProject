<?php
require_once __DIR__ . '/config/db_connect.php';

try {
    // Test the connection
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "Database connection successful!\n";
    echo "Number of users in database: " . $userCount . "\n";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
?>