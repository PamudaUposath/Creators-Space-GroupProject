<?php
// backend/test_ajax_login.php - Test AJAX login exactly as frontend sends it

// Simulate the exact request the frontend makes
$_POST['email'] = 'pamuda@mailinator.com';
$_POST['password'] = 'Pamuda123';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_ORIGIN'] = 'http://localhost';

// Start output buffering to capture the JSON response
ob_start();

// Include the actual login process file
try {
    include __DIR__ . '/auth/login_process.php';
} catch (Exception $e) {
    // If there's an error, output it as JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

$response = ob_get_clean();

// Display the raw response
echo "=== RAW RESPONSE ===\n";
echo $response;
echo "\n=== END RESPONSE ===\n";

// Try to parse as JSON
echo "\n=== PARSED JSON ===\n";
$jsonData = json_decode($response, true);
if ($jsonData) {
    print_r($jsonData);
} else {
    echo "Response is not valid JSON!\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
}

?>