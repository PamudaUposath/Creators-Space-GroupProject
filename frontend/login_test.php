<?php
session_start();
require_once '../backend/config/db_connect.php';

echo "<h1>Login Test</h1>";

// Test login
if (isset($_POST['login'])) {
    $email = 'test@gmail.com';
    $password = 'password123';
    
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, username, password_hash, role FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            echo "<p style='color: green;'>Login successful!</p>";
        } else {
            echo "<p style='color: red;'>Login failed!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

// Show current session
echo "<h2>Current Session:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Show login form
if (!isset($_SESSION['user_id'])) {
    echo "<form method='POST'>";
    echo "<button type='submit' name='login'>Login as Test User</button>";
    echo "</form>";
} else {
    echo "<p><a href='cart.php'>Go to Cart</a></p>";
    echo "<p><a href='course-detail.php?id=1'>Go to Course Detail</a></p>";
    echo "<p><a href='?logout=1'>Logout</a></p>";
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login_test.php');
    exit();
}
?>