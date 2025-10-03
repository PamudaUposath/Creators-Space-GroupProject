<?php
// backend/test_login_debug.php - Debug login issues

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Login Debug Test</h2>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection...</h3>";
try {
    require_once __DIR__ . '/config/db_connect.php';
    echo "✅ Database connection successful<br>";
    
    // Test if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Users table exists<br>";
        
        // Count users
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "📊 Total users in database: " . $result['count'] . "<br>";
        
        // Show first few users (for testing)
        $stmt = $pdo->query("SELECT id, email, first_name, last_name, role, is_active FROM users LIMIT 3");
        $users = $stmt->fetchAll();
        
        if (!empty($users)) {
            echo "<h4>Sample Users:</h4>";
            echo "<table border='1'>";
            echo "<tr><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>Active</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['first_name']} {$user['last_name']}</td>";
                echo "<td>{$user['role']}</td>";
                echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "❌ Users table does not exist<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 2: Check helper functions
echo "<h3>2. Testing Helper Functions...</h3>";
try {
    require_once __DIR__ . '/lib/helpers.php';
    echo "✅ Helper functions loaded<br>";
    
    // Test if functions exist
    if (function_exists('errorResponse')) {
        echo "✅ errorResponse function exists<br>";
    } else {
        echo "❌ errorResponse function missing<br>";
    }
    
    if (function_exists('successResponse')) {
        echo "✅ successResponse function exists<br>";
    } else {
        echo "❌ successResponse function missing<br>";
    }
    
    if (function_exists('checkRateLimit')) {
        echo "✅ checkRateLimit function exists<br>";
    } else {
        echo "❌ checkRateLimit function missing<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Helper functions failed to load: " . $e->getMessage() . "<br>";
}

// Test 3: Test login with sample data
echo "<h3>3. Testing Login Process...</h3>";

if (isset($_POST['test_email']) && isset($_POST['test_password'])) {
    echo "<h4>Processing login test...</h4>";
    
    $email = $_POST['test_email'];
    $password = $_POST['test_password'];
    
    echo "Testing with email: $email<br>";
    
    try {
        // Get user by email
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, username, password_hash, role, is_active 
            FROM users 
            WHERE email = ? AND (remove IS NULL OR remove = 0)
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "✅ User found in database<br>";
            echo "User details: ID={$user['id']}, Role={$user['role']}, Active=" . ($user['is_active'] ? 'Yes' : 'No') . "<br>";
            
            if (password_verify($password, $user['password_hash'])) {
                echo "✅ Password verification successful<br>";
                echo "🎉 Login would succeed for this user<br>";
            } else {
                echo "❌ Password verification failed<br>";
                echo "Stored hash: " . substr($user['password_hash'], 0, 20) . "...<br>";
            }
        } else {
            echo "❌ User not found with email: $email<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Login test failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "<form method='post'>";
    echo "<label>Email: <input type='email' name='test_email' value='alice.johnson@example.com'></label><br><br>";
    echo "<label>Password: <input type='text' name='test_password' value='password123'></label><br><br>";
    echo "<button type='submit'>Test Login</button>";
    echo "</form>";
}

// Test 4: Check session configuration
echo "<h3>4. Testing Session Configuration...</h3>";
session_start();
echo "✅ Session started<br>";
echo "Session ID: " . session_id() . "<br>";
echo "Session save path: " . session_save_path() . "<br>";

?>