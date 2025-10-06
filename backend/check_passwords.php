<?php
// backend/check_passwords.php - Check user passwords

require_once __DIR__ . '/config/db_connect.php';

// Get users and their password info
$stmt = $pdo->query("
    SELECT id, email, first_name, last_name, password_hash, role 
    FROM users 
    WHERE email IN ('admin@creatorsspace.local', 'test@gmail.com', 'instructor@creatorsspace.local')
    ORDER BY id
");

$users = $stmt->fetchAll();

echo "<h2>User Password Analysis</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>Hash Sample</th><th>Test Passwords</th></tr>";

$testPasswords = ['admin123', 'password123', 'test123', 'admin', '123456'];

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['first_name']} {$user['last_name']}</td>";
    echo "<td>{$user['role']}</td>";
    echo "<td>" . substr($user['password_hash'], 0, 20) . "...</td>";
    echo "<td>";
    
    $validPassword = null;
    foreach ($testPasswords as $testPass) {
        if (password_verify($testPass, $user['password_hash'])) {
            echo "<strong style='color: green;'>✅ $testPass</strong><br>";
            $validPassword = $testPass;
            break;
        }
    }
    
    if (!$validPassword) {
        echo "<span style='color: red;'>❌ None of the test passwords work</span>";
    }
    
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Also check if there are any users with simple passwords
echo "<h3>All Users Summary:</h3>";
$stmt = $pdo->query("SELECT COUNT(*) as total, COUNT(CASE WHEN is_active = 1 THEN 1 END) as active FROM users");
$summary = $stmt->fetch();
echo "Total users: {$summary['total']}, Active users: {$summary['active']}<br>";

?>