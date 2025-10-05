<?php
// backend/test/test_signup.php
// Test script to verify signup process components

session_start();
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

echo "<h1>Signup Process Test</h1>\n";

// Test 1: Database Connection
echo "<h2>1. Database Connection Test</h2>\n";
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection successful<br>\n";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->fetch()) {
        echo "✅ Users table exists<br>\n";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE users");
        echo "📋 Users table structure:<br>\n";
        echo "<pre>";
        while ($row = $stmt->fetch()) {
            echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']}\n";
        }
        echo "</pre>";
    } else {
        echo "❌ Users table does not exist<br>\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>\n";
}

// Test 2: Helper Functions
echo "<h2>2. Helper Functions Test</h2>\n";
if (function_exists('sanitizeInput')) {
    echo "✅ sanitizeInput function exists<br>\n";
} else {
    echo "❌ sanitizeInput function missing<br>\n";
}

if (function_exists('successResponse')) {
    echo "✅ successResponse function exists<br>\n";
} else {
    echo "❌ successResponse function missing<br>\n";
}

if (function_exists('errorResponse')) {
    echo "✅ errorResponse function exists<br>\n";
} else {
    echo "❌ errorResponse function missing<br>\n";
}

if (function_exists('checkRateLimit')) {
    echo "✅ checkRateLimit function exists<br>\n";
} else {
    echo "❌ checkRateLimit function missing<br>\n";
}

// Test 3: Session
echo "<h2>3. Session Test</h2>\n";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "✅ Session is active<br>\n";
    echo "Session ID: " . session_id() . "<br>\n";
} else {
    echo "❌ Session is not active<br>\n";
}

// Test 4: Simulate signup data validation
echo "<h2>4. Data Validation Test</h2>\n";
$testData = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'password' => 'TestPass123',
    'confirm_password' => 'TestPass123'
];

echo "Testing with sample data:<br>\n";
foreach ($testData as $key => $value) {
    echo "- $key: $value<br>\n";
}

$firstName = sanitizeInput($testData['first_name']);
$email = filter_var($testData['email'], FILTER_VALIDATE_EMAIL);
$password = $testData['password'];

if (!empty($firstName)) {
    echo "✅ First name validation passed<br>\n";
} else {
    echo "❌ First name validation failed<br>\n";
}

if ($email) {
    echo "✅ Email validation passed<br>\n";
} else {
    echo "❌ Email validation failed<br>\n";
}

if (strlen($password) >= 8) {
    echo "✅ Password length validation passed<br>\n";
} else {
    echo "❌ Password length validation failed<br>\n";
}

if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
    echo "✅ Password strength validation passed<br>\n";
} else {
    echo "❌ Password strength validation failed<br>\n";
}

echo "<p><strong>Test completed!</strong></p>\n";
?>