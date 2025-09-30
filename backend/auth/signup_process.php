<?php
// backend/auth/signup_process.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the request method and data
error_log("Signup request received. Method: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Set proper headers
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("Invalid request method: " . $_SERVER['REQUEST_METHOD']);
    errorResponse('Method not allowed', 405);
}

// Rate limiting
if (!checkRateLimit('signup_' . $_SERVER['REMOTE_ADDR'])) {
    errorResponse('Too many signup attempts. Please try again later.', 429);
}

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
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        errorResponse('Email already registered');
    }
    
    // Check if username already exists (if provided)
    $username = sanitizeInput($_POST['username'] ?? '');
    if (!empty($username)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            errorResponse('Username already taken');
        }
    }

    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, username, password_hash, role) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $firstName,
        $lastName,
        $email,
        $username ?: null,
        $passwordHash,
        'user'
    ]);

    $userId = $pdo->lastInsertId();
    
    // Log activity
    logActivity($userId, 'signup', "New user registered: $email");

    successResponse('Account created successfully! You can now log in.');

} catch (PDOException $e) {
    error_log("Signup error: " . $e->getMessage());
    errorResponse('Registration failed. Please try again.', 500);
}
?>
