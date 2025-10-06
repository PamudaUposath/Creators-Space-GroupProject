<?php
// Simple test to check what the API returns
session_start();

// Simulate being logged in (replace with your actual user ID)
if (!isset($_SESSION['user_id'])) {
    echo "Not logged in. Please log in first.";
    exit;
}

echo "<h2>Testing Profile API</h2>";

// Test GET request
echo "<h3>GET Request:</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/Creators-Space-GroupProject/backend/api/profile.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "<br>";
echo "Response: <pre>" . htmlspecialchars($response) . "</pre>";

// Check if it's valid JSON
$json = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Valid JSON: YES<br>";
    echo "Parsed: <pre>" . print_r($json, true) . "</pre>";
} else {
    echo "Valid JSON: NO<br>";
    echo "JSON Error: " . json_last_error_msg() . "<br>";
}
?>