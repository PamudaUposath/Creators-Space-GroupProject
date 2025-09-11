<?php
// backend/auth/logout.php

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../lib/helpers.php';

// Check if user was an admin before destroying session
$wasAdmin = isAdmin();

// Log the logout activity if user is logged in
if (isLoggedIn()) {
    logActivity($_SESSION['user_id'], 'logout', 'User logged out');
}

// Clear remember me cookie if it exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Destroy session
session_unset();
session_destroy();

// Start new session to show logout message
session_start();
session_regenerate_id(true);

$_SESSION['message'] = 'You have been logged out successfully.';

// Redirect based on user type
if ($wasAdmin) {
    // Redirect admin to admin login page
    header('Location: ../public/admin_login.php');
} else {
    // Redirect regular users to frontend
    header('Location: ../../frontend/index.php');
}
exit;
?>
