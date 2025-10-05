<?php
// backend/test/test_actual_signup.php
// Test the actual signup process with real data

session_start();

// Simulate POST data
$_POST = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'testuser' . time() . '@example.com', // Unique email with timestamp
    'username' => 'testuser' . time(),
    'password' => 'TestPass123',
    'confirm_password' => 'TestPass123',
    'terms' => 'on'
];

// Simulate POST method
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

echo "Testing signup with data:\n";
echo "Name: " . $_POST['first_name'] . " " . $_POST['last_name'] . "\n";
echo "Email: " . $_POST['email'] . "\n";
echo "Username: " . $_POST['username'] . "\n";
echo "Password: " . $_POST['password'] . "\n\n";

echo "Running signup process...\n\n";

// Capture output
ob_start();

// Include the actual signup process
try {
    include __DIR__ . '/../auth/signup_process.php';
} catch (Exception $e) {
    ob_end_clean();
    echo "Error occurred: " . $e->getMessage() . "\n";
} catch (Error $e) {
    ob_end_clean();
    echo "Fatal error occurred: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();
echo "Signup process output:\n";
echo $output . "\n";
?>