<?php
// backend/test_pamuda_login.php - Test specific user login

require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/lib/helpers.php';

$email = 'pamuda@mailinator.com';
$password = 'Pamuda123';

echo "<h2>Testing Login for: $email</h2>";

// Check if user exists
echo "<h3>1. Checking if user exists...</h3>";
try {
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, username, password_hash, role, is_active, remove
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "✅ User found in database<br>";
        echo "User ID: {$user['id']}<br>";
        echo "Name: {$user['first_name']} {$user['last_name']}<br>";
        echo "Username: {$user['username']}<br>";
        echo "Role: {$user['role']}<br>";
        echo "Active: " . ($user['is_active'] ? 'Yes' : 'No') . "<br>";
        echo "Removed: " . ($user['remove'] ? 'Yes' : 'No') . "<br>";
        echo "Password Hash: " . substr($user['password_hash'], 0, 30) . "...<br>";
        
        // Test password
        echo "<h3>2. Testing password verification...</h3>";
        if (password_verify($password, $user['password_hash'])) {
            echo "✅ Password verification successful<br>";
        } else {
            echo "❌ Password verification failed<br>";
            
            // Test common variations
            $variations = [
                'pamuda123',
                'PAMUDA123', 
                'Pamuda123!',
                'pamuda@123',
                '123456',
                'password'
            ];
            
            echo "<h4>Testing password variations:</h4>";
            foreach ($variations as $testPass) {
                if (password_verify($testPass, $user['password_hash'])) {
                    echo "✅ Correct password is: <strong>$testPass</strong><br>";
                    break;
                }
            }
        }
        
        // Check if user meets login criteria
        echo "<h3>3. Checking login eligibility...</h3>";
        if (!$user['is_active']) {
            echo "❌ User account is not active<br>";
        } else {
            echo "✅ User account is active<br>";
        }
        
        if ($user['remove']) {
            echo "❌ User account is marked as removed<br>";
        } else {
            echo "✅ User account is not removed<br>";
        }
        
    } else {
        echo "❌ User not found with email: $email<br>";
        
        // Search for similar emails
        echo "<h4>Searching for similar emails:</h4>";
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email LIKE ?");
        $stmt->execute(['%pamuda%']);
        $similar = $stmt->fetchAll();
        
        if ($similar) {
            foreach ($similar as $row) {
                echo "Found similar: {$row['email']}<br>";
            }
        } else {
            echo "No similar emails found<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test the actual login process
if ($user && password_verify($password, $user['password_hash'])) {
    echo "<h3>4. Testing full login process...</h3>";
    
    // Simulate the login request
    $_POST = ['email' => $email, 'password' => $password];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    
    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    ob_start();
    try {
        // Test rate limiting
        if (!checkRateLimit('login_' . $_SERVER['REMOTE_ADDR'])) {
            echo "❌ Rate limit exceeded<br>";
        } else {
            echo "✅ Rate limit OK<br>";
        }
        
        // Simulate successful login session setup
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in_at'] = time();
        
        echo "✅ Session variables set successfully<br>";
        echo "Session user_id: {$_SESSION['user_id']}<br>";
        echo "Session role: {$_SESSION['role']}<br>";
        
    } catch (Exception $e) {
        echo "❌ Login process error: " . $e->getMessage() . "<br>";
    }
    $output = ob_get_clean();
    echo $output;
}

?>