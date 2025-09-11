<?php
// backend/add_recent_users.php - Add more recent users for demo

require_once __DIR__ . '/config/db_connect.php';

$recentUsers = [
    [
        'first_name' => 'Sarah',
        'last_name' => 'Connor',
        'username' => 'sarah.connor',
        'email' => 'sarah.connor@example.com',
        'password' => 'password123',
        'role' => 'user',
        'days_ago' => 1
    ],
    [
        'first_name' => 'Mike',
        'last_name' => 'Johnson',
        'username' => 'mike.johnson',
        'email' => 'mike.johnson@example.com',
        'password' => 'password123',
        'role' => 'user',
        'days_ago' => 2
    ],
    [
        'first_name' => 'Lisa',
        'last_name' => 'Wang',
        'username' => 'lisa.wang',
        'email' => 'lisa.wang@example.com',
        'password' => 'password123',
        'role' => 'user',
        'days_ago' => 3
    ]
];

try {
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, username, email, password_hash, role, is_active, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, 1, DATE_SUB(NOW(), INTERVAL ? DAY))
    ");

    foreach ($recentUsers as $user) {
        // Check if user exists
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
                $user['role'],
                $user['days_ago']
            ]);
            echo "✅ Added recent user: {$user['first_name']} {$user['last_name']}\n";
        }
    }
    
    echo "🎉 Recent users added successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
