<?php
// backend/create_admin.php - Create admin user for testing

require_once __DIR__ . '/config/db_connect.php';

// Admin user details
$admin_email = 'admin@creators-space.com';
$admin_password = 'Admin123!';
$admin_first_name = 'Admin';
$admin_last_name = 'User';
$admin_username = 'admin';

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR role = 'admin'");
    $stmt->execute([$admin_email]);
    
    if ($stmt->fetch()) {
        echo "✅ Admin user already exists!\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
    } else {
        // Create admin user
        $password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, username, email, password_hash, role, is_active, created_at) 
            VALUES (?, ?, ?, ?, ?, 'admin', 1, NOW())
        ");
        
        $stmt->execute([
            $admin_first_name,
            $admin_last_name,
            $admin_username,
            $admin_email,
            $password_hash
        ]);
        
        echo "✅ Admin user created successfully!\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
        echo "Role: admin\n";
    }
    
    // Show current admin users
    echo "\n📋 Current Admin Users:\n";
    $stmt = $pdo->query("
        SELECT id, first_name, last_name, email, role, is_active, created_at 
        FROM users 
        WHERE role IN ('admin', 'instructor') 
        ORDER BY created_at DESC
    ");
    
    $admins = $stmt->fetchAll();
    
    if (empty($admins)) {
        echo "No admin users found.\n";
    } else {
        echo "ID | Name                | Email                      | Role       | Active | Created\n";
        echo "---|---------------------|----------------------------|------------|--------|---------\n";
        foreach ($admins as $admin) {
            printf(
                "%-3s| %-19s | %-26s | %-10s | %-6s | %s\n",
                $admin['id'],
                $admin['first_name'] . ' ' . $admin['last_name'],
                $admin['email'],
                $admin['role'],
                $admin['is_active'] ? 'Yes' : 'No',
                date('Y-m-d', strtotime($admin['created_at']))
            );
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
