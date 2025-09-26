<?php
// backend/auth/login_process.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse('Method not allowed', 405);
}

// Rate limiting
if (!checkRateLimit('login_' . $_SERVER['REMOTE_ADDR'])) {
    errorResponse('Too many login attempts. Please try again later.', 429);
}

// Get and sanitize input
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']);

// Validation
if (!$email) {
    errorResponse('Valid email is required');
}

if (empty($password)) {
    errorResponse('Password is required');
}

try {
    // Get user by email
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, email, username, password_hash, role, is_active, profile_image 
        FROM users 
        WHERE email = ? 
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        // Log failed attempt
        logActivity($user['id'] ?? 0, 'failed_login', "Failed login attempt for: $email");
        errorResponse('Invalid email or password', 401);
    }

    if (!$user['is_active']) {
        errorResponse('Account is deactivated. Please contact support.', 403);
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['profile_image'] = $user['profile_image'];
    $_SESSION['logged_in_at'] = time();
    
    // Regenerate session ID to prevent session fixation after setting session data
    session_regenerate_id(true);

    // Handle remember me functionality
    if ($rememberMe) {
        $token = generateSecureToken();
        // In production, you would store this token in database and create a longer-lasting cookie
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true); // 30 days
    }

    // Log successful login
    logActivity($user['id'], 'login', "Successful login for: $email");

    // Determine redirect based on user role
    $redirectPath = 'index.php'; // Default for regular users
    
    switch ($user['role']) {
        case 'admin':
            $redirectPath = '/Creators-Space-GroupProject/backend/admin/dashboard.php';
            break;
        case 'instructor':
            // TODO: Create instructor dashboard page
            $redirectPath = 'index.php'; // Temporarily redirect to main page
            break;
        case 'user':
        default:
            $redirectPath = 'index.php';
            break;
    }

    // Return success with user data
    successResponse('Login successful', [
        'user' => [
            'id' => $user['id'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'username' => $user['username'],
            'role' => $user['role']
        ],
        'redirect' => $redirectPath
    ]);

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    errorResponse('Login failed. Please try again.', 500);
}
?>
