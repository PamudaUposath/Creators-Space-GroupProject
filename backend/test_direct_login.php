<?php
// backend/test_direct_login.php - Test login process directly

// Start session first
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simulate POST data
$_POST['email'] = 'admin@creatorsspace.local';
$_POST['password'] = 'admin123';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Capture output
ob_start();

try {
    // Include the login process
    include __DIR__ . '/auth/login_process.php';
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$output = ob_get_clean();
echo $output;
?>