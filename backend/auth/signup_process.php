<?php
// backend/auth/signup_process.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set proper headers for JSON response
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Debug logging - remove this after testing
error_log("Signup process started. Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . json_encode($_POST));

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Rate limiting (temporarily disabled for testing - enable in production)
// if (!checkRateLimit('signup_' . $_SERVER['REMOTE_ADDR'])) {
//     errorResponse('Too many signup attempts. Please try again later.', 429);
// }

// Get and sanitize input
$firstName = sanitizeInput($_POST['first_name'] ?? '');
$lastName = sanitizeInput($_POST['last_name'] ?? '');
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validation
if (empty($firstName)) {
    errorResponse('First name is required');
}

if (!$email) {
    errorResponse('Valid email is required');
}

if (strlen($password) < 8) {
    errorResponse('Password must be at least 8 characters long');
}

if ($password !== $confirmPassword) {
    errorResponse('Passwords do not match');
}

// Password strength check
if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
    errorResponse('Password must contain at least one lowercase letter, one uppercase letter, and one number');
}

try {
    // Check if email already exists (excluding removed/inactive users)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND is_active = 1 AND (remove IS NULL OR remove = 0)");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        errorResponse('Email already registered');
    }
    
    // Check if username already exists (if provided, excluding removed/inactive users)
    $username = sanitizeInput($_POST['username'] ?? '');
    if (!empty($username)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND is_active = 1 AND (remove IS NULL OR remove = 0)");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            errorResponse('Username already taken');
        }
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, username, password_hash, role, is_active, remove) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    // Log signup attempt
    error_log("Signup attempt for email: " . $email . ", username: " . ($username ?: 'null'));
    
    $result = $stmt->execute([
        $firstName,
        $lastName,
        $email,
        $username ?: null,
        $passwordHash,
        'user',
        1,  // is_active = 1 (active user)
        0   // remove = 0 (not removed)
    ]);

    if (!$result) {
        error_log("INSERT failed: " . json_encode($stmt->errorInfo()));
        errorResponse('Failed to create account. Please try again.', 500);
    }

    $userId = $pdo->lastInsertId();
    error_log("User created successfully with ID: " . $userId);
    
    // Log activity
    logActivity($userId, 'signup', "New user registered: $email");

    successResponse('Account created successfully! You can now log in.');

} catch (PDOException $e) {
    error_log("Signup error: " . $e->getMessage());
    errorResponse('Registration failed. Please try again.', 500);
}
?>
